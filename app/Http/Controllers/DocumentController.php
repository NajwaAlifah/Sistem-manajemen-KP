<?php
namespace App\Http\Controllers;

use App\Models\{Category, Document, DocumentVersion, Review};
use App\Support\Kp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    private const MIMES = 'pdf,doc,docx'; // format file: butir TBD SRS (Appendix C), diatur di sini

    private function exts(): string { return Kp::setting('allowed_ext', self::MIMES); }

    private function find(Request $r, $id, bool $with = true): Document
    {
        $doc = Document::with('owner', 'category', 'versions', 'reviews.author')->findOrFail($id);
        abort_unless($doc->canBeSeenBy($r->user()), 403, 'Anda tidak berwenang mengakses dokumen ini.');
        return $doc;
    }

    // REQ-ARSIP-01/02/03/05 : pencarian, filter, arsip
    public function index(Request $r)
    {
        $q = Document::with('owner', 'category', 'versions', 'reviews.author')->visibleTo($r->user());

        if ($s = $r->status) {
            $s === 'archive' ? $q->whereIn('status', ['approved', 'archived']) : $q->where('status', $s);
        }
        if ($c = $r->category) $q->whereHas('category', fn ($x) => $x->where('slug', $c));
        if ($s = $r->search) {
            $q->where(fn ($x) => $x->where('name', 'like', "%$s%")
                ->orWhereHas('owner', fn ($o) => $o->where('name', 'like', "%$s%")));
        }
        if ($r->date_from) $q->whereDate('created_at', '>=', $r->date_from);
        if ($r->date_to)   $q->whereDate('created_at', '<=', $r->date_to);

        $sort = in_array($r->sort_by, ['created_at', 'updated_at', 'name']) ? $r->sort_by : 'updated_at';
        $p = $q->orderBy($sort, $r->order === 'asc' ? 'asc' : 'desc')->paginate(min(max((int) $r->input('limit', 10), 5), 100));

        return response()->json([
            'success' => true,
            'data' => collect($p->items())->map(fn ($x) => $x->toApi())->values(),
            'pagination' => ['current_page' => $p->currentPage(), 'total_pages' => $p->lastPage(), 'total' => $p->total(), 'per_page' => $p->perPage()],
        ]);
    }

    // REQ-DOC-01/02/03/08
    public function store(Request $r)
    {
        abort_unless($r->user()->role === 'mahasiswa', 403, 'Hanya mahasiswa yang dapat mengunggah dokumen.');
        $r->validate([
            'category' => 'required|exists:categories,slug',
            'description' => 'nullable|string|max:500',
            'file' => 'required|file|mimes:' . self::MIMES,
        ], ['file.mimes' => 'Format file harus: ' . strtoupper(str_replace(',', ', ', $this->exts())), 'category.exists' => 'Kategori tidak valid']);

        $cat = Category::where('slug', $r->category)->where('is_active', true)->firstOrFail();
        $this->checkSize($r, $cat);

        $doc = Document::create([
            'user_id' => $r->user()->id, 'category_id' => $cat->id,
            'name' => $r->file('file')->getClientOriginalName(), 'description' => $r->description, 'status' => 'draft',
        ]);
        $this->saveVersion($r, $doc, 1);
        Kp::audit('dokumen_unggah', $doc->name);

        return response()->json(['success' => true, 'message' => 'Dokumen berhasil diunggah',
            'document' => $doc->fresh(['owner', 'category', 'versions', 'reviews.author'])->toApi()], 201);
    }

    public function show(Request $r, $id)
    {
        $doc = $this->find($r, $id);
        // REQ-REVIEW-04: dosen membuka dokumen Diajukan -> Diperiksa
        if ($r->user()->role === 'dosen' && $doc->status === 'submitted') {
            $doc->update(['status' => 'reviewed']);
            $doc->refresh();
        }
        return response()->json(['success' => true, 'document' => $doc->toApi(true)]);
    }

    public function versions(Request $r, $id)
    {
        return response()->json(['success' => true, 'versions' => $this->find($r, $id)->toApi(true)['versions']]);
    }

    // REQ-DOC-05 : ubah metadata / ganti file (hanya Draft atau Revisi, BR-06)
    public function update(Request $r, $id)
    {
        $doc = $this->ownedEditable($r, $id);
        $r->validate(['description' => 'nullable|string|max:500', 'name' => 'nullable|string|max:255', 'file' => 'nullable|file|mimes:' . self::MIMES]);
        $doc->fill($r->only('name', 'description'));

        if ($r->hasFile('file')) {
            $this->checkSize($r, $doc->category);
            $doc->current_version++;
            $doc->save();
            $this->saveVersion($r, $doc, $doc->current_version); // REQ-DOC-06
            Kp::audit('dokumen_versi_baru', $doc->name . ' v' . $doc->current_version);
        }
        $doc->save();
        return response()->json(['success' => true, 'message' => 'Dokumen berhasil diperbarui', 'document' => $doc->fresh(['owner', 'category', 'versions', 'reviews.author'])->toApi()]);
    }

    // REQ-DOC-07 + Safety: hanya pemilik (Draft) atau admin
    public function destroy(Request $r, $id)
    {
        $doc = $this->find($r, $id);
        $u = $r->user();
        abort_unless($u->role === 'admin' || ($doc->user_id === $u->id && $doc->status === 'draft'), 403, 'Dokumen ini tidak dapat dihapus.');
        foreach ($doc->versions as $v) Storage::delete($v->path);
        Kp::audit('dokumen_hapus', $doc->name);
        $doc->delete();
        return response()->json(['success' => true, 'message' => 'Dokumen berhasil dihapus']);
    }

    // Mahasiswa: Draft/Revisi -> Diajukan (BR-11)
    public function submit(Request $r, $id)
    {
        $doc = $this->ownedEditable($r, $id);
        $doc->update(['status' => 'submitted', 'submitted_at' => now()]);
        Kp::notify($doc->owner->dosen_id, 'Dokumen menunggu pemeriksaan', $doc->owner->name . ' · ' . $doc->name, route('documents.show', $doc->id));
        Kp::audit('dokumen_ajukan', $doc->name);
        return response()->json(['success' => true, 'message' => 'Dokumen berhasil diajukan untuk pemeriksaan', 'document' => ['id' => $doc->id, 'status' => 'submitted']]);
    }

    // REQ-REVIEW-03/05/06 : catatan, revisi, persetujuan
    public function feedback(Request $r, $id)
    {
        $data = $r->validate(['type' => 'required|in:revision,approved,rejected', 'message' => 'nullable|string|max:2000']);
        if ($data['type'] !== 'approved' && blank($data['message'] ?? null)) {
            return response()->json(['success' => false, 'message' => 'Catatan wajib diisi untuk permintaan revisi.'], 422);
        }
        $doc = $this->reviewable($r, $id);
        $status = $data['type'] === 'approved' ? 'approved' : 'revision';

        $doc->update($status === 'approved'
            ? ['status' => 'approved', 'approved_at' => now(), 'approved_by' => $r->user()->id]
            : ['status' => 'revision']);
        $rev = Review::create(['document_id' => $doc->id, 'user_id' => $r->user()->id, 'version' => $doc->current_version,
            'type' => $data['type'], 'message' => $data['message'] ?? null, 'status_after' => $status]);

        Kp::notify($doc->user_id, $status === 'approved' ? 'Dokumen disetujui' : 'Dokumen perlu revisi', $doc->name . ($rev->message ? ': ' . $rev->message : ''), route('documents.show', $doc->id));
        Kp::audit('dokumen_review', $doc->name . ' ' . $status);
        return response()->json(['success' => true, 'feedback' => ['id' => $rev->id, 'document_id' => $doc->id, 'type' => $rev->type,
            'message' => $rev->message, 'given_by' => ['id' => $r->user()->id, 'name' => $r->user()->name, 'email' => $r->user()->email],
            'created_at' => $rev->created_at->toIso8601String()], 'status' => $status], 201);
    }

    public function approve(Request $r, $id)
    {
        $r->merge(['type' => 'approved']);
        return $this->feedback($r, $id);
    }

    // Unduh lewat controller agar hak akses dicek (REQ-DOC-04, Security Req.)
    public function download(Request $r, $id)
    {
        $doc = $this->find($r, $id);
        $v = $doc->versions->firstWhere('version', (int) ($r->v ?: $doc->current_version));
        abort_unless($v && Storage::exists($v->path), 404, 'Berkas tidak ditemukan.');
        if ($r->boolean('inline') && $v->mime === 'application/pdf') {
            return Storage::response($v->path, $v->original_name, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="' . addslashes($v->original_name) . '"']);
        }
        return Storage::download($v->path, $v->original_name);
    }

    /* ---------- helpers ---------- */
    private function ownedEditable(Request $r, $id): Document
    {
        $doc = $this->find($r, $id);
        abort_unless($doc->user_id === $r->user()->id, 403, 'Bukan dokumen Anda.');
        abort_unless(in_array($doc->status, ['draft', 'revision']), 422, 'Dokumen hanya dapat diubah saat berstatus Draft atau Revisi.');
        return $doc;
    }

    private function reviewable(Request $r, $id): Document
    {
        $doc = $this->find($r, $id);
        abort_unless($r->user()->role === 'dosen', 403, 'Hanya Dosen Pembimbing yang dapat memeriksa dokumen.'); // BR-09
        abort_unless(in_array($doc->status, ['submitted', 'reviewed']), 422, 'Dokumen belum diajukan atau sudah diputuskan.');
        return $doc;
    }

    private function checkSize(Request $r, Category $cat): void
    {
        abort_if($r->file('file')->getSize() > $cat->max_file_size * 1048576, 422, "File terlalu besar (max {$cat->max_file_size}MB)");
    }

    private function saveVersion(Request $r, Document $doc, int $n): void
    {
        $f = $r->file('file');
        DocumentVersion::create([
            'document_id' => $doc->id, 'version' => $n,
            'path' => $f->store("documents/{$doc->user_id}"), // disk private (bukan public)
            'original_name' => $f->getClientOriginalName(), 'mime' => $f->getMimeType(), 'size' => $f->getSize(),
            'status_at_upload' => $doc->status, 'uploaded_by' => $r->user()->id,
        ]);
    }
}
