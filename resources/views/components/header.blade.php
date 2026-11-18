@php
    $u = auth()->user();
    $ident = $u->nim ?: ($u->nip ?: $u->name);
    $initial = strtoupper(mb_substr($u->name, 0, 1));
    $unread = \App\Models\Notif::where('user_id', $u->id)->whereNull('read_at')->count();
@endphp
<header class="sk-topbar">
    <div class="d-flex align-items-center gap-2">
        <button class="sk-burger" id="skBurger" aria-label="Menu"><i class="bi bi-list"></i></button>
        <span class="sk-brand"><span class="d-none d-md-inline">Manajemen KP Universitas Kebangsaan Republik Indonesia</span><span class="d-md-none">Manajemen KP</span></span>
    </div>
    <div class="d-flex align-items-center h-100">
        <a href="{{ route('notifications.index') }}" class="sk-bell" title="Notifikasi" aria-label="Notifikasi">
            <i class="bi bi-bell-fill"></i>@if ($unread)<span class="sk-bell-count">{{ $unread > 99 ? '99+' : $unread }}</span>@endif
        </a>
        <div class="dropdown">
            <button class="sk-user" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="sk-avatar">{{ $initial }}</span>
                <span class="sk-welcome d-none d-sm-inline">Welcome, {{ $ident }}</span>
                <i class="bi bi-caret-down-fill small"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end sk-dd">
                <li class="sk-dd-head"><span class="sk-avatar lg">{{ $initial }}</span>
                    <div><strong>{{ $u->name }}</strong><small>{{ $u->role_label }}</small></div></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('settings.profile') }}"><i class="bi bi-person"></i> Profil Saya</a></li>
                <li><a class="dropdown-item" href="{{ route('settings.password') }}"><i class="bi bi-key"></i> Ubah Password</a></li>
                <li><a class="dropdown-item" href="{{ route('help') }}"><i class="bi bi-question-circle"></i> Panduan & FAQ</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Logout</button></form></li>
            </ul>
        </div>
    </div>
</header>
