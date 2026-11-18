<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-url" content="{{ config('app.api_url', '/api') }}">
    <title>@yield('title', 'Dashboard - Manajemen KP')</title>
    
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" onerror="this.onerror=null;this.href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}" onerror="this.onerror=null;this.href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css'">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @yield('extra-css')
    <link rel="stylesheet" href="{{ asset('css/siakad.css') }}">
</head>
<body class="sk-body">
    @include('components.header')
    @include('components.sidebar')
    <div class="sk-backdrop" id="skBackdrop"></div>

    <main class="sk-main">
        @yield('hero')
        <div class="sk-pagehead">
            <div>
                <h1 class="sk-title">@yield('page-title', 'Dashboard')</h1>
                <p class="sk-subtitle">@yield('page-subtitle', '')</p>
            </div>
            <div class="sk-actions">@yield('header-actions')</div>
        </div>
        @if (session('success'))<div class="alert alert-success sk-flash"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>@endif
        @if ($errors->any())<div class="alert alert-danger sk-flash"><i class="bi bi-exclamation-triangle-fill"></i> {{ $errors->first() }}</div>@endif
        @yield('content')
        <footer class="sk-footer">&copy; {{ date('Y') }} Manajemen Dokumen KP &middot; Universitas Kebangsaan Republik Indonesia</footer>
    </main>

    @yield('modals')

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" onerror="this.onerror=null;var s=document.createElement('script');s.src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js';document.head.appendChild(s)"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        (function () {
            const sb = document.getElementById('skSidebar'), bd = document.getElementById('skBackdrop');
            const set = o => { sb.classList.toggle('open', o); bd.classList.toggle('show', o); };
            document.getElementById('skBurger')?.addEventListener('click', () => set(!sb.classList.contains('open')));
            bd.addEventListener('click', () => set(false));
            sb.querySelectorAll('a').forEach(a => a.addEventListener('click', () => set(false)));
        })();
    </script>
    @yield('extra-js')
</body>
</html>
