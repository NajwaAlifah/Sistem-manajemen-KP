@php
    $role = auth()->user()->role;
    $is = fn (...$p) => request()->is(...$p);
    $uRole = request('role');
    $menus = [
        'mahasiswa' => [
            ['Dashboard', route('dashboard'), 'bi-house-door-fill', $is('dashboard*')],
            ['Pengajuan KP', route('kp.pengajuan'), 'bi-send-fill', $is('kp/pengajuan')],
            ['Logbook', route('logbook.index'), 'bi-journal-text', $is('logbook*')],
            ['Bimbingan', route('bimbingan.index'), 'bi-chat-dots-fill', $is('bimbingan*')],
            ['Progress KP', route('kp.progress'), 'bi-graph-up-arrow', $is('kp/progress')],
            ['Penilaian', route('nilai.index'), 'bi-award-fill', $is('nilai*')],
            ['Pencarian & Arsip', route('archive.index'), 'bi-folder2-open', $is('archive*', 'documents*')],
            ['Pengumuman', route('announcements.index'), 'bi-megaphone-fill', $is('pengumuman*')],
            ['Panduan KP', route('help'), 'bi-book-fill', $is('help')],
        ],
        'dosen' => [
            ['Dashboard', route('dashboard'), 'bi-house-door-fill', $is('dashboard*')],
            ['Mahasiswa Bimbingan', route('monitoring.index'), 'bi-people-fill', $is('monitoring*')],
            ['Logbook Mahasiswa', route('logbook.index'), 'bi-journal-text', $is('logbook*')],
            ['Bimbingan', route('bimbingan.index'), 'bi-chat-dots-fill', $is('bimbingan*')],
            ['Penilaian', route('nilai.index'), 'bi-award-fill', $is('nilai*')],
            ['Arsip Dokumen', route('archive.index'), 'bi-folder2-open', $is('archive*', 'documents*')],
            ['Pengumuman', route('announcements.index'), 'bi-megaphone-fill', $is('pengumuman*')],
            ['Panduan KP', route('help'), 'bi-book-fill', $is('help')],
        ],
        'lapangan' => [
            ['Dashboard', route('dashboard'), 'bi-house-door-fill', $is('dashboard*')],
            ['Mahasiswa Bimbingan', route('monitoring.index'), 'bi-people-fill', $is('monitoring*')],
            ['Validasi Logbook', route('logbook.index'), 'bi-journal-check', $is('logbook*')],
            ['Penilaian KP', route('nilai.index'), 'bi-award-fill', $is('nilai*')],
            ['Pengumuman', route('announcements.index'), 'bi-megaphone-fill', $is('pengumuman*')],
            ['Panduan KP', route('help'), 'bi-book-fill', $is('help')],
        ],
        'admin' => [
            ['Dashboard', route('dashboard'), 'bi-house-door-fill', $is('dashboard*')],
            ['Data Mahasiswa', route('admin.users', ['role' => 'mahasiswa']), 'bi-mortarboard-fill', $is('admin/users') && $uRole === 'mahasiswa'],
            ['Data Dosen', route('admin.users', ['role' => 'dosen']), 'bi-person-workspace', $is('admin/users') && $uRole === 'dosen'],
            ['Pembimbing Lapangan', route('admin.users', ['role' => 'lapangan']), 'bi-person-badge-fill', $is('admin/users') && $uRole === 'lapangan'],
            ['Perusahaan', route('admin.companies'), 'bi-building-fill', $is('admin/companies')],
            ['Data KP', route('admin.kp'), 'bi-briefcase-fill', $is('admin/kp')],
            ['Kategori Dokumen', route('admin.categories'), 'bi-tags-fill', $is('admin/categories')],
            ['Semua Dokumen', route('archive.index'), 'bi-folder2-open', $is('archive*', 'documents*')],
            ['Monitoring KP', route('monitoring.index'), 'bi-activity', $is('monitoring*')],
            ['Penilaian', route('nilai.index'), 'bi-award-fill', $is('nilai*')],
            ['Pengumuman', route('announcements.index'), 'bi-megaphone-fill', $is('pengumuman*')],
            ['Audit Log', route('admin.audit'), 'bi-shield-check', $is('admin/audit')],
            ['Pengaturan', route('admin.settings'), 'bi-gear-fill', $is('admin/settings')],
        ],
    ];
@endphp
<aside class="sk-sidebar" id="skSidebar">
    <div class="sk-logo">@include('components.logo')</div>
    <nav class="sk-nav">
        @foreach ($menus[$role] ?? [] as [$label, $url, $icon, $active])
            <a href="{{ $url }}" class="{{ $active ? 'active' : '' }}"><i class="bi {{ $icon }}"></i> {{ $label }}</a>
        @endforeach
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit" class="sk-logout"><i class="bi bi-box-arrow-left"></i> Logout</button></form>
    </nav>
</aside>
