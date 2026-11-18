@extends('layouts.app')
@section('title', 'Pengumuman - Manajemen KP')
@section('page-title', 'Pengumuman')
@section('page-subtitle', 'Informasi umum, jadwal, dan perubahan prosedur KP')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('header-actions')@if (auth()->user()->role === 'admin')<button class="btn btn-primary" onclick="openAnn()"><i class="bi bi-plus-circle-fill"></i> Buat Pengumuman</button>@endif @endsection
@section('content')
@php $admin = auth()->user()->role === 'admin'; @endphp
@forelse ($items as $a)
<div class="sk-panel"><div class="sk-panel-body">
    <div class="d-flex justify-content-between gap-2 flex-wrap"><h4 class="mb-1">{{ $a->title }}</h4>
        @if ($admin)<div class="doc-actions">
            <button class="doc-action-btn" data-item="{{ json_encode($a->only(['id', 'title', 'body'])) }}" onclick="openAnn(JSON.parse(this.dataset.item))"><i class="bi bi-pencil-square"></i></button>
            <button class="doc-action-btn delete" onclick="del('/announcements/{{ $a->id }}','pengumuman ini')"><i class="bi bi-trash3"></i></button></div>@endif</div>
    <div class="text-muted small mb-2">{{ $a->created_at->format('d M Y, H:i') }} · {{ $a->author->name ?? 'Admin' }}</div>
    <div style="white-space:pre-line">{{ $a->body }}</div>
</div></div>
@empty
<div class="documents-section"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-megaphone"></i></div><div class="empty-state-title">Belum ada pengumuman</div></div></div>
@endforelse
{{ $items->links('pagination::bootstrap-5') }}
@endsection
@section('modals')
@if (auth()->user()->role === 'admin')
<div class="modal fade" id="aModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="aTitleH">Buat Pengumuman</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><input type="hidden" id="aId"><div class="form-group"><label class="form-label">Judul</label><input class="form-control" id="aTitle" maxlength="150"></div>
        <div class="form-group"><label class="form-label">Isi</label><textarea class="form-control" id="aBody" rows="6" maxlength="3000"></textarea></div></div>
    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" onclick="saveAnn(this)">Simpan</button></div>
</div></div></div>
@endif
@endsection
@section('extra-js')@if (auth()->user()->role === 'admin')<script src="{{ asset('js/kp-pages.js') }}"></script><script src="{{ asset('js/admin-pages.js') }}"></script>@endif @endsection
