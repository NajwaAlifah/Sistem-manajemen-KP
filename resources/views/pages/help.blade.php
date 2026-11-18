@extends('layouts.app')
@section('title', 'Panduan & FAQ - Manajemen KP')
@section('page-title', 'Panduan & FAQ')
@section('page-subtitle', 'Alur KP, status dokumen, dan pertanyaan umum')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('content')
@php $role = auth()->user()->role; @endphp
<div class="sk-panel"><div class="section-header"><h3 class="section-title"><i class="bi bi-diagram-3-fill"></i> Alur Dokumen</h3></div>
    <div class="sk-panel-body">
        <p><span class="badge badge-draft">Draft</span> → <span class="badge badge-submitted">Diajukan</span> → <span class="badge badge-reviewed">Diperiksa</span> → <span class="badge badge-revision">Revisi</span> / <span class="badge badge-approved">Disetujui</span></p>
        <ol class="mb-0"><li>Mahasiswa mengunggah dokumen (status Draft) lalu mengajukannya.</li><li>Dosen pembimbing memeriksa; bila perlu perbaikan, dokumen dikembalikan dengan catatan (Revisi).</li>
            <li>Mahasiswa mengunggah versi baru dan mengajukan ulang. Dokumen yang disetujui masuk arsip digital.</li></ol>
    </div></div>
<div class="sk-panel"><div class="section-header"><h3 class="section-title"><i class="bi bi-signpost-split-fill"></i> Status Pelaksanaan KP</h3></div>
    <div class="sk-panel-body">@foreach (\App\Support\Kp::KP_STATUS as $k => $v)<span class="badge badge-{{ \App\Support\Kp::KP_BADGE[$k] }} me-1">{{ $v }}</span>@endforeach
        <p class="mt-2 mb-0 text-muted">Status KP berbeda dari status dokumen: menggambarkan posisi mahasiswa dalam keseluruhan proses KP.</p></div></div>
<div class="sk-panel"><div class="section-header"><h3 class="section-title"><i class="bi bi-question-circle-fill"></i> FAQ</h3></div>
    <div class="sk-panel-body">
    @foreach ([
        ['Format dan ukuran file apa yang diterima?', 'Format dan batas ukuran ditentukan admin prodi (bawaan: PDF, DOC, DOCX, maksimal 10 MB per kategori).'],
        ['Siapa yang dapat melihat dokumen saya?', 'Anda, dosen pembimbing Anda, dan admin prodi. Pembimbing lapangan tidak mengakses dokumen, hanya logbook dan penilaian.'],
        ['Bagaimana mengajukan KP?', 'Buka menu Pengajuan KP, isi perusahaan, judul, dan periode. Prodi akan menyetujui dan menetapkan dosen serta pembimbing lapangan.'],
        ['Bagaimana logbook divalidasi?', 'Pembimbing lapangan menyetujui atau meminta perbaikan. Catatan yang perlu diperbaiki dapat diubah lalu divalidasi ulang.'],
        ['Kapan nilai muncul?', 'Setelah dosen dan pembimbing lapangan mengisi nilai dan admin prodi mempublikasikannya.'],
        ['Lupa password?', 'Hubungi admin prodi untuk pemulihan akun.'],
    ] as [$q, $a])
        <details class="mb-2"><summary><strong>{{ $q }}</strong></summary><p class="mt-1 mb-2 text-muted">{{ $a }}</p></details>
    @endforeach
    </div></div>
@endsection
