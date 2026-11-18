@extends('layouts.app')
@section('title', 'Audit Log - Manajemen KP')
@section('page-title', 'Audit Log')
@section('page-subtitle', 'Jejak aktivitas penting pengguna')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('content')
<form method="GET" class="sk-inline mb-3"><div><label>Cari</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Aksi, detail, atau nama"></div>
    <button class="btn btn-primary"><i class="bi bi-search"></i> Cari</button><a class="btn btn-secondary" href="{{ route('admin.audit') }}">Reset</a></form>
<div class="documents-section">
    <div class="table-responsive-wrapper"><table class="documents-table" style="min-width:700px">
        <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Detail</th><th>IP</th></tr></thead>
        <tbody>@forelse ($logs as $l)
            <tr><td class="doc-date">{{ $l->created_at->format('d M Y H:i:s') }}</td><td>{{ $l->user->name ?? '-' }}<div class="text-muted small">{{ $l->user?->role_label }}</div></td>
                <td><span class="doc-category">{{ $l->action }}</span></td><td>{{ $l->detail }}</td><td class="text-muted small">{{ $l->ip }}</td></tr>
        @empty<tr><td colspan="5"><div class="empty-state"><div class="empty-state-title">Belum ada aktivitas</div></div></td></tr>@endforelse</tbody></table></div>
    <div class="pagination-container"><div class="pagination-info">{{ $logs->total() }} catatan</div>{{ $logs->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
