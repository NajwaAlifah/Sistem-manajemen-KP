<?php
namespace App\Http\Controllers;

use App\Models\{Category, Company, Document, KpData, Logbook, User};
use Illuminate\Http\Request;

/** Halaman server-rendered: Pencarian & Arsip, Detail Dokumen, Data Master. */
class PageController extends Controller
{
    // REQ-ARSIP-01..07 (filter hanya menampilkan dokumen sesuai hak akses)
    public function archive(Request $r)
    {
        $q = Document::with('owner', 'category')->visibleTo($r->user());
        if ($s = trim((string) $r->q)) {
            $q->where(fn ($x) => $x->where('name', 'like', "%$s%")->orWhereHas('owner', fn ($o) => $o->where('name', 'like', "%$s%")));
        }
        if ($r->category) $q->whereHas('category', fn ($x) => $x->where('slug', $r->category));
        if ($r->status === 'archive') $q->whereIn('status', ['approved', 'archived']);
        elseif ($r->status) $q->where('status', $r->status);
        if ($r->date_from) $q->whereDate('created_at', '>=', $r->date_from);
        if ($r->date_to) $q->whereDate('created_at', '<=', $r->date_to);

        return view('pages.archive', [
            'docs' => $q->latest('updated_at')->paginate(10)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function show(Request $r, $id)
    {
        $doc = Document::with('owner.dosen', 'category', 'versions', 'reviews.author')->findOrFail($id);
        abort_unless($doc->canBeSeenBy($r->user()), 403, 'Anda tidak berwenang mengakses dokumen ini.');
        if ($r->user()->role === 'dosen' && $doc->status === 'submitted') { // REQ-REVIEW-04
            $doc->update(['status' => 'reviewed']);
        }
        return view('pages.doc-show', ['doc' => $doc, 'tab' => $r->query('tab', 'versions')]);
    }

    public function admin()
    {
        return view('pages.admin.dashboard', [
            'byStatus' => Document::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status'),
            'kpStatus' => KpData::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status'),
            'counts' => ['mahasiswa' => User::where('role', 'mahasiswa')->count(), 'dosen' => User::where('role', 'dosen')->count(),
                'lapangan' => User::where('role', 'lapangan')->count(), 'perusahaan' => Company::count()],
            'pengajuan' => KpData::where('status', 'menunggu_persetujuan')->count(),
            'recent' => Document::with('owner', 'category')->latest('updated_at')->limit(6)->get(),
        ]);
    }

    public function lapangan(Request $r)
    {
        $ids = User::binaan($r->user())->select('id');
        return view('pages.dashboard-lapangan', [
            'mhs' => User::binaan($r->user())->with('kp')->orderBy('name')->get(),
            'pending' => Logbook::whereIn('user_id', $ids)->where('status', 'menunggu')->count(),
            'validated' => Logbook::whereIn('user_id', $ids)->where('status', 'disetujui')->count(),
            'recent' => Logbook::with('owner')->whereIn('user_id', $ids)->latest('id')->limit(6)->get(),
        ]);
    }

    public function adminUsers(Request $r)
    {
        $role = in_array($r->role, ['mahasiswa', 'dosen', 'lapangan', 'admin']) ? $r->role : null;
        return view('pages.admin.users', [
            'role' => $role,
            'users' => User::with('dosen', 'lapangan', 'company')->when($role, fn ($q) => $q->where('role', $role))->orderBy('role')->orderBy('name')->get(),
            'dosen' => User::where('role', 'dosen')->orderBy('name')->get(),
            'lapanganList' => User::where('role', 'lapangan')->orderBy('name')->get(),
            'companies' => Company::orderBy('name')->get(),
        ]);
    }

    public function adminCategories()
    {
        return view('pages.admin.categories', ['categories' => Category::withCount('documents')->orderBy('id')->get()]);
    }

    public function adminKp()
    {
        return view('pages.admin.kp', [
            'kps' => KpData::with('mahasiswa.dosen', 'mahasiswa.lapangan', 'company')->orderByRaw("case when status = 'menunggu_persetujuan' then 0 else 1 end")->latest()->get(),
            'mahasiswa' => User::where('role', 'mahasiswa')->orderBy('name')->get(),
            'dosen' => User::where('role', 'dosen')->orderBy('name')->get(),
            'lapanganList' => User::where('role', 'lapangan')->orderBy('name')->get(),
            'companies' => Company::orderBy('name')->get(),
        ]);
    }
}
