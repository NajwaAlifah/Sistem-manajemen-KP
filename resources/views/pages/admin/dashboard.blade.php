@extends('layouts.app')
@section('title', 'Dashboard Admin Prodi - Manajemen KP')
@section('page-title', 'Dashboard Admin Prodi')
@section('page-subtitle', 'Ringkasan pelaksanaan KP dan dokumen')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('hero')@include('components.hero')@include('components.widgets')@endsection

@section('content')
@php use App\Support\Kp; $st = \App\Models\Document::STATUS; @endphp
<div class="status-cards-grid">
    <a href="{{ route('admin.users', ['role' => 'mahasiswa']) }}" class="status-card archived"><div class="status-card-icon"><i class="bi bi-mortarboard-fill"></i></div><div class="status-card-count">{{ $counts['mahasiswa'] }}</div><div class="status-card-label">Mahasiswa</div></a>
    <a href="{{ route('admin.users', ['role' => 'dosen']) }}" class="status-card submitted"><div class="status-card-icon"><i class="bi bi-person-workspace"></i></div><div class="status-card-count">{{ $counts['dosen'] }}</div><div class="status-card-label">Dosen Pembimbing</div></a>
    <a href="{{ route('admin.users', ['role' => 'lapangan']) }}" class="status-card draft"><div class="status-card-icon"><i class="bi bi-person-badge-fill"></i></div><div class="status-card-count">{{ $counts['lapangan'] }}</div><div class="status-card-label">Pembimbing Lapangan</div></a>
    <a href="{{ route('admin.companies') }}" class="status-card approved"><div class="status-card-icon"><i class="bi bi-building-fill"></i></div><div class="status-card-count">{{ $counts['perusahaan'] }}</div><div class="status-card-label">Perusahaan</div></a>
</div>

@if ($pengajuan)<div class="sk-note mb-3"><i class="bi bi-bell-fill"></i> Ada <strong>{{ $pengajuan }}</strong> pengajuan KP menunggu persetujuan. <a href="{{ route('admin.kp') }}">Tinjau sekarang</a></div>@endif

<div class="row g-3 mb-3">
    <div class="col-lg-6"><div class="sk-panel h-100"><div class="section-header"><h3 class="section-title"><i class="bi bi-briefcase-fill"></i> Status KP</h3></div><div class="sk-panel-body">
        @foreach (Kp::KP_STATUS as $k => $v)<div class="d-flex justify-content-between py-1"><span><span class="badge badge-{{ Kp::KP_BADGE[$k] }}">{{ $v }}</span></span><strong>{{ $kpStatus[$k] ?? 0 }}</strong></div>@endforeach
    </div></div></div>
    <div class="col-lg-6"><div class="sk-panel h-100"><div class="section-header"><h3 class="section-title"><i class="bi bi-files"></i> Status Dokumen</h3></div><div class="sk-panel-body">
        @foreach ($st as $k => $v)<div class="d-flex justify-content-between py-1"><span class="badge badge-{{ $k }}">{{ $v }}</span><strong>{{ $byStatus[$k] ?? 0 }}</strong></div>@endforeach
    </div></div></div>
</div>

<div class="documents-section">
    <div class="section-header"><h3 class="section-title"><i class="bi bi-clock-history"></i> Dokumen Terbaru</h3><a class="btn btn-secondary btn-sm" href="{{ route('archive.index') }}">Lihat semua</a></div>
    <div class="table-responsive-wrapper"><table class="documents-table">
        <thead><tr><th>Dokumen</th><th>Mahasiswa</th><th>Kategori</th><th>Status</th><th>Diperbarui</th></tr></thead>
        <tbody>@forelse ($recent as $d)
            <tr class="clickable" onclick="location='{{ route('documents.show', $d->id) }}'"><td><div class="doc-name">{{ $d->name }}</div></td><td>{{ $d->owner->name }}</td>
                <td>{{ $d->category->name }}</td><td><span class="badge badge-{{ $d->status }}">{{ $st[$d->status] }}</span></td><td class="doc-date">{{ $d->updated_at->format('d M Y') }}</td></tr>
        @empty<tr><td colspan="5"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-inbox"></i></div><div class="empty-state-title">Belum ada dokumen</div></div></td></tr>@endforelse</tbody></table></div>
</div>
@endsection
