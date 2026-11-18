@if (file_exists(public_path('logo.png')))
    <img src="{{ asset('logo.png') }}" alt="Logo Universitas">
@else
    <svg viewBox="0 0 200 200" width="150" height="150" role="img" aria-label="Logo Universitas"><circle cx="100" cy="100" r="96" fill="#1f6b3a"/><circle cx="100" cy="100" r="74" fill="#fff"/><path d="M100 36a64 64 0 0 1 64 64H100z" fill="#8a1c24"/><path d="M100 164a64 64 0 0 1-64-64h64z" fill="#8a1c24"/><path d="M36 100A64 64 0 0 1 100 36v64z" fill="#d9a82b"/><path d="M164 100a64 64 0 0 1-64 64v-64z" fill="#d9a82b"/><circle cx="100" cy="100" r="20" fill="#fff" stroke="#1f6b3a" stroke-width="4"/><path d="M90 98h20M100 88v20" stroke="#1f6b3a" stroke-width="4"/></svg>
@endif
