@extends('layouts.app')
@section('title', 'Pengajuan KP - Manajemen KP')
@section('page-title', 'Pengajuan KP')
@section('page-subtitle', 'Ajukan rencana pelaksanaan kerja praktik')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('content')
@php use App\Support\Kp; $locked = $kp && in_array($kp->status, ['berlangsung', 'menunggu_penyelesaian', 'selesai']); @endphp
@if ($kp)
<div class="sk-panel"><div class="sk-panel-body">
    <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
        <h4 class="mb-0"><i class="bi bi-clipboard-check"></i> Status Pengajuan</h4>
        <span class="badge badge-{{ Kp::KP_BADGE[$kp->status] }}" style="font-size:13px">{{ Kp::KP_STATUS[$kp->status] }}</span>
    </div>
    <div class="info-grid" style="padding:10px 0 0">
        <div class="info-item"><div class="k">Perusahaan</div><div class="v">{{ $kp->perusahaan }}</div></div>
        <div class="info-item"><div class="k">Judul / Bidang</div><div class="v">{{ $kp->judul ?: '-' }}</div></div>
        <div class="info-item"><div class="k">Periode</div><div class="v">{{ $kp->tanggal_mulai?->format('d M Y') }} – {{ $kp->tanggal_selesai?->format('d M Y') }}</div></div>
        <div class="info-item"><div class="k">Dosen Pembimbing</div><div class="v">{{ auth()->user()->dosen->name ?? 'Belum ditetapkan' }}</div></div>
        <div class="info-item"><div class="k">Pembimbing Lapangan</div><div class="v">{{ auth()->user()->lapangan->name ?? 'Belum ditetapkan' }}</div></div>
        <div class="info-item"><div class="k">Konfirmasi Lapangan</div><div class="v">{{ $kp->dikonfirmasi_at ? $kp->dikonfirmasi_at->format('d M Y') : 'Belum' }}</div></div>
    </div>
    @if ($kp->catatan_admin)<div class="sk-note mt-3"><strong>Catatan prodi:</strong> {{ $kp->catatan_admin }}</div>@endif
</div></div>
@endif

@unless ($locked)
<div class="sk-panel">
    <div class="section-header"><h3 class="section-title"><i class="bi bi-pencil-square"></i> {{ $kp ? 'Ubah Pengajuan' : 'Form Pengajuan KP' }}</h3></div>
    <form method="POST" action="{{ route('kp.store') }}" class="sk-panel-body">
        @csrf
        <div class="sk-form">
            <div><label>Pilih perusahaan terdaftar</label>
                <select class="form-control" name="company_id"><option value="">— tidak ada di daftar —</option>
                    @foreach ($companies as $c)<option value="{{ $c->id }}" @selected(old('company_id', $kp->company_id ?? null) == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
            <div><label>Atau tulis nama perusahaan</label>
                <input class="form-control" name="perusahaan" value="{{ old('perusahaan', $kp->perusahaan ?? '') }}" placeholder="Wajib diisi bila tidak memilih dari daftar"></div>
            <div class="full"><label>Judul / bidang pekerjaan KP</label><input class="form-control" name="judul" value="{{ old('judul', $kp->judul ?? '') }}" required></div>
            <div><label>Tanggal mulai</label><input type="date" class="form-control" name="tanggal_mulai" value="{{ old('tanggal_mulai', $kp?->tanggal_mulai?->format('Y-m-d')) }}" required></div>
            <div><label>Tanggal selesai</label><input type="date" class="form-control" name="tanggal_selesai" value="{{ old('tanggal_selesai', $kp?->tanggal_selesai?->format('Y-m-d')) }}" required></div>
        </div>
        <div class="sk-note mt-3"><i class="bi bi-info-circle"></i> Berkas persyaratan (mis. Surat Pengantar, Proposal KP) diunggah melalui <a href="{{ route('dashboard') }}">Dashboard &rsaquo; Upload Dokumen</a>.</div>
        <button class="btn btn-primary mt-3"><i class="bi bi-send"></i> {{ $kp ? 'Kirim Ulang Pengajuan' : 'Kirim Pengajuan' }}</button>
    </form>
</div>
@endunless
@endsection
