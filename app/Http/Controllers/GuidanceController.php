<?php
namespace App\Http\Controllers;

use App\Models\{Guidance, User};
use App\Support\Kp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuidanceController extends Controller
{
    public function index(Request $r)
    {
        $u = $r->user(); $q = Guidance::with('owner', 'dosen');
        if ($u->role === 'mahasiswa') $q->where('user_id', $u->id);
        elseif ($u->role === 'dosen') $q->where('dosen_id', $u->id);
        return view('pages.bimbingan', ['items' => $q->latest('tanggal_usul')->paginate(15)]);
    }

    public function store(Request $r)
    {
        $u = $r->user();
        abort_unless($u->dosen_id, 422, 'Dosen pembimbing Anda belum ditetapkan oleh prodi.');
        $d = $r->validate(['tanggal_usul' => 'required|date|after:now', 'metode' => 'required|in:daring,luring', 'topik' => 'required|string|max:200'],
            ['tanggal_usul.after' => 'Jadwal harus di masa depan.']);
        Guidance::create($d + ['user_id' => $u->id, 'dosen_id' => $u->dosen_id]);
        Kp::notify($u->dosen_id, 'Pengajuan jadwal bimbingan', $u->name . ': ' . $d['topik'], route('bimbingan.index'));
        Kp::audit('bimbingan_ajukan', $d['topik']);
        return back()->with('success', 'Jadwal bimbingan diajukan ke dosen pembimbing.');
    }

    // Dosen: konfirmasi / ubah jadwal / catatan hasil
    public function respond(Request $r, $id)
    {
        $g = Guidance::where('dosen_id', $r->user()->id)->findOrFail($id);
        $d = $r->validate(['aksi' => 'required|in:setuju,ubah,selesai', 'tanggal_final' => 'nullable|date', 'catatan_dosen' => 'nullable|string|max:3000']);
        if ($d['aksi'] === 'ubah' && blank($d['tanggal_final'] ?? null)) abort(422, 'Isi jadwal usulan baru.');
        if ($d['aksi'] === 'selesai' && blank($d['catatan_dosen'] ?? null)) abort(422, 'Catatan hasil bimbingan wajib diisi.');
        $g->update(match ($d['aksi']) {
            'setuju' => ['status' => 'disetujui', 'tanggal_final' => $g->tanggal_usul] + (!empty($d['catatan_dosen']) ? ['catatan_dosen' => $d['catatan_dosen']] : []),
            'ubah' => ['status' => 'jadwal_diubah', 'tanggal_final' => $d['tanggal_final'], 'catatan_dosen' => $d['catatan_dosen'] ?? $g->catatan_dosen],
            'selesai' => ['status' => 'selesai', 'catatan_dosen' => $d['catatan_dosen']],
        });
        Kp::notify($g->user_id, 'Bimbingan: ' . Guidance::STATUS[$g->status], $g->topik, route('bimbingan.index'));
        Kp::audit('bimbingan_respon', $g->id . ' ' . $d['aksi']);
        return back()->with('success', 'Tanggapan bimbingan disimpan.');
    }

    public function lampiran(Request $r, $id)
    {
        $g = Guidance::where('user_id', $r->user()->id)->findOrFail($id);
        $r->validate(['file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'], ['file.mimes' => 'Lampiran harus PDF, DOC, DOCX, JPG, atau PNG (maks. 10 MB).']);
        if ($g->lampiran_path) Storage::delete($g->lampiran_path);
        $f = $r->file('file');
        $g->update(['lampiran_path' => $f->store('bimbingan/' . $g->user_id), 'lampiran_name' => $f->getClientOriginalName()]);
        Kp::notify($g->dosen_id, 'Lampiran tindak lanjut bimbingan', $r->user()->name . ' · ' . $g->topik, route('bimbingan.index'));
        return back()->with('success', 'Lampiran diunggah.');
    }

    public function download(Request $r, $id)
    {
        $g = Guidance::findOrFail($id); $u = $r->user();
        abort_unless($u->role === 'admin' || $g->user_id === $u->id || $g->dosen_id === $u->id, 403);
        abort_unless($g->lampiran_path && Storage::exists($g->lampiran_path), 404);
        return Storage::download($g->lampiran_path, $g->lampiran_name);
    }
}
