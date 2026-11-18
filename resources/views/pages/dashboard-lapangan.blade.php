@extends('layouts.app')
@section('title', 'Dashboard Pembimbing Lapangan - Manajemen KP')
@section('page-title', 'Dashboard Pembimbing Lapangan')
@section('page-subtitle', 'Pantau mahasiswa, validasi logbook, dan beri penilaian')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('hero')@include('components.hero')@include('components.widgets')@endsection
@section('content')
@php use App\Support\Kp; $bd = ['menunggu' => 'submitted', 'disetujui' => 'approved', 'revisi' => 'revision']; @endphp
<div class="status-cards-grid">
    <a href="{{ route('monitoring.index') }}" class="status-card archived"><div class="status-card-icon"><i class="bi bi-people-fill"></i></div><div class="status-card-count">{{ $mhs->count() }}</div><div class="status-card-label">Mahasiswa Dibimbing</div></a>
    <a href="{{ route('logbook.index') }}" class="status-card revision"><div class="status-card-icon"><i class="bi bi-journal-check"></i></div><div class="status-card-count">{{ $pending }}</div><div class="status-card-label">Logbook Menunggu Validasi</div></a>
    <a href="{{ route('logbook.index', ['status' => 'disetujui']) }}" class="status-card approved"><div class="status-card-icon"><i class="bi bi-check2-circle"></i></div><div class="status-card-count">{{ $validated }}</div><div class="status-card-label">Logbook Tervalidasi</div></a>
</div>
<div class="row g-3">
    <div class="col-lg-6"><div class="documents-section h-100"><div class="section-header"><h3 class="section-title"><i class="bi bi-people-fill"></i> Mahasiswa</h3></div>
        <div class="table-responsive-wrapper"><table class="documents-table" style="min-width:380px"><tbody>
        @forelse ($mhs as $m)<tr><td><strong>{{ $m->name }}</strong><div class="text-muted small">{{ $m->nim }}</div></td>
            <td><span class="badge badge-{{ Kp::KP_BADGE[$m->kp->status ?? 'belum_mengajukan'] }}">{{ Kp::KP_STATUS[$m->kp->status ?? 'belum_mengajukan'] }}</span></td></tr>
        @empty<tr><td class="text-center text-muted py-4">Belum ada mahasiswa yang ditetapkan.</td></tr>@endforelse</tbody></table></div></div></div>
    <div class="col-lg-6"><div class="documents-section h-100"><div class="section-header"><h3 class="section-title"><i class="bi bi-journal-text"></i> Logbook Terbaru</h3></div>
        <div class="table-responsive-wrapper"><table class="documents-table" style="min-width:380px"><tbody>
        @forelse ($recent as $l)<tr><td><strong>{{ $l->owner->name }}</strong><div class="text-muted small">{{ $l->tanggal->format('d M Y') }} · {{ \Illuminate\Support\Str::limit($l->kegiatan, 50) }}</div></td>
            <td><span class="badge badge-{{ $bd[$l->status] }}">{{ \App\Models\Logbook::STATUS[$l->status] }}</span></td></tr>
        @empty<tr><td class="text-center text-muted py-4">Belum ada logbook.</td></tr>@endforelse</tbody></table></div></div></div>
</div>
@endsection
