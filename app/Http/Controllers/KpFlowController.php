<?php
namespace App\Http\Controllers;

use App\Models\{Company, KpData, Logbook, User};
use App\Support\Kp;
use Illuminate\Http\Request;

/** Pengajuan KP, progress, monitoring, konfirmasi pelaksanaan, export. */
class KpFlowController extends Controller
{
    public function pengajuan(Request $r)
    {
        return view('pages.kp.pengajuan', ['kp' => $r->user()->kp, 'companies' => Company::orderBy('name')->get()]);
    }

    public function storePengajuan(Request $r)
    {
        $kp = $r->user()->kp;
        abort_if($kp && in_array($kp->status, ['berlangsung', 'menunggu_penyelesaian', 'selesai']), 422, 'Pengajuan sudah disetujui dan tidak dapat diubah.');
        $d = $r->validate(['perusahaan' => 'required|string|max:150', 'company_id' => 'nullable|exists:companies,id', 'judul' => 'required|string|max:200',
            'tanggal_mulai' => 'required|date', 'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai']);
        if (!empty($d['company_id'])) $d['perusahaan'] = Company::find($d['company_id'])->name;
        KpData::updateOrCreate(['mahasiswa_id' => $r->user()->id], $d + ['status' => 'menunggu_persetujuan', 'catatan_admin' => null]);
        Kp::notify(Kp::admins(), 'Pengajuan KP baru', $r->user()->name . ' mengajukan KP di ' . $d['perusahaan'], route('admin.kp'));
        Kp::audit('pengajuan_kp', $d['perusahaan']);
        return back()->with('success', 'Pengajuan KP berhasil dikirim dan menunggu persetujuan prodi.');
    }

    public function progress(Request $r)
    {
        $u = $r->user(); $kp = $u->kp; $c = Kp::completeness($u);
        $lb = $u->logbooks()->selectRaw("count(*) as t, sum(case when status = 'disetujui' then 1 else 0 end) as v")->first();
        $steps = [
            ['Pengajuan KP', $kp && $kp->status !== 'ditolak', $kp ? Kp::KP_STATUS[$kp->status] : 'Belum mengajukan'],
            ['Pelaksanaan KP di perusahaan', $kp && in_array($kp->status, ['berlangsung', 'menunggu_penyelesaian', 'selesai']), $kp?->dikonfirmasi_at ? 'Dikonfirmasi pembimbing lapangan' : 'Menunggu persetujuan prodi'],
            ['Logbook kegiatan', (int) $lb->v > 0, (int) $lb->t . ' catatan, ' . (int) $lb->v . ' tervalidasi'],
            ['Kelengkapan dokumen', $c['total'] > 0 && $c['percent'] === 100, $c['done'] . ' dari ' . $c['total'] . ' dokumen wajib disetujui'],
            ['Penilaian dipublikasikan', (bool) $kp?->nilai_published, $kp?->nilai_published ? 'Nilai tersedia' : 'Belum dipublikasikan'],
            ['KP selesai', $kp?->status === 'selesai', $kp?->status === 'selesai' ? 'Selesai' : 'Belum selesai'],
        ];
        return view('pages.kp.progress', compact('kp', 'c', 'steps'));
    }

    // Monitoring KP: dosen (bimbingan), lapangan (binaan), admin (semua)
    public function monitoring(Request $r)
    {
        $list = User::binaan($r->user())->with('kp', 'dosen', 'lapangan')->withCount(['logbooks', 'documents'])
            ->when($r->q, fn ($q, $s) => $q->where(fn ($x) => $x->where('name', 'like', "%$s%")->orWhere('nim', 'like', "%$s%")))
            ->orderBy('name')->get();
        $rows = $list->map(fn ($m) => ['m' => $m, 'c' => Kp::completeness($m), 'vl' => Logbook::where('user_id', $m->id)->where('status', 'disetujui')->count()]);
        if ($r->status) $rows = $rows->filter(fn ($x) => ($x['m']->kp->status ?? 'belum_mengajukan') === $r->status);
        return view('pages.kp.monitoring', ['rows' => $rows->values()]);
    }

    // Pembimbing lapangan mengonfirmasi pelaksanaan KP
    public function konfirmasi(Request $r, $id)
    {
        $m = User::binaan($r->user())->with('kp')->findOrFail($id);
        abort_unless($m->kp && $m->kp->status !== 'menunggu_persetujuan', 422, 'KP mahasiswa belum disetujui prodi.');
        $m->kp->update(['dikonfirmasi_at' => now()]);
        Kp::notify([$m->id, $m->dosen_id], 'Pelaksanaan KP dikonfirmasi', $r->user()->name . ' mengonfirmasi pelaksanaan KP ' . $m->name, route('kp.progress'));
        Kp::audit('konfirmasi_kp', $m->name);
        return back()->with('success', 'Pelaksanaan KP ' . $m->name . ' dikonfirmasi.');
    }

    // Admin: export rekap (CSV, dapat dibuka di Excel)
    public function export(Request $r, string $type)
    {
        abort_unless(in_array($type, ['kp', 'dokumen', 'nilai']), 404);
        $rows = match ($type) {
            'kp' => [['NPM', 'Nama', 'Perusahaan', 'Judul', 'Mulai', 'Selesai', 'Status KP', 'Dosen Pembimbing', 'Pembimbing Lapangan'],
                ...\App\Models\KpData::with('mahasiswa.dosen', 'mahasiswa.lapangan')->get()->map(fn ($k) => [$k->mahasiswa->nim, $k->mahasiswa->name, $k->perusahaan, $k->judul,
                    $k->tanggal_mulai?->format('Y-m-d'), $k->tanggal_selesai?->format('Y-m-d'), Kp::KP_STATUS[$k->status] ?? $k->status, $k->mahasiswa->dosen->name ?? '', $k->mahasiswa->lapangan->name ?? ''])->all()],
            'dokumen' => [['Dokumen', 'Mahasiswa', 'NPM', 'Kategori', 'Status', 'Versi', 'Diunggah', 'Diperbarui'],
                ...\App\Models\Document::with('owner', 'category')->get()->map(fn ($d) => [$d->name, $d->owner->name, $d->owner->nim, $d->category->name,
                    \App\Models\Document::STATUS[$d->status], 'v' . $d->current_version . '.0', $d->created_at->format('Y-m-d'), $d->updated_at->format('Y-m-d')])->all()],
            'nilai' => [['NPM', 'Nama', 'Nilai Dosen', 'Nilai Lapangan', 'Nilai Seminar', 'Nilai Akhir', 'Huruf', 'Dipublikasikan'],
                ...User::where('role', 'mahasiswa')->with('grades', 'kp')->orderBy('name')->get()->map(function ($m) {
                    $g = $m->grades->pluck('nilai', 'komponen'); $f = Kp::finalGrade($m);
                    return [$m->nim, $m->name, $g['dosen'] ?? '', $g['lapangan'] ?? '', $g['seminar'] ?? '', $f ?? '', Kp::huruf($f), $m->kp?->nilai_published ? 'Ya' : 'Belum'];
                })->all()],
        };
        Kp::audit('export', $type);
        return response()->streamDownload(function () use ($rows) {
            $o = fopen('php://output', 'w'); fwrite($o, "\xEF\xBB\xBF");
            foreach ($rows as $row) fputcsv($o, $row, ';');
            fclose($o);
        }, 'rekap-' . $type . '-' . date('Ymd') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
