@extends('layouts.app')
@section('title', 'Profil - Manajemen KP')
@section('page-title', 'Profil')
@section('page-subtitle', 'Perbarui informasi akun Anda')
@section('content')
<div class="documents-section" style="max-width:640px">
    <div class="section-header"><h3 class="section-title"><i class="bi bi-person-gear"></i> Informasi Akun</h3></div>
    <form method="POST" action="{{ route('settings.profile') }}" class="p-3">
        @csrf
        <div class="form-group"><label class="form-label">Nama</label>
            <input class="form-control" name="name" value="{{ old('name', auth()->user()->name) }}" required></div>
        <div class="form-group"><label class="form-label">Email</label>
            <input class="form-control" value="{{ auth()->user()->email }}" disabled></div>
        <div class="form-group"><label class="form-label">No. Telepon</label>
            <input class="form-control" name="phone" value="{{ old('phone', auth()->user()->phone) }}"></div>
        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
