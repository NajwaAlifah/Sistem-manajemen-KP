@extends('layouts.app')
@section('title', 'Pengguna - Manajemen KP')
@section('page-title', 'Data pengguna')
@section('page-subtitle', 'Kelola akun, peran, dosen pembimbing, dan pembimbing lapangan')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('page-title', ['mahasiswa' => 'Data Mahasiswa', 'dosen' => 'Data Dosen Pembimbing', 'lapangan' => 'Data Pembimbing Lapangan', 'admin' => 'Data Admin Prodi'][$role] ?? 'Semua Pengguna')
@section('header-actions')<button class="btn btn-primary" onclick="openUser(null, '{{ $role }}')"><i class="bi bi-person-plus-fill"></i> Tambah</button>@endsection

@section('content')
@php $badge = ['admin' => 'badge-revision', 'dosen' => 'badge-reviewed', 'mahasiswa' => 'badge-submitted', 'lapangan' => 'badge-approved']; @endphp
<div class="documents-section">
    <div class="section-header"><h3 class="section-title"><i class="bi bi-people-fill"></i> Daftar <small class="text-muted">({{ $users->count() }})</small></h3>
        <div class="d-flex gap-1 flex-wrap">
            <a class="btn btn-sm {{ !$role ? 'btn-primary' : 'btn-secondary' }}" href="{{ route('admin.users') }}">Semua</a>
            @foreach (['mahasiswa' => 'Mahasiswa', 'dosen' => 'Dosen', 'lapangan' => 'Lapangan', 'admin' => 'Admin'] as $k => $v)<a class="btn btn-sm {{ $role === $k ? 'btn-primary' : 'btn-secondary' }}" href="{{ route('admin.users', ['role' => $k]) }}">{{ $v }}</a>@endforeach
        </div></div>
    <div class="table-responsive-wrapper"><table class="documents-table">
        <thead><tr><th>Nama</th><th>NPM / NIP</th><th>Peran</th><th>Relasi</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>@foreach ($users as $u)
            <tr><td><strong>{{ $u->name }}</strong><div class="text-muted small">{{ \Illuminate\Support\Str::endsWith($u->email, '@kp.local') ? '' : $u->email }}</div></td>
                <td>{{ $u->nim ?: $u->nip ?: '-' }}</td>
                <td><span class="badge {{ $badge[$u->role] }}">{{ $u->role_label }}</span></td>
                <td class="small">@if ($u->role === 'mahasiswa')Dosen: {{ $u->dosen->name ?? '-' }}<br>Lapangan: {{ $u->lapangan->name ?? '-' }}
                    @elseif ($u->role === 'lapangan'){{ $u->company->name ?? '-' }}@else - @endif</td>
                <td><span class="badge {{ $u->is_active ? 'badge-approved' : 'badge-draft' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td><div class="doc-actions">
                    <button class="doc-action-btn" title="Ubah" data-item="{{ json_encode($u->only(['id', 'name', 'email', 'role', 'nim', 'nip', 'is_active', 'dosen_id', 'lapangan_id', 'company_id'])) }}" onclick="openUser(JSON.parse(this.dataset.item))"><i class="bi bi-pencil-square"></i></button>
                    @if ($u->id !== auth()->id())<button class="doc-action-btn delete" title="Hapus" onclick="del('/users/{{ $u->id }}','pengguna {{ e(addslashes($u->name)) }}')"><i class="bi bi-trash3"></i></button>@endif
                </div></td></tr>
        @endforeach</tbody></table></div>
</div>
@endsection

@section('modals')
<div class="modal fade" id="uModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="uTitle">Tambah Pengguna</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <input type="hidden" id="uId">
        <div class="form-group"><label class="form-label">Nama Lengkap</label><input class="form-control" id="uName"></div>
        <div class="row"><div class="col-sm-6 form-group"><label class="form-label">Peran</label>
            <select class="form-control" id="uRole" onchange="roleUI()"><option value="mahasiswa">Mahasiswa</option><option value="dosen">Dosen Pembimbing</option><option value="admin">Admin Prodi</option><option value="lapangan">Pembimbing Lapangan</option></select></div>
            <div class="col-sm-6 form-group"><label class="form-label" id="uIdLbl">NPM</label><input class="form-control" id="uNo" inputmode="numeric" placeholder="Hanya angka (untuk login)"></div></div>
        <div id="uMhsBox"><div class="form-group"><label class="form-label">Dosen Pembimbing</label>
            <select class="form-control" id="uDosen"><option value="">— belum ditetapkan —</option>@foreach ($dosen as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
            <div class="form-group"><label class="form-label">Pembimbing Lapangan</label>
            <select class="form-control" id="uLap"><option value="">— belum ditetapkan —</option>@foreach ($lapanganList as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div></div>
        <div class="form-group" id="uCoBox"><label class="form-label">Perusahaan</label>
            <select class="form-control" id="uCo"><option value="">— pilih perusahaan —</option>@foreach ($companies as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
        <div class="form-group"><label class="form-label">Email <small class="text-muted">(opsional)</small></label><input type="email" class="form-control" id="uEmail"></div>
        <div class="form-group"><label class="form-label">Password <small class="text-muted" id="uPwHint">(min. 6 karakter)</small></label><input type="password" class="form-control" id="uPw" autocomplete="new-password"></div>
        <div class="form-check"><input type="checkbox" class="form-check-input" id="uActive" checked><label class="form-check-label" for="uActive">Akun aktif</label></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" onclick="saveUser(this)">Simpan</button></div>
</div></div></div>
@endsection
@section('extra-js')<script src="{{ asset('js/kp-pages.js') }}"></script><script src="{{ asset('js/admin-pages.js') }}"></script>@endsection
