@extends('layouts.app')
@section('title', 'Kategori Dokumen - Manajemen KP')
@section('page-title', 'Kategori Dokumen')
@section('page-subtitle', 'Jenis dokumen, tenggat, dan batas ukuran file')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('header-actions')<button class="btn btn-primary" onclick="openCat()"><i class="bi bi-plus-circle-fill"></i> Tambah Kategori</button>@endsection

@section('content')
<div class="documents-section">
    <div class="section-header"><h3 class="section-title"><i class="bi bi-tags-fill"></i> Daftar Kategori <small class="text-muted">({{ $categories->count() }})</small></h3></div>
    <div class="table-responsive-wrapper"><table class="documents-table">
        <thead><tr><th>Nama</th><th>Kode</th><th>Wajib</th><th>Tenggat</th><th>Maks.</th><th>Dok.</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>@foreach ($categories as $c)
            <tr><td><strong>{{ $c->name }}</strong></td><td class="text-muted">{{ $c->slug }}</td>
                <td>{{ $c->wajib ? 'Ya' : 'Tidak' }}</td><td class="doc-date">{{ $c->deadline?->format('d M Y') ?? '-' }}</td>
                <td>{{ $c->max_file_size }} MB</td><td>{{ $c->documents_count }}</td>
                <td><span class="badge {{ $c->is_active ? 'badge-approved' : 'badge-draft' }}">{{ $c->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td><div class="doc-actions">
                    <button class="doc-action-btn" title="Ubah" data-item="{{ json_encode(['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug, 'description' => $c->description, 'max_file_size' => $c->max_file_size, 'is_active' => $c->is_active, 'wajib' => $c->wajib, 'deadline' => $c->deadline?->format('Y-m-d')]) }}" onclick="openCat(JSON.parse(this.dataset.item))"><i class="bi bi-pencil-square"></i></button>
                    <button class="doc-action-btn delete" title="Hapus" onclick="del('/categories/{{ $c->id }}','kategori {{ e(addslashes($c->name)) }}')"><i class="bi bi-trash3"></i></button>
                </div></td></tr>
        @endforeach</tbody></table></div>
    <p class="text-muted small px-3 py-2 mb-0">Format berkas yang diterima diatur di menu <a href="{{ route('admin.settings') }}">Pengaturan</a>.</p>
</div>
@endsection

@section('modals')
<div class="modal fade" id="cModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="cTitle">Tambah Kategori</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <input type="hidden" id="cId">
        <div class="form-group"><label class="form-label">Nama</label><input class="form-control" id="cName"></div>
        <div class="form-group"><label class="form-label">Kode</label><input class="form-control" id="cSlug" placeholder="contoh: laporan_akhir"></div>
        <div class="form-group"><label class="form-label">Deskripsi</label><textarea class="form-control" id="cDesc" rows="2"></textarea></div>
        <div class="row"><div class="col-6 form-group"><label class="form-label">Maks. ukuran (MB)</label><input type="number" class="form-control" id="cMax" value="10" min="1" max="100"></div>
            <div class="col-6 form-group"><label class="form-label">Tenggat (opsional)</label><input type="date" class="form-control" id="cDeadline"></div></div>
        <div class="form-check"><input type="checkbox" class="form-check-input" id="cWajib" checked><label class="form-check-label" for="cWajib">Wajib dikumpulkan (dihitung pada kelengkapan)</label></div>
        <div class="form-check"><input type="checkbox" class="form-check-input" id="cActive" checked><label class="form-check-label" for="cActive">Aktif</label></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" onclick="saveCat(this)">Simpan</button></div>
</div></div></div>
@endsection
@section('extra-js')<script src="{{ asset('js/kp-pages.js') }}"></script><script src="{{ asset('js/admin-pages.js') }}"></script>@endsection
