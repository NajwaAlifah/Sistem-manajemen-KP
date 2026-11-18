@extends('layouts.app')
@section('title', 'Data KP - Manajemen KP')
@section('page-title', 'Data KP')
@section('page-subtitle', 'Pengajuan, status KP, dan penetapan dosen pembimbing / pembimbing lapangan')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('header-actions')<button class="btn btn-primary" onclick="openKp()"><i class="bi bi-plus-circle-fill"></i> Tambah Data KP</button>@endsection

@section('content')
@php use App\Support\Kp; @endphp
<div class="documents-section">
    <div class="section-header"><h3 class="section-title"><i class="bi bi-briefcase-fill"></i> Data KP <small class="text-muted">({{ $kps->count() }})</small></h3></div>
    <div class="table-responsive-wrapper"><table class="documents-table" style="min-width:900px">
        <thead><tr><th>Mahasiswa</th><th>Perusahaan / Judul</th><th>Periode</th><th>Status</th><th>Pembimbing</th><th>Aksi</th></tr></thead>
        <tbody>@forelse ($kps as $k)
            <tr><td><strong>{{ $k->mahasiswa->name }}</strong><div class="text-muted small">{{ $k->mahasiswa->nim }}</div></td>
                <td>{{ $k->perusahaan }}<div class="text-muted small">{{ $k->judul ?: '-' }}</div></td>
                <td class="doc-date">{{ $k->tanggal_mulai?->format('d M Y') ?? '-' }} –<br>{{ $k->tanggal_selesai?->format('d M Y') ?? '-' }}</td>
                <td><span class="badge badge-{{ Kp::KP_BADGE[$k->status] }}">{{ Kp::KP_STATUS[$k->status] }}</span></td>
                <td class="small">Dosen: {{ $k->mahasiswa->dosen->name ?? '-' }}<br>Lapangan: {{ $k->mahasiswa->lapangan->name ?? '-' }}</td>
                @php $payload = $k->only(['id', 'mahasiswa_id', 'perusahaan', 'company_id', 'judul', 'tanggal_mulai', 'tanggal_selesai', 'status', 'catatan_admin']) + ['dosen_id' => $k->mahasiswa->dosen_id, 'lapangan_id' => $k->mahasiswa->lapangan_id, 'nama' => $k->mahasiswa->name]; @endphp
                <td><div class="doc-actions">
                    @if ($k->status === 'menunggu_persetujuan')<button class="btn btn-primary btn-sm" data-item="{{ json_encode($payload) }}" onclick="openKp(JSON.parse(this.dataset.item))">Tinjau</button>
                    @else<button class="doc-action-btn" title="Atur" data-item="{{ json_encode($payload) }}" onclick="openKp(JSON.parse(this.dataset.item))"><i class="bi bi-sliders"></i></button>@endif
                    <button class="doc-action-btn delete" title="Hapus" onclick="del('/kp/{{ $k->id }}','data KP')"><i class="bi bi-trash3"></i></button>
                </div></td></tr>
        @empty<tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-briefcase"></i></div><div class="empty-state-title">Belum ada data KP</div></div></td></tr>@endforelse</tbody></table></div>
</div>
@endsection

@section('modals')
<div class="modal fade" id="kpModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="kpTitle">Data Kerja Praktik</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><input type="hidden" id="kId">
        <div class="row">
        <div class="col-md-6 form-group"><label class="form-label">Mahasiswa</label>
            <select class="form-control" id="kM">@foreach ($mahasiswa as $m)<option value="{{ $m->id }}">{{ $m->name }} ({{ $m->nim }})</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">Status KP</label>
            <select class="form-control" id="kSt">@foreach (Kp::KP_STATUS as $k => $v)@if ($k !== 'belum_mengajukan')<option value="{{ $k }}">{{ $v }}</option>@endif @endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">Perusahaan terdaftar</label>
            <select class="form-control" id="kC"><option value="">— tidak ada di daftar —</option>@foreach ($companies as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">Nama perusahaan</label><input class="form-control" id="kP"></div>
        <div class="col-12 form-group"><label class="form-label">Judul / Bidang KP</label><input class="form-control" id="kJ"></div>
        <div class="col-6 form-group"><label class="form-label">Mulai</label><input type="date" class="form-control" id="kS"></div>
        <div class="col-6 form-group"><label class="form-label">Selesai</label><input type="date" class="form-control" id="kE"></div>
        <div class="col-md-6 form-group"><label class="form-label">Dosen Pembimbing</label>
            <select class="form-control" id="kD"><option value="">— belum ditetapkan —</option>@foreach ($dosen as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">Pembimbing Lapangan</label>
            <select class="form-control" id="kL"><option value="">— belum ditetapkan —</option>@foreach ($lapanganList as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
        <div class="col-12 form-group"><label class="form-label">Catatan untuk mahasiswa</label><textarea class="form-control" id="kN" rows="2" placeholder="Mis. alasan penolakan atau arahan"></textarea></div>
        </div></div>
    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" onclick="saveKp(this)">Simpan</button></div>
</div></div></div>
@endsection
@section('extra-js')<script src="{{ asset('js/kp-pages.js') }}"></script><script src="{{ asset('js/admin-pages.js') }}"></script>@endsection
