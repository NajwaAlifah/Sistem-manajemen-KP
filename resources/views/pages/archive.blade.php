@extends('layouts.app')
@section('title', 'Pencarian & Arsip - Manajemen KP')
@section('page-title', 'Pencarian & Arsip')
@section('page-subtitle', 'Cari dokumen dan telusuri arsip digital (dokumen Disetujui)')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection

@section('content')
@php($st = \App\Models\Document::STATUS)
<div class="documents-section">
    <form method="GET" class="filter-bar">
        <div><label>Kata kunci</label>
            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Nama dokumen atau nama mahasiswa"></div>
        <div><label>Kategori</label>
            <select class="form-control" name="category"><option value="">Semua</option>
                @foreach ($categories as $c)<option value="{{ $c->slug }}" @selected(request('category') === $c->slug)>{{ $c->name }}</option>@endforeach
            </select></div>
        <div><label>Status</label>
            <select class="form-control" name="status"><option value="">Semua</option>
                <option value="archive" @selected(request('status') === 'archive')>Arsip Digital</option>
                @foreach ($st as $k => $v)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>@endforeach
            </select></div>
        <div><label>Dari tanggal</label><input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}"></div>
        <div><label>Sampai</label><input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}"></div>
        <div class="action-bar">
            <button class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
            <a class="btn btn-secondary" href="{{ route('archive.index') }}">Reset</a>
        </div>
    </form>

    <div class="table-responsive-wrapper">
        <table class="documents-table">
            <thead><tr><th>Nama Dokumen</th><th>Mahasiswa</th><th>Kategori</th><th>Status</th><th>Versi</th><th>Diperbarui</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse ($docs as $d)
                <tr class="clickable" onclick="location='{{ route('documents.show', $d->id) }}'">
                    <td><div class="doc-name">{{ $d->name }}</div></td>
                    <td>{{ $d->owner->name }}</td>
                    <td><span class="doc-category">{{ $d->category->name }}</span></td>
                    <td><span class="badge badge-{{ $d->status }}">{{ $st[$d->status] }}</span></td>
                    <td class="text-muted">v{{ $d->current_version }}.0</td>
                    <td class="doc-date">{{ $d->updated_at->format('d M Y') }}</td>
                    <td onclick="event.stopPropagation()"><div class="doc-actions">
                        <a class="doc-action-btn" title="Lihat" href="{{ route('documents.show', $d->id) }}"><i class="bi bi-eye"></i></a>
                        <a class="doc-action-btn" title="Unduh" href="{{ route('documents.download', $d->id) }}"><i class="bi bi-download"></i></a>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state">
                    <div class="empty-state-icon"><i class="bi bi-search"></i></div>
                    <div class="empty-state-title">Dokumen tidak ditemukan</div>
                    <div class="empty-state-text">Coba ubah kata kunci atau filter pencarian Anda.</div>
                </div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-container">
        <div class="pagination-info">Menampilkan {{ $docs->count() }} dari {{ $docs->total() }} dokumen</div>
        {{ $docs->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
