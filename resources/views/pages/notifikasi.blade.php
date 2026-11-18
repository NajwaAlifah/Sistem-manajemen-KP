@extends('layouts.app')
@section('title', 'Notifikasi - Manajemen KP')
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Informasi dokumen, revisi, bimbingan, dan aktivitas penting')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('header-actions')
    <form method="POST" action="{{ route('notifications.readall') }}">@csrf<button class="btn btn-secondary"><i class="bi bi-check2-all"></i> Tandai semua dibaca</button></form>
@endsection
@section('content')
<div class="documents-section">
    @forelse ($items as $n)
        <a href="{{ route('notifications.go', $n->id) }}" class="d-flex gap-3 p-3 text-decoration-none" style="border-bottom:1px solid #f0f0ed;color:inherit;{{ $n->read_at ? '' : 'background:#fcf7f7' }}">
            <span class="sk-step-dot" style="{{ $n->read_at ? '' : 'background:#8a1c24;color:#fff' }}"><i class="bi bi-bell-fill"></i></span>
            <div><strong>{{ $n->title }}</strong>@if ($n->body)<div class="text-muted">{{ $n->body }}</div>@endif<small class="text-muted">{{ $n->created_at->diffForHumans() }}</small></div>
        </a>
    @empty
        <div class="empty-state"><div class="empty-state-icon"><i class="bi bi-bell-slash"></i></div><div class="empty-state-title">Belum ada notifikasi</div></div>
    @endforelse
    <div class="pagination-container"><div class="pagination-info">{{ $items->total() }} notifikasi</div>{{ $items->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
