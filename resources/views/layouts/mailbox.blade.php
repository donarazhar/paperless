<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Paperless Mail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --primary-light: #e0e7ff;
            --bg: #f8fafc;
            --surface: #ffffff;
            --border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --sidebar-w: 260px;
            --header-h: 64px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-main);
            margin: 0;
            overflow: hidden;
        }

        /* ══ HEADER ══ */
        .mb-header {
            height: var(--header-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 1rem;
            gap: .75rem;
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            box-shadow: 0 1px 4px rgba(15,23,42,.04);
        }

        /* Hamburger button — standalone, always visible on mobile/tablet */
        .mb-hamburger {
            display: none; /* shown via media query */
            width: 40px; height: 40px;
            border: none; background: none;
            cursor: pointer; padding: 0;
            border-radius: .5rem;
            align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: background .2s;
            color: var(--text-main);
        }
        .mb-hamburger:hover { background: var(--primary-light); }
        .mb-hamburger i { font-size: 1.4rem; }

        .mb-brand {
            display: flex;
            align-items: center;
            gap: .65rem;
            font-weight: 700;
            font-size: 1rem;
            color: var(--text-main);
            text-decoration: none;
            flex-shrink: 0;
        }
        .mb-brand img { width: 32px; height: 32px; border-radius: 8px; }
        .mb-brand-sub { font-size: .6rem; font-weight: 600; color: var(--text-muted); }

        .mb-search {
            flex: 1;
            max-width: 520px;
            margin: 0 auto;
            position: relative;
        }
        .mb-search input {
            width: 100%;
            background: #f1f5f9;
            border: 1.5px solid transparent;
            border-radius: 12px;
            padding: .55rem 1rem .55rem 2.4rem;
            font-size: .9rem;
            font-family: inherit;
            transition: all .2s;
        }
        .mb-search input:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79,70,229,.1);
            outline: none;
        }
        .mb-search i {
            position: absolute; left: .9rem; top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted); font-size: .9rem;
        }

        .mb-profile {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-shrink: 0;
        }

        /* ══ APP LAYOUT ══ */
        .mb-app {
            display: flex;
            height: 100vh;
            padding-top: var(--header-h);
            position: relative;
        }

        /* ══ SIDEBAR OVERLAY (mobile/tablet) ══ */
        .mb-overlay {
            display: none;
            position: fixed;
            inset: 0;
            top: var(--header-h);
            background: rgba(15,23,42,.45);
            backdrop-filter: blur(3px);
            z-index: 1015;
            opacity: 0;
            pointer-events: none;
            transition: opacity .3s;
        }
        .mb-overlay.show { opacity: 1; pointer-events: auto; }

        /* ══ SIDEBAR — Gmail Style ══ */
        .mb-sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            padding: .75rem 0 1rem;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            flex-shrink: 0;
            transition: transform .3s cubic-bezier(.16,1,.3,1);
            scrollbar-width: thin;
            scrollbar-color: #e2e8f0 transparent;
        }
        .mb-sidebar::-webkit-scrollbar { width: 4px; }
        .mb-sidebar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        /* Compose button — Gmail pill style */
        .mb-compose-wrap { padding: .5rem 1rem 1rem; flex-shrink: 0; }
        .mb-compose-btn {
            background: #fff;
            color: #0f172a;
            border: none;
            border-radius: 18px;
            padding: .9rem 1.5rem .9rem 1.1rem;
            font-size: .9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: .75rem;
            transition: all .2s;
            text-decoration: none;
            box-shadow: 0 1px 6px rgba(0,0,0,.12), 0 4px 12px rgba(0,0,0,.06);
            width: auto;
        }
        .mb-compose-btn:hover {
            box-shadow: 0 2px 10px rgba(0,0,0,.16), 0 6px 18px rgba(0,0,0,.08);
            color: #0f172a;
            transform: translateY(-1px);
            background: #f8fafc;
        }
        .mb-compose-btn .compose-icon {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .mb-compose-btn .compose-icon i { font-size: .95rem; color: #fff; }

        /* Nav */
        .mb-nav { list-style: none; padding: 0; margin: 0; }
        .mb-nav-item { margin-bottom: 1px; }

        /* Gmail-style: pill rounded on right side */
        .mb-nav-link {
            display: flex;
            align-items: center;
            gap: .85rem;
            padding: .55rem 1rem .55rem 1.25rem;
            border-radius: 0 100px 100px 0;   /* Gmail pill right */
            margin-right: .75rem;              /* gap dari kanan */
            color: #0f172a;
            text-decoration: none;
            font-size: .875rem;
            font-weight: 500;
            transition: background .12s, color .12s;
            white-space: nowrap;
            min-height: 38px;
        }
        .mb-nav-link:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .mb-nav-link.active {
            background: #d3e3fd;   /* Gmail active = biru muda */
            color: #041e49;
            font-weight: 700;
        }
        .mb-nav-link i {
            font-size: 1rem;
            color: #444746;
            width: 1.15rem;
            text-align: center;
            flex-shrink: 0;
        }
        .mb-nav-link.active i { color: #041e49; }

        /* Count badge — bold number ala Gmail */
        .mb-nav-badge {
            margin-left: auto;
            font-size: .8rem;
            font-weight: 700;
            color: #0f172a;
            padding: 0 .25rem;
            flex-shrink: 0;
            min-width: 1.5rem;
            text-align: right;
        }
        .mb-nav-badge.danger { color: #dc2626; }
        .mb-nav-badge.warn   { color: #d97706; }

        /* Section divider */
        .sb-divider {
            height: 1px;
            background: var(--border);
            margin: .5rem 1rem .5rem .75rem;
        }

        /* "Selengkapnya" toggle — Gmail style */
        .mb-nav-toggle {
            display: flex;
            align-items: center;
            gap: .85rem;
            padding: .55rem 1rem .55rem 1.25rem;
            border-radius: 0 100px 100px 0;
            margin-right: .75rem;
            color: #444746;
            font-size: .875rem;
            font-weight: 500;
            cursor: pointer;
            background: none;
            border: none;
            width: calc(100% - .75rem);
            text-align: left;
            transition: background .12s;
            min-height: 38px;
        }
        .mb-nav-toggle:hover { background: #f1f5f9; }
        .mb-nav-toggle i.nav-icon { font-size: 1rem; color: #444746; width: 1.15rem; text-align: center; }
        .mb-nav-toggle .toggle-chevron { margin-left: auto; font-size: .7rem; color: #64748b; transition: transform .3s; }
        .mb-nav-toggle.open .toggle-chevron { transform: rotate(180deg); }

        /* Sub-item indented */
        .mb-nav-sub .mb-nav-link {
            padding-left: 2.5rem;
            font-size: .82rem;
        }

        /* ══ MAIN CONTENT ══ */
        .mb-main {
            flex: 1;
            background: var(--surface);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            margin: .5rem .5rem 0 .5rem;
            border-radius: 16px 16px 0 0;
            box-shadow: 0 0 10px rgba(0,0,0,.025);
            border: 1px solid var(--border);
            min-width: 0;
        }

        /* Split pane */
        .mb-split { display: flex; height: 100%; }
        .mb-list-pane {
            flex: 1;
            min-width: 280px;
            max-width: 420px;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            background: #fff;
        }
        .mb-detail-pane {
            flex: 2;
            background: var(--bg);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        /* Mail item */
        .mail-item {
            display: flex;
            padding: .85rem 1rem;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: background .15s;
            text-decoration: none;
            color: inherit;
        }
        .mail-item:hover { background: #f8fafc; }
        .mail-item.unread { font-weight: 700; background: #fff; }
        .mail-item.read { font-weight: 400; background: #f8fafc; color: #475569; }
        .mail-item.active { background: var(--primary-light); border-left: 3px solid var(--primary); }

        .mail-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: #e2e8f0; display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #64748b; margin-right: 1rem; flex-shrink: 0;
        }
        .mail-content { flex: 1; min-width: 0; }
        .mail-header-row { display: flex; justify-content: space-between; margin-bottom: .2rem; }
        .mail-sender { font-size: .9rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: inherit; }
        .mail-date { font-size: .75rem; color: var(--text-muted); white-space: nowrap; }
        .mail-subject { font-size: .85rem; color: #0f172a; margin-bottom: .2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: inherit; }
        .mail-snippet { font-size: .8rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 400; }

        .list-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .mail-scroll { overflow-y: auto; flex: 1; }

        /* ══ RESPONSIVE ══ */

        /* Tablet (768px – 1024px) */
        @media (max-width: 1024px) {
            .mb-hamburger { display: flex; }
            .mb-search { display: none; }

            .mb-overlay { display: block; }

            .mb-sidebar {
                position: fixed;
                top: var(--header-h);
                left: 0;
                bottom: 0;
                z-index: 1020;
                transform: translateX(-100%);
                box-shadow: 4px 0 20px rgba(15,23,42,.12);
            }
            .mb-sidebar.open { transform: translateX(0); }

            .mb-main { margin: .25rem .25rem 0 .25rem; border-radius: 12px 12px 0 0; }
        }

        /* Mobile (≤ 640px) */
        @media (max-width: 640px) {
            .mb-list-pane { max-width: 100%; border-right: none; }
            .mb-detail-pane { display: none; }
            .mb-detail-pane.show {
                display: flex;
                position: fixed;
                inset: 0;
                top: var(--header-h);
                z-index: 1025;
            }
            .mb-main { margin: 0; border-radius: 0; border-left: none; border-right: none; }
        }

        /* Always show search on large desktop */
        @media (min-width: 1025px) {
            .mb-search { display: block; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php $role = Auth::user()->role ?? ''; @endphp

    <!-- ══ HEADER ══ -->
    <header class="mb-header">
        <!-- Hamburger — standalone button, tidak terbungkus link -->
        <button class="mb-hamburger" id="mbHamburger" aria-label="Buka Menu" type="button">
            <i class="bi bi-list" id="mbHamIcon"></i>
        </button>

        <!-- Brand -->
        <a href="{{ route('dashboard') }}" class="mb-brand">
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
            <div class="d-flex flex-column" style="line-height:1.2;">
                <span>Paperless Mail</span>
                <span class="mb-brand-sub">YPI Al Azhar v.1.0</span>
            </div>
        </a>

        <!-- Search (hidden on tablet/mobile) -->
        <div class="mb-search">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Telusuri surat...">
        </div>

        <!-- Profile -->
        <div class="mb-profile dropdown">
            <a href="#" class="text-muted d-flex align-items-center gap-2"
               data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration:none;">
                <div class="d-none d-md-flex flex-column text-end">
                    <span style="font-size:.85rem;font-weight:700;color:#0f172a;line-height:1.2;">{{ Auth::user()->name ?? 'User' }}</span>
                    <span style="font-size:.7rem;color:#64748b;line-height:1.2;text-transform:lowercase;">{{ Auth::user()->email ?? '' }}</span>
                </div>
                <div style="width:36px;height:36px;border-radius:50%;background:#4f46e5;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;flex-shrink:0;">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0"
                style="border-radius:.75rem;margin-top:.5rem;min-width:180px;">
                <li><a class="dropdown-item py-2 mt-1" href="{{ route('profile.edit') }}" style="font-size:.85rem;"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 mb-1 text-danger" style="font-size:.85rem;">
                            <i class="bi bi-box-arrow-right me-2"></i> Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </header>

    <!-- Overlay (klik untuk tutup sidebar) -->
    <div class="mb-overlay" id="mbOverlay"></div>

    <!-- ══ APP LAYOUT ══ -->
    <div class="mb-app">

        <!-- ══ SIDEBAR — Gmail Style ══ -->
        <aside class="mb-sidebar" id="mbSidebar">

            <!-- Compose button -->
            @if(in_array($role, ['admin_sekretariat', 'admin_unit', 'admin']))
            <div class="mb-compose-wrap">
                <a href="{{ route('letters.create') }}" class="mb-compose-btn">
                    <span class="compose-icon"><i class="bi bi-pencil"></i></span>
                    Tulis Surat
                </a>
            </div>
            @else
            <div style="height:.75rem;"></div>
            @endif

            <!-- ── Nav items ── -->
            <ul class="mb-nav">

                {{-- ADMIN GROUP --}}
                @if(in_array($role, ['admin_sekretariat', 'admin_unit', 'admin']))
                <li class="mb-nav-item">
                    <a href="{{ route('letters.inbound') }}"
                       class="mb-nav-link {{ request()->routeIs('letters.inbound*') ? 'active' : '' }}">
                        <i class="bi bi-inbox-fill"></i>
                        <span>Inbox</span>
                        @if(isset($unreadInboxCount) && $unreadInboxCount > 0)
                            <span class="mb-nav-badge">{{ $unreadInboxCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="mb-nav-item">
                    <a href="{{ route('letters.drafts') }}"
                       class="mb-nav-link {{ request()->routeIs('letters.drafts') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span>Draft</span>
                        @if(isset($draftCount) && $draftCount > 0)
                            <span class="mb-nav-badge warn">{{ $draftCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="mb-nav-item">
                    <a href="{{ route('letters.outbound') }}"
                       class="mb-nav-link {{ request()->routeIs('letters.outbound*') ? 'active' : '' }}">
                        <i class="bi bi-send-fill"></i>
                        <span>Outbox</span>
                        @if(isset($pendingSendingCount) && $pendingSendingCount > 0)
                            <span class="mb-nav-badge danger">{{ $pendingSendingCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="mb-nav-item">
                    <a href="{{ route('letters.arsip') }}"
                       class="mb-nav-link {{ request()->routeIs('letters.arsip') ? 'active' : '' }}">
                        <i class="bi bi-archive-fill"></i>
                        <span>Arsip</span>
                    </a>
                </li>

                <div class="sb-divider"></div>

                <li class="mb-nav-item">
                    <a href="{{ route('letters.createExternal') }}"
                       class="mb-nav-link {{ request()->routeIs('letters.createExternal') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-plus-fill"></i>
                        <span>Catat Surat Masuk</span>
                    </a>
                </li>
                @endif

                {{-- SUBAG / KEPALA UNIT --}}
                @if(in_array($role, ['subag_persuratan', 'kepala_unit']))
                <li class="mb-nav-item">
                    <a href="{{ route('tugas.accSurat') }}"
                       class="mb-nav-link {{ request()->routeIs('tugas.accSurat') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span>Draft</span>
                        @if(isset($unreadAccCount) && $unreadAccCount > 0)
                            <span class="mb-nav-badge danger">{{ $unreadAccCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="mb-nav-item">
                    <a href="{{ route('tugas.disposisi') }}"
                       class="mb-nav-link {{ request()->routeIs('tugas.disposisi') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-check-fill"></i>
                        <span>Disposisi</span>
                        @if(isset($pendingDispCount) && $pendingDispCount > 0)
                            <span class="mb-nav-badge">{{ $pendingDispCount }}</span>
                        @endif
                    </a>
                </li>
                @endif

                {{-- KEPALA SEKRETARIAT / SUB UNIT / BAGIAN TU --}}
                @if(in_array($role, ['kepala_sekretariat', 'sub_unit', 'bagian_tu']))
                <li class="mb-nav-item">
                    <a href="{{ route('tugas.myDisposisi') }}"
                       class="mb-nav-link {{ request()->routeIs('tugas.myDisposisi') ? 'active' : '' }}">
                        <i class="bi bi-inboxes-fill"></i>
                        <span>Disposisi Saya</span>
                        @if(isset($unreadMyDispCount) && $unreadMyDispCount > 0)
                            <span class="mb-nav-badge danger">{{ $unreadMyDispCount }}</span>
                        @endif
                    </a>
                </li>
                @endif

                {{-- TASK LOG (Semua Role Pekerja) --}}
                @if(in_array($role, ['subag_persuratan', 'kepala_unit', 'kepala_sekretariat', 'sub_unit', 'bagian_tu']))
                <li class="mb-nav-item">
                    <a href="{{ route('tugas.index') }}"
                       class="mb-nav-link {{ request()->routeIs('tugas.index') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Log Task</span>
                    </a>
                </li>
                <li class="mb-nav-item">
                    <a href="{{ route('letters.arsip') }}"
                       class="mb-nav-link {{ request()->routeIs('letters.arsip') ? 'active' : '' }}">
                        <i class="bi bi-archive-fill"></i>
                        <span>Arsip</span>
                    </a>
                </li>
                @endif

                {{-- MASTER DATA (admin only) --}}
                @if(in_array($role, ['admin', 'admin_sekretariat']))
                @php $isMoreActive = request()->routeIs('users.*', 'units.*', 'branches.*', 'organs.*'); @endphp
                <div class="sb-divider"></div>

                <li class="mb-nav-item">
                    <button class="mb-nav-toggle {{ $isMoreActive ? 'open' : '' }}"
                            id="toggleMore" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseMore"
                            aria-expanded="{{ $isMoreActive ? 'true' : 'false' }}">
                        <i class="bi bi-database-fill nav-icon"></i>
                        <span>Master Data</span>
                        <i class="bi bi-chevron-down toggle-chevron"></i>
                    </button>
                </li>
                <div class="collapse mb-nav-sub {{ $isMoreActive ? 'show' : '' }}" id="collapseMore">
                    <li class="mb-nav-item">
                        <a href="{{ route('branches.index') }}"
                           class="mb-nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                            <i class="bi bi-geo-alt-fill"></i> Cabang
                        </a>
                    </li>
                    <li class="mb-nav-item">
                        <a href="{{ route('units.index') }}"
                           class="mb-nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                            <i class="bi bi-building-fill"></i> Unit Kerja
                        </a>
                    </li>
                    <li class="mb-nav-item">
                        <a href="{{ route('organs.index') }}"
                           class="mb-nav-link {{ request()->routeIs('organs.*') ? 'active' : '' }}">
                            <i class="bi bi-diagram-3-fill"></i> Organ
                        </a>
                    </li>
                    <li class="mb-nav-item">
                        <a href="{{ route('users.index') }}"
                           class="mb-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i> Pengguna
                        </a>
                    </li>
                </div>
                @endif

            </ul>

            <!-- Footer: Profil & Logout -->
            <div style="margin-top:auto; padding-top:.5rem;">
                <div class="sb-divider"></div>
                <a href="{{ route('profile.edit') }}" class="mb-nav-link">
                    <i class="bi bi-person-circle"></i>
                    <span>Profil Saya</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="mb-nav-link border-0 bg-transparent w-100 text-start"
                            style="color:#dc2626;">
                        <i class="bi bi-box-arrow-right" style="color:#dc2626;"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ══ MAIN CONTENT ══ -->
        <main class="mb-main">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    (function () {
        const hamburger = document.getElementById('mbHamburger');
        const sidebar   = document.getElementById('mbSidebar');
        const overlay   = document.getElementById('mbOverlay');
        const hamIcon   = document.getElementById('mbHamIcon');

        if (!hamburger || !sidebar || !overlay) return;

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('show');
            hamIcon.classList.replace('bi-list', 'bi-x-lg');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            hamIcon.classList.replace('bi-x-lg', 'bi-list');
            document.body.style.overflow = '';
        }

        function toggleSidebar() {
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        }

        hamburger.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleSidebar();
        });

        overlay.addEventListener('click', closeSidebar);

        // Tutup sidebar saat klik link navigasi (mobile/tablet)
        sidebar.querySelectorAll('a.mb-nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 1024) closeSidebar();
            });
        });

        // Toggle chevron Master Data
        const toggleMore = document.getElementById('toggleMore');
        if (toggleMore) {
            toggleMore.addEventListener('click', function () {
                this.classList.toggle('open');
            });
        }

        // Tutup sidebar saat resize ke desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth > 1024) closeSidebar();
        });
    })();
    </script>

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({ toast:true, icon:'success', title:'{!! session("success") !!}', position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true });
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({ toast:true, icon:'error', title:'{!! session("error") !!}', position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true });
        });
    </script>
    @endif

    @stack('scripts')
</body>
</html>
