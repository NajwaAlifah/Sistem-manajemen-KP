@extends('layouts.app')
@section('title', 'Perusahaan - Manajemen KP')
@section('page-title', 'Data Perusahaan')
@section('page-subtitle', 'Perusahaan tempat pelaksanaan KP')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('header-actions')<button class="btn btn-primary" onclick="openCo()"><i class="bi bi-plus-circle-fill"></i> Tambah Perusahaan</button>@endsection
@section('content')
<div class="documents-section">
    <div class="section-header"><h3 class="section-title"><i class="bi bi-building-fill"></i> Daftar Perusahaan <small class="text-muted">({{ $companies->count() }})</small></h3></div>
    <div class="table-responsive-wrapper"><table class="documents-table">
        <thead><tr><th>Nama</th><th>Bidang</th><th>Alamat</th><th>Telepon</th><th>Pemb. Lapangan</th><th>Mhs KP</th><th>Aksi</th></tr></thead>
        <tbody>@forelse ($companies as $c)
            <tr><td><strong>{{ $c->name }}</strong></td><td>{{ $c->bidang ?: '-' }}</td><td style="max-width:260px">{{ $c->address ?: '-' }}</td><td>{{ $c->phone ?: '-' }}</td><td>{{ $c->pembimbing_count }}</td><td>{{ $c->kp_count }}</td>
                <td><div class="doc-actions">
                    <button class="doc-action-btn" title="Ubah" data-item="{{ json_encode($c->only(['id', 'name', 'address', 'bidang', 'phone'])) }}" onclick="openCo(JSON.parse(this.dataset.item))"><i class="bi bi-pencil-square"></i></button>
                    <button class="doc-action-btn delete" title="Hapus" onclick="del('/companies/{{ $c->id }}','perusahaan {{ e(addslashes($c->name)) }}')"><i class="bi bi-trash3"></i></button></div></td></tr>
        @empty<tr><td colspan="7"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-building"></i></div><div class="empty-state-title">Belum ada perusahaan</div></div></td></tr>@endforelse</tbody></table></div>
</div>
@endsection
@section('modals')
<div class="modal fade" id="coModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="coTitle">Tambah Perusahaan</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><input type="hidden" id="coId">
        <div class="form-group"><label class="form-label">Nama perusahaan</label><input class="form-control" id="coName"></div>
        <div class="form-group"><label class="form-label">Bidang usaha</label><input class="form-control" id="coBidang"></div>
        <div class="form-group"><label class="form-label">Alamat</label><textarea class="form-control" id="coAddr" rows="2"></textarea></div>
        <div class="form-group"><label class="form-label">Telepon</label><input class="form-control" id="coPhone"></div></div>
    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" onclick="saveCo(this)">Simpan</button></div>
</div></div></div>
@endsection
@section('extra-js')<script src="{{ asset('js/kp-pages.js') }}"></script><script src="{{ asset('js/admin-pages.js') }}"></script>@endsection
