<?php
namespace App\Http\Controllers;

use App\Models\{Grade, User};
use App\Support\Kp;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $r)
    {
        $u = $r->user();
        $students = $u->role === 'mahasiswa' ? User::whereKey($u->id) : User::binaan($u);
        $students = $students->with('grades', 'kp')->orderBy('name')->get()->each(function ($m) {
            $m->setAttribute('final', Kp::finalGrade($m));
            $m->setAttribute('byKomponen', $m->grades->keyBy('komponen'));
        });
        return view('pages.nilai', ['students' => $students,
            'bobot' => ['dosen' => Kp::setting('bobot_dosen', 40), 'lapangan' => Kp::setting('bobot_lapangan', 40), 'seminar' => Kp::setting('bobot_seminar', 20)]]);
    }

    // Dosen: komponen dosen & seminar; Pembimbing lapangan: komponen lapangan (sesuai kewenangan)
    public function store(Request $r)
    {
        $d = $r->validate(['user_id' => 'required|exists:users,id', 'komponen' => 'required|in:dosen,lapangan,seminar', 'nilai' => 'required|numeric|min:0|max:100', 'catatan' => 'nullable|string|max:1000']);
        $role = $r->user()->role;
        $allowed = $role === 'lapangan' ? ['lapangan'] : ['dosen', 'seminar'];
        abort_unless(in_array($d['komponen'], $allowed), 403, 'Anda tidak berwenang mengisi komponen nilai ini.');
        $m = User::binaan($r->user())->findOrFail($d['user_id']);
        abort_if($m->kp?->nilai_published, 422, 'Nilai sudah dipublikasikan; hubungi admin prodi untuk perubahan.');
        Grade::updateOrCreate(['user_id' => $m->id, 'komponen' => $d['komponen']], ['nilai' => $d['nilai'], 'catatan' => $d['catatan'] ?? null, 'graded_by' => $r->user()->id]);
        Kp::audit('nilai_simpan', $m->name . ' ' . $d['komponen'] . '=' . $d['nilai']);
        return back()->with('success', 'Nilai ' . $m->name . ' disimpan.');
    }

    // Admin: publikasi / tarik nilai & atur bobot
    public function publish(Request $r, $id)
    {
        $m = User::where('role', 'mahasiswa')->with('kp')->findOrFail($id);
        abort_unless($m->kp, 422, 'Mahasiswa belum memiliki data KP.');
        $m->kp->update(['nilai_published' => !$m->kp->nilai_published]);
        if ($m->kp->nilai_published) Kp::notify($m->id, 'Nilai KP dipublikasikan', 'Nilai akhir Anda sudah dapat dilihat.', route('nilai.index'));
        Kp::audit('nilai_publikasi', $m->name . ' ' . ($m->kp->nilai_published ? 'ya' : 'tarik'));
        return back()->with('success', 'Status publikasi nilai ' . $m->name . ' diperbarui.');
    }

    public function bobot(Request $r)
    {
        $d = $r->validate(['bobot_dosen' => 'required|numeric|min:0|max:100', 'bobot_lapangan' => 'required|numeric|min:0|max:100', 'bobot_seminar' => 'required|numeric|min:0|max:100']);
        abort_unless(abs(array_sum($d) - 100) < 0.01, 422, 'Total bobot harus 100%.');
        foreach ($d as $k => $v) Kp::setSetting($k, $v);
        Kp::audit('bobot_nilai', json_encode($d));
        return back()->with('success', 'Formula nilai akhir disimpan.');
    }
}
