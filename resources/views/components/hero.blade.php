@php
    $u = auth()->user();
    $fak = 'Fakultas Ilmu Komputer dan Sistem Informasi';
    $sub2 = match ($u->role) { 'mahasiswa' => 'Sistem Informasi', 'lapangan' => $u->company->name ?? 'Pembimbing Lapangan', default => $u->role_label };
    $kp = $u->role === 'mahasiswa' ? $u->kp : null;
@endphp
<section class="sk-hero">
    <div class="sk-photo"><span>{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span></div>
    <div class="sk-nim">{{ $u->nim ?: ($u->nip ?: $u->role_label) }}</div>
    <div class="sk-name">{{ $u->name }}</div>
    <div class="sk-sub">{{ $u->role === 'lapangan' ? 'Pembimbing Lapangan' : $fak }}</div>
    <div class="sk-sub">{{ $sub2 }}</div>
    @if ($u->role === 'mahasiswa' && ($kp || $u->dosen || $u->lapangan))
        <div class="sk-chips">
            @if ($kp)<span><i class="bi bi-building"></i> {{ $kp->perusahaan }}</span>@endif
            @if ($u->dosen)<span><i class="bi bi-person-check"></i> Dosen: {{ $u->dosen->name }}</span>@endif
            @if ($u->lapangan)<span><i class="bi bi-person-badge"></i> Lapangan: {{ $u->lapangan->name }}</span>@endif
        </div>
    @endif
</section>
