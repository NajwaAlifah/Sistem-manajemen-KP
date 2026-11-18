@extends('layouts.app')
@section('title', 'Ubah Password - Manajemen KP')
@section('page-title', 'Ubah Password')
@section('page-subtitle', 'Gunakan password yang kuat dan unik')
@section('content')
<div class="documents-section" style="max-width:640px">
    <div class="section-header"><h3 class="section-title"><i class="bi bi-key"></i> Password</h3></div>
    <form method="POST" action="{{ route('settings.password') }}" class="p-3">
        @csrf
        <div class="form-group"><label class="form-label">Password Saat Ini</label><input type="password" class="form-control" name="current_password" required></div>
        <div class="form-group"><label class="form-label">Password Baru</label><input type="password" class="form-control" name="password" minlength="6" required></div>
        <div class="form-group"><label class="form-label">Konfirmasi Password Baru</label><input type="password" class="form-control" name="password_confirmation" required></div>
        <button class="btn btn-primary">Ubah Password</button>
    </form>
</div>
@endsection
