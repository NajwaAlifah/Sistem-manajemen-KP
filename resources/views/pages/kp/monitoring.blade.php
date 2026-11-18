@extends('layouts.app')
@section('title', 'Monitoring KP - Manajemen KP')
@section('page-title', 'Monitoring KP')
@section('page-subtitle', 'Status KP, kelengkapan dokumen, dan logbook mahasiswa')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('header-actions')
@if (auth()->user()->role === 'admin')
    <a class="btn btn-secondary" href="{{ route('admin.export', 'kp') }}"><i class="bi bi-file-earmark-spreadsheet"></i> Export Data KP</a>
    <a class="btn btn-secondary" href="{{ route('admin.export', 'dokumen') }}"><i class="bi bi-file-earmark-spreadsheet"></i> Export Dokumen</a>
    <a class="btn btn-secondary" href="{{ route('admin.export', 'nilai') }}"><i class="bi bi-file-earmark-spreadsheet"></i> Export Nilai</a>
@endif
@endsection
@section('content')
@php use App\Support\Kp; $me = auth()->user(); @endphp
<form method="GET" class="sk-inline mb-3">
    <div><label>Cari</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Nama atau NPM"></div>
    <div><label>Status KP</label><select class="form-control" name="status"><option value="">Semua</option>
        @foreach (Kp::KP_STATUS as $k => $v)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>@endforeach</select></div>
    <button class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
</form>
<div class="documents-section">
    <div class="section-header"><h3 class="section-title"><i class="bi bi-activity"></i> Mahasiswa <small class="text-muted">({{ $rows->count() }})</small></h3></div>
    <div class="table-responsive-wrapper"><table class="documents-table" style="min-width:900px">
        <thead><tr><th>Mahasiswa</th><th>Perusahaan</th><th>Status KP</th><th style="width:170px">Kelengkapan</th><th>Logbook</th><th>Aksi</th></tr></thead>
        <tbody>@forelse ($rows as $x)
            @php $m = $x['m']; $kp = $m->kp; @endphp
            <tr><td><strong>{{ $m->name }}</strong><div class="text-muted small">{{ $m->nim }}@if ($me->role === 'admin') · {{ $m->dosen->name ?? 'tanpa dosen' }}@endif</div></td>
                <td>{{ $kp->perusahaan ?? '-' }}@if ($kp?->dikonfirmasi_at)<div class="text-success small"><i class="bi bi-patch-check-fill"></i> Dikonfirmasi</div>@endif</td>
                <td><span class="badge badge-{{ Kp::KP_BADGE[$kp->status ?? 'belum_mengajukan'] }}">{{ Kp::KP_STATUS[$kp->status ?? 'belum_mengajukan'] }}</span></td>
                <td><div class="sk-bar"><span style="width:{{ $x['c']['percent'] }}%"></span></div><small class="text-muted">{{ $x['c']['done'] }}/{{ $x['c']['total'] }} dokumen · {{ $x['c']['percent'] }}%</small></td>
                <td>{{ $x['vl'] }}/{{ $m->logbooks_count }} <small class="text-muted">tervalidasi</small></td>
                <td><div class="doc-actions">
                    <a class="doc-action-btn" title="Logbook" href="{{ route('logbook.index', ['mahasiswa' => $m->id]) }}"><i class="bi bi-journal-text"></i></a>
                    @if ($me->role !== 'lapangan')<a class="doc-action-btn" title="Dokumen" href="{{ route('archive.index', ['q' => $m->name]) }}"><i class="bi bi-folder2-open"></i></a>@endif
                    @if ($me->role === 'lapangan' && $kp && !$kp->dikonfirmasi_at && $kp->status !== 'menunggu_persetujuan')
                        <form method="POST" action="{{ route('monitoring.konfirmasi', $m->id) }}" onsubmit="return confirm('Konfirmasi bahwa {{ e(addslashes($m->name)) }} melaksanakan KP di perusahaan Anda?')">@csrf
                            <button class="btn btn-primary btn-sm"><i class="bi bi-patch-check"></i> Konfirmasi</button></form>
                    @endif
                </div></td></tr>
        @empty<tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-people"></i></div><div class="empty-state-title">Tidak ada mahasiswa</div></div></td></tr>@endforelse</tbody>
    </table></div>
</div>
@endsection
