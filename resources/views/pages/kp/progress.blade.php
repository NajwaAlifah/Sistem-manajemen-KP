@extends('layouts.app')
@section('title', 'Progress KP - Manajemen KP')
@section('page-title', 'Progress KP')
@section('page-subtitle', 'Tahapan, kelengkapan dokumen, dan tenggat waktu')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('content')
@php $st = \App\Models\Document::STATUS; @endphp
<div class="sk-panel"><div class="sk-panel-body">
    <div class="d-flex justify-content-between mb-2"><strong>Kelengkapan dokumen wajib</strong><span>{{ $c['done'] }} dari {{ $c['total'] }} disetujui ({{ $c['percent'] }}%)</span></div>
    <div class="sk-bar"><span style="width:{{ $c['percent'] }}%"></span></div>
</div></div>

<div class="row g-3">
    <div class="col-lg-5"><div class="sk-panel h-100">
        <div class="section-header"><h3 class="section-title"><i class="bi bi-signpost-split-fill"></i> Tahapan KP</h3></div>
        <ol class="sk-steps sk-panel-body">
            @foreach ($steps as [$label, $done, $info])
                <li class="{{ $done ? 'done' : '' }}"><span class="sk-step-dot"><i class="bi {{ $done ? 'bi-check-lg' : 'bi-circle' }}"></i></span><div><strong>{{ $label }}</strong><small>{{ $info }}</small></div></li>
            @endforeach
        </ol>
    </div></div>
    <div class="col-lg-7"><div class="sk-panel h-100">
        <div class="section-header"><h3 class="section-title"><i class="bi bi-calendar-check-fill"></i> Dokumen &amp; Tenggat</h3></div>
        <div class="table-responsive-wrapper"><table class="documents-table" style="min-width:480px">
            <thead><tr><th>Dokumen</th><th>Tenggat</th><th>Status</th></tr></thead>
            <tbody>@forelse ($c['items'] as $i)
                <tr><td><strong>{{ $i['category']->name }}</strong></td>
                    <td>@if ($i['category']->deadline){{ $i['category']->deadline->format('d M Y') }}@if ($i['late']) <span class="badge badge-revision">Terlambat</span>@endif @else <span class="text-muted">-</span>@endif</td>
                    <td>@if ($i['doc_id'])<a href="{{ route('documents.show', $i['doc_id']) }}"><span class="badge badge-{{ $i['status'] }}">{{ $st[$i['status']] }}</span></a>
                        @else <span class="badge badge-draft">Belum dikumpulkan</span>@endif</td></tr>
            @empty<tr><td colspan="3" class="text-center text-muted py-4">Belum ada kategori wajib.</td></tr>@endforelse</tbody>
        </table></div>
    </div></div>
</div>
@endsection
