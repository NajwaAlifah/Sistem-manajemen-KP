<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Manajemen KP')</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    
    @yield('extra-css')
</head>
<body class="auth-page">
    @yield('content')

    <!-- Bootstrap JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    @yield('extra-js')
</body>
</html>
