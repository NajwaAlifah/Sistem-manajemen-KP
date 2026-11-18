@extends('layouts.auth')

@section('title', 'Login - Manajemen KP')

@section('content')
<div class="login-container">
    <!-- Logo & Brand -->
    <div class="logo-section">
        <div class="logo-icon">
            <i class="bi bi-file-earmark-text"></i>
        </div>
        <h1>Manajemen KP</h1>
        <p>Sistem Manajemen Dokumen Kerja Praktik</p>
    </div>

    <!-- Messages -->
    @if ($errors->any())
        <div class="error-message show">
            <strong>Error:</strong>
            <ul style="margin: 5px 0 0 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="success-message show">
            {{ session('success') }}
        </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <div class="form-group">
            <label class="form-label" for="login">NPM / NIP</label>
            <div class="input-group-icon">
                <input 
                    type="text" inputmode="numeric" autocomplete="username" 
                    class="form-control @error('login') is-invalid @enderror" 
                    id="login" 
                    name="login"
                    value="{{ old('login') }}"
                    placeholder="Masukkan NPM (mahasiswa) atau NIP (dosen/admin)"
                    required
                    autofocus
                >
                <i class="bi bi-person-badge"></i>
            </div>
            @error('login')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div class="input-group-icon">
                <input 
                    type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    id="password" 
                    name="password"
                    placeholder="••••••••"
                    required
                >
                <i class="bi bi-lock"></i>
            </div>
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-check">
            <input 
                type="checkbox" 
                class="form-check-input" 
                id="remember"
                name="remember"
                {{ old('remember') ? 'checked' : '' }}
            >
            <label class="form-check-label" for="remember">
                Ingat saya di perangkat ini
            </label>
        </div>

        <button type="submit" class="login-btn" id="loginBtn">
            <i class="bi bi-box-arrow-in-right" style="margin-right: 8px;"></i>
            Masuk
        </button>
    </form>

    <!-- Demo Hint - ONLY IN DEV -->
    @if (config('app.debug'))
        <div class="demo-hint">
            <strong>Demo Akun (Development Only):</strong>
            Mahasiswa (NPM): <code>20240000001</code><br>
            Dosen Pembimbing (NIP): <code>0000000001</code><br>
            Pembimbing Lapangan (ID): <code>0000000002</code><br>
            Admin Prodi (ID): <code>0000000000</code><br>
            Password: <code>password123</code>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer-text">
        <p>Belum punya akun?<br>
        Hubungi <strong>admin@fiksi.edu</strong></p>
    </div>
</div>

<script>
    // Enter key submit
    document.getElementById('loginForm').addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.target.matches('textarea')) {
            document.getElementById('loginForm').submit();
        }
    });

    // Demo auto-fill (dev only)
    @if (config('app.debug'))
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.demo-hint code').forEach(el => {
                el.style.cursor = 'pointer';
                el.addEventListener('click', () => {
                    const text = el.textContent;
                    if (/^\d+$/.test(text)) {
                        document.getElementById('login').value = text;
                        document.getElementById('login').focus();
                    } else if (text === 'password123') {
                        document.getElementById('password').value = text;
                        document.getElementById('password').focus();
                    }
                });
            });
        });
    @endif
</script>
@endsection
