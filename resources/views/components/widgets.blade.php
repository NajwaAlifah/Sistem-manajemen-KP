@php
    $u = auth()->user(); $role = $u->role;
    $ann = \App\Models\Announcement::latest()->limit(3)->get();
    if ($role === 'mahasiswa') {
        $kp = $u->kp; $comp = \App\Support\Kp::completeness($u);
        $lbRev = $u->logbooks()->where('status', 'revisi')->count();
        $next = \App\Models\Guidance::where('user_id', $u->id)->whereIn('status', ['disetujui', 'jadwal_diubah'])->where('tanggal_final', '>=', now())->orderBy('tanggal_final')->first();
    } elseif ($role === 'dosen') {
        $nMhs = \App\Models\User::binaan($u)->count();
        $nBim = \App\Models\Guidance::where('dosen_id', $u->id)->where('status', 'diajukan')->count();
    }
@endphp
@if ($role === 'mahasiswa')
<div class="sk-tiles">
    <a href="{{ route('kp.pengajuan') }}"><i class="bi bi-send-fill"></i><strong>Pengajuan KP</strong>
        <span>{{ \App\Support\Kp::KP_STATUS[$kp->status ?? 'belum_mengajukan'] }}</span></a>
    <a href="{{ route('logbook.index') }}"><i class="bi bi-journal-text"></i><strong>Logbook</strong>
        <span>{{ $lbRev ? $lbRev . ' catatan perlu diperbaiki' : 'Catat kegiatan harian KP' }}</span></a>
    <a href="{{ route('bimbingan.index') }}"><i class="bi bi-chat-dots-fill"></i><strong>Bimbingan</strong>
        <span>{{ $next ? 'Berikutnya: ' . $next->tanggal_final->format('d M, H:i') : 'Ajukan jadwal bimbingan' }}</span></a>
    <a href="{{ route('kp.progress') }}"><i class="bi bi-graph-up-arrow"></i><strong>Progress KP</strong>
        <span>Dokumen wajib {{ $comp['done'] }}/{{ $comp['total'] }} ({{ $comp['percent'] }}%)</span></a>
</div>
@elseif ($role === 'dosen')
<div class="sk-tiles">
    <a href="{{ route('monitoring.index') }}"><i class="bi bi-people-fill"></i><strong>Mahasiswa Bimbingan</strong><span>{{ $nMhs }} mahasiswa</span></a>
    <a href="{{ route('bimbingan.index') }}"><i class="bi bi-chat-dots-fill"></i><strong>Bimbingan</strong><span>{{ $nBim }} menunggu konfirmasi</span></a>
    <a href="{{ route('nilai.index') }}"><i class="bi bi-award-fill"></i><strong>Penilaian</strong><span>Isi nilai dosen dan seminar</span></a>
</div>
@endif
@if ($ann->count())
<div class="sk-annc">
    <div class="sk-annc-head"><i class="bi bi-megaphone-fill"></i> Pengumuman <a href="{{ route('announcements.index') }}">Lihat semua</a></div>
    @foreach ($ann as $a)
        <div class="sk-annc-item"><strong>{{ $a->title }}</strong><span>{{ $a->created_at->format('d M Y') }}</span><p>{{ \Illuminate\Support\Str::limit($a->body, 140) }}</p></div>
    @endforeach
</div>
@endif
