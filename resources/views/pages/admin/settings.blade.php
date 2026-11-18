@extends('layouts.app')
@section('title', 'Pengaturan - Manajemen KP')
@section('page-title', 'Pengaturan Sistem')
@section('page-subtitle', 'Format berkas dan konfigurasi umum')
@section('extra-css')<link rel="stylesheet" href="{{ asset('css/kp-extra.css') }}">@endsection
@section('content')
<div class="sk-panel" style="max-width:640px"><div class="section-header"><h3 class="section-title"><i class="bi bi-file-earmark-lock2-fill"></i> Pengaturan File</h3></div>
    <form method="POST" action="{{ route('admin.settings.save') }}" class="sk-panel-body">@csrf
        <div class="form-group"><label class="form-label">Format berkas yang diterima</label>
            <input class="form-control" name="allowed_ext" value="{{ old('allowed_ext', $ext) }}" placeholder="pdf,doc,docx"><small class="text-muted">Pisahkan dengan koma. Batas ukuran diatur per kategori di menu Kategori Dokumen.</small></div>
        <button class="btn btn-primary">Simpan</button></form></div>
<div class="sk-panel" style="max-width:640px"><div class="sk-panel-body">
    <h5><i class="bi bi-award-fill"></i> Formula nilai akhir</h5><p class="text-muted mb-1">Bobot komponen nilai diatur di menu <a href="{{ route('nilai.index') }}">Penilaian</a>. Nilai ditampilkan ke mahasiswa setelah dipublikasikan.</p></div></div>
@endsection
