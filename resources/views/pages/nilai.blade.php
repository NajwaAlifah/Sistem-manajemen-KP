@extends('layouts.app')
@section('title', 'Penilaian - Manajemen KP')
@section('page-title', 'Penilaian')
@section('page-subtitle', 'Nilai dosen pembimbing, pembimbing lapangan, dan nilai akhir')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('content')
@php $me = auth()->user(); $K = \App\Models\Grade::KOMPONEN; @endphp

@if ($me->role === 'mahasiswa')
    @php $m = $students->first(); $pub = $m?->kp?->nilai_published; @endphp
    <div class="sk-panel"><div class="sk-panel-body">
    @if ($pub)
        <div class="row g-3 text-center">
            @foreach ($K as $k => $label)<div class="col-sm-4"><div class="text-muted small text-uppercase">{{ $label }}</div><div class="sk-score">{{ isset($m->byKomponen[$k]) ? rtrim(rtrim(number_format($m->byKomponen[$k]->nilai, 2), '0'), '.') : '-' }}</div></div>@endforeach
        </div><hr>
        <div class="text-center"><div class="text-muted small text-uppercase">Nilai Akhir</div><div class="sk-score" style="font-size:46px">{{ $m->final ?? '-' }}</div>
            <span class="badge badge-approved" style="font-size:16px">{{ \App\Support\Kp::huruf($m->final) }}</span></div>
    @else
        <div class="empty-state"><div class="empty-state-icon"><i class="bi bi-hourglass-split"></i></div><div class="empty-state-title">Nilai belum dipublikasikan</div>
            <div class="empty-state-text">Nilai akan tampil setelah dipublikasikan oleh admin prodi.</div></div>
    @endif
    </div></div>
@else
    @if ($me->role === 'admin')
    <div class="sk-panel"><div class="section-header"><h3 class="section-title"><i class="bi bi-sliders"></i> Formula Nilai Akhir</h3></div>
        <form method="POST" action="{{ route('nilai.bobot') }}" class="sk-panel-body sk-inline">@csrf
            @foreach ($K as $k => $label)<div><label>{{ $label }} (%)</label><input type="number" class="form-control" name="bobot_{{ $k }}" value="{{ $bobot[$k] }}" min="0" max="100" step="1" style="max-width:130px"></div>@endforeach
            <button class="btn btn-primary">Simpan</button><small class="text-muted">Total harus 100%</small></form></div>
    @endif
    <div class="documents-section">
        <div class="section-header"><h3 class="section-title"><i class="bi bi-award-fill"></i> Daftar Nilai Mahasiswa</h3></div>
        <div class="table-responsive-wrapper"><table class="documents-table" style="min-width:820px">
            <thead><tr><th>Mahasiswa</th>@foreach ($K as $label)<th>{{ $label }}</th>@endforeach<th>Akhir</th>@if ($me->role === 'admin')<th>Publikasi</th>@endif</tr></thead>
            <tbody>@forelse ($students as $s)
                <tr><td><strong>{{ $s->name }}</strong><div class="text-muted small">{{ $s->nim }}</div></td>
                @foreach ($K as $k => $label)
                    @php $can = ($me->role === 'lapangan' && $k === 'lapangan') || ($me->role === 'dosen' && $k !== 'lapangan'); $g = $s->byKomponen[$k] ?? null; @endphp
                    <td>@if ($can && !$s->kp?->nilai_published)
                        <form method="POST" action="{{ route('nilai.store') }}" class="sk-inline">@csrf<input type="hidden" name="user_id" value="{{ $s->id }}"><input type="hidden" name="komponen" value="{{ $k }}">
                            <input type="number" class="form-control form-control-sm" name="nilai" value="{{ $g?->nilai }}" min="0" max="100" step="0.01" style="width:90px" required>
                            <button class="btn btn-primary btn-sm" title="Simpan"><i class="bi bi-check-lg"></i></button></form>
                        @else {{ $g ? rtrim(rtrim(number_format($g->nilai, 2), '0'), '.') : '-' }} @endif</td>
                @endforeach
                <td><strong>{{ $s->final ?? '-' }}</strong> <span class="text-muted">{{ \App\Support\Kp::huruf($s->final) }}</span></td>
                @if ($me->role === 'admin')<td>@if ($s->kp)<form method="POST" action="{{ route('nilai.publikasi', $s->id) }}">@csrf
                    <button class="btn btn-sm {{ $s->kp->nilai_published ? 'btn-secondary' : 'btn-primary' }}">{{ $s->kp->nilai_published ? 'Tarik' : 'Publikasikan' }}</button></form>@else<span class="text-muted small">Belum ada data KP</span>@endif</td>@endif</tr>
            @empty<tr><td colspan="6"><div class="empty-state"><div class="empty-state-title">Belum ada mahasiswa</div></div></td></tr>@endforelse</tbody>
        </table></div>
    </div>
    @if ($me->role !== 'admin')<p class="text-muted small mt-2">Komponen nilai yang bisa Anda isi: {{ $me->role === 'lapangan' ? 'Pembimbing Lapangan' : 'Dosen Pembimbing dan Seminar / Presentasi' }}. Nilai dikunci setelah dipublikasikan admin prodi.</p>@endif
@endif
@endsection
