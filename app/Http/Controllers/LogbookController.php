<?php
namespace App\Http\Controllers;

use App\Models\{Logbook, User};
use App\Support\Kp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogbookController extends Controller
{
    public function index(Request $r)
    {
        $u = $r->user();
        $q = Logbook::with('owner', 'validator');
        if ($u->role === 'mahasiswa') $q->where('user_id', $u->id);
        else $q->whereIn('user_id', User::binaan($u)->select('id'));
        if ($r->mahasiswa && $u->role !== 'mahasiswa') $q->where('user_id', $r->mahasiswa);
        if ($r->status) $q->where('status', $r->status);
        elseif ($u->role === 'lapangan') $q->where('status', 'menunggu');
        return view('pages.logbook', ['logs' => $q->latest('tanggal')->latest('id')->paginate(15)->withQueryString(),
            'students' => $u->role === 'mahasiswa' ? collect() : User::binaan($u)->orderBy('name')->get(['id', 'name'])]);
    }

    public function store(Request $r)
    {
        $d = $r->validate(['tanggal' => 'required|date|before_or_equal:today', 'kegiatan' => 'required|string|max:2000', 'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'],
            ['file.mimes' => 'Dokumentasi harus JPG, PNG, atau PDF (maks. 5 MB).']);
        $log = new Logbook(['user_id' => $r->user()->id, 'tanggal' => $d['tanggal'], 'kegiatan' => $d['kegiatan']]);
        if ($f = $r->file('file')) { $log->file_path = $f->store('logbook/' . $r->user()->id); $log->file_name = $f->getClientOriginalName(); }
        $log->save();
        Kp::notify($r->user()->lapangan_id, 'Logbook baru menunggu validasi', $r->user()->name . ' · ' . $log->tanggal->format('d M Y'), route('logbook.index'));
        Kp::audit('logbook_tambah', $log->tanggal->format('Y-m-d'));
        return back()->with('success', 'Kegiatan berhasil dicatat.');
    }

    public function update(Request $r, $id)
    {
        $log = Logbook::where('user_id', $r->user()->id)->findOrFail($id);
        abort_unless(in_array($log->status, ['menunggu', 'revisi']), 422, 'Logbook yang sudah disetujui tidak dapat diubah.');
        $d = $r->validate(['tanggal' => 'required|date|before_or_equal:today', 'kegiatan' => 'required|string|max:2000']);
        $log->update($d + ['status' => 'menunggu']);
        Kp::notify($r->user()->lapangan_id, 'Logbook diperbarui', $r->user()->name . ' · ' . $log->tanggal->format('d M Y'), route('logbook.index'));
        return back()->with('success', 'Logbook diperbarui dan menunggu validasi ulang.');
    }

    public function destroy(Request $r, $id)
    {
        $log = Logbook::where('user_id', $r->user()->id)->where('status', 'menunggu')->findOrFail($id);
        if ($log->file_path) Storage::delete($log->file_path);
        $log->delete();
        return back()->with('success', 'Catatan dihapus.');
    }

    // Validasi oleh pembimbing lapangan
    public function validateLog(Request $r, $id)
    {
        $d = $r->validate(['aksi' => 'required|in:setuju,revisi', 'catatan' => 'nullable|string|max:1000']);
        abort_if($d['aksi'] === 'revisi' && blank($d['catatan'] ?? null), 422, 'Catatan wajib diisi untuk permintaan perbaikan.');
        $log = Logbook::whereIn('user_id', User::binaan($r->user())->select('id'))->findOrFail($id);
        $log->update(['status' => $d['aksi'] === 'setuju' ? 'disetujui' : 'revisi', 'catatan' => $d['catatan'] ?? null, 'validated_by' => $r->user()->id, 'validated_at' => now()]);
        Kp::notify($log->user_id, $d['aksi'] === 'setuju' ? 'Logbook disetujui' : 'Logbook perlu diperbaiki', $log->tanggal->format('d M Y') . ($log->catatan ? ': ' . $log->catatan : ''), route('logbook.index'));
        Kp::audit('logbook_validasi', $log->id . ' ' . $d['aksi']);
        return back()->with('success', 'Logbook ' . ($d['aksi'] === 'setuju' ? 'disetujui.' : 'dikembalikan untuk diperbaiki.'));
    }

    public function file(Request $r, $id)
    {
        $log = Logbook::findOrFail($id);
        $ok = $r->user()->role === 'admin' || $log->user_id === $r->user()->id || User::binaan($r->user())->whereKey($log->user_id)->exists();
        abort_unless($ok && $log->file_path && Storage::exists($log->file_path), 404);
        return Storage::download($log->file_path, $log->file_name);
    }
}
