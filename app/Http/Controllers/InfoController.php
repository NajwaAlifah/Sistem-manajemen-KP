<?php
namespace App\Http\Controllers;

use App\Models\{Announcement, AuditLog, Notif};
use App\Support\Kp;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function notifications(Request $r)
    {
        return view('pages.notifikasi', ['items' => Notif::where('user_id', $r->user()->id)->latest()->paginate(20)]);
    }

    public function go(Request $r, $id)
    {
        $n = Notif::where('user_id', $r->user()->id)->findOrFail($id);
        $n->update(['read_at' => $n->read_at ?? now()]);
        return redirect($n->url ?: route('notifications.index'));
    }

    public function readAll(Request $r)
    {
        Notif::where('user_id', $r->user()->id)->whereNull('read_at')->update(['read_at' => now()]);
        return back();
    }

    public function announcements()
    {
        return view('pages.pengumuman', ['items' => Announcement::with('author')->latest()->paginate(10)]);
    }

    public function storeAnnouncement(Request $r)
    {
        $d = $r->validate(['title' => 'required|string|max:150', 'body' => 'required|string|max:3000']);
        $a = Announcement::create($d + ['created_by' => $r->user()->id]);
        Kp::notify(\App\Models\User::where('is_active', true)->where('id', '!=', $r->user()->id)->pluck('id'), 'Pengumuman: ' . $a->title, null, route('announcements.index'));
        Kp::audit('pengumuman_buat', $a->title);
        return response()->json(['success' => true], 201);
    }

    public function updateAnnouncement(Request $r, Announcement $announcement)
    {
        $announcement->update($r->validate(['title' => 'required|string|max:150', 'body' => 'required|string|max:3000']));
        return response()->json(['success' => true]);
    }

    public function destroyAnnouncement(Announcement $announcement)
    {
        $announcement->delete();
        return response()->json(['success' => true]);
    }

    public function audit(Request $r)
    {
        $q = AuditLog::with('user')->when($r->q, fn ($x, $s) => $x->where(fn ($y) => $y->where('action', 'like', "%$s%")->orWhere('detail', 'like', "%$s%")
            ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%$s%"))));
        return view('pages.admin.audit', ['logs' => $q->latest('id')->paginate(30)->withQueryString()]);
    }

    public function settings()
    {
        return view('pages.admin.settings', ['ext' => Kp::setting('allowed_ext', 'pdf,doc,docx')]);
    }

    public function saveSettings(Request $r)
    {
        $d = $r->validate(['allowed_ext' => ['required', 'regex:/^[a-z0-9]+(,[a-z0-9]+)*$/i']], ['allowed_ext.regex' => 'Format pisahkan dengan koma, contoh: pdf,doc,docx']);
        Kp::setSetting('allowed_ext', strtolower($d['allowed_ext']));
        Kp::audit('pengaturan_file', $d['allowed_ext']);
        return back()->with('success', 'Pengaturan file disimpan.');
    }
}
