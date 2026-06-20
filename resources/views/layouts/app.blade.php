<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <title>@yield('title') – Paperless Mail | YPI Al Azhar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @stack('styles')

    <style>
        :root {
            --blue:        #2563eb;
            --blue-dark:   #1d4ed8;
            --blue-soft:   #eff6ff;
            --blue-mid:    #dbeafe;
            --sidebar-w:   268px;
            --bg:          #f4f6fb;
            --text:        #0f172a;
            --muted:       #64748b;
            --border:      #e8edf4;
            --white:       #ffffff;
            --header-h:    64px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow-x: hidden;
            height: 100%;
        }

        a { text-decoration: none; color: inherit; }

        /* ═══════════════════════════
           SIDEBAR
        ═══════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--white);
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--border);
            transition: transform .3s cubic-bezier(.4,0,.2,1);
        }

        /* Brand */
        .sb-brand {
            height: var(--header-h);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0 1.25rem;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }

        .sb-brand a {
            display: flex; align-items: center; gap: 0.75rem;
        }

        .sb-brand img {
            width: 34px; height: 34px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid var(--border);
            padding: 3px;
        }

        .sb-brand .brand-text {
            line-height: 1.2;
        }
        .sb-brand .brand-name {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.02em;
        }
        .sb-brand .brand-sub {
            font-size: 0.68rem;
            color: var(--muted);
            font-weight: 500;
        }

        /* Scroll area */
        .sb-scroll {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.1rem 0.85rem;
            scrollbar-width: thin;
            scrollbar-color: #e2e8f0 transparent;
        }

        .sb-scroll::-webkit-scrollbar { width: 4px; }
        .sb-scroll::-webkit-scrollbar-track { background: transparent; }
        .sb-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        /* Section label */
        .sb-section {
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #b0bcd0;
            padding: 0 0.6rem;
            margin: 1.25rem 0 0.5rem;
        }

        .sb-section:first-child { margin-top: 0; }

        /* Nav item */
        .sb-item {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.6rem 0.75rem;
            border-radius: 0.6rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--muted);
            cursor: pointer;
            transition: background .15s, color .15s;
            margin-bottom: 2px;
            width: 100%;
            border: none;
            background: none;
            text-align: left;
            line-height: 1;
        }

        .sb-item .sb-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
            transition: background .15s, color .15s;
        }

        .sb-item:hover {
            background: var(--blue-soft);
            color: var(--blue);
        }

        .sb-item:hover .sb-icon {
            background: var(--blue-mid);
            color: var(--blue);
        }

        .sb-item.active {
            background: var(--blue-soft);
            color: var(--blue);
            font-weight: 600;
        }

        .sb-item.active .sb-icon {
            background: var(--blue);
            color: #fff;
        }

        .sb-item .sb-label { flex: 1; }

        .sb-item .sb-chevron {
            font-size: 0.65rem;
            color: #c0cad8;
            transition: transform .2s;
        }

        .sb-item[aria-expanded="true"] .sb-chevron { transform: rotate(180deg); }

        /* Sub-items */
        .sb-sub {
            padding-left: 2.75rem;
        }

        .sb-sub .sb-item {
            font-size: 0.835rem;
            padding: 0.5rem 0.6rem;
            border-radius: 0.5rem;
        }

        .sb-sub .sb-item .sb-icon {
            width: 24px; height: 24px;
            border-radius: 6px;
            font-size: 0.8rem;
        }

        /* Profile footer */
        .sb-footer {
            padding: 0.85rem;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        .sb-profile {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 0.75rem;
            border-radius: 0.65rem;
            cursor: pointer;
            transition: background .15s;
        }

        .sb-profile:hover { background: var(--blue-soft); }

        .sb-avatar {
            width: 34px; height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--blue), #7c3aed);
            color: #fff;
            font-size: 0.85rem;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .sb-profile .sb-profile-name {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }

        .sb-profile .sb-profile-role {
            font-size: 0.7rem;
            color: var(--muted);
            font-weight: 500;
        }

        /* ═══════════════════════════
           HEADER
        ═══════════════════════════ */
        .top-header {
            position: sticky;
            top: 0;
            z-index: 100;
            height: var(--header-h);
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.75rem;
            gap: 1rem;
        }

        .hdr-left { display: flex; align-items: center; gap: 0.75rem; }

        .hdr-toggle {
            width: 36px; height: 36px;
            border-radius: 9px;
            border: 1px solid var(--border);
            background: var(--white);
            display: none;
            align-items: center; justify-content: center;
            font-size: 1.15rem;
            color: var(--muted);
            cursor: pointer;
            transition: background .15s, color .15s;
        }

        .hdr-toggle:hover { background: var(--blue-soft); color: var(--blue); }

        .hdr-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.02em;
        }

        .hdr-date {
            font-size: 0.8rem;
            color: var(--muted);
            font-weight: 500;
        }

        /* ═══════════════════════════
           MAIN WRAPPER
        ═══════════════════════════ */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left .3s cubic-bezier(.4,0,.2,1);
        }

        .content-area {
            padding: 1.5rem 1.75rem 2.5rem;
            flex: 1;
        }

        /* ═══════════════════════════
           GLOBAL COMPONENTS
        ═══════════════════════════ */
        .card {
            border: 1px solid var(--border);
            border-radius: 1rem;
            box-shadow: 0 1px 6px rgba(15,23,42,0.04);
            background: var(--white);
            margin-bottom: 1.5rem;
        }

        .btn-primary {
            background: var(--blue);
            border-color: var(--blue);
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.5rem 1.25rem;
            transition: background .15s, transform .1s, box-shadow .15s;
        }

        .btn-primary:hover {
            background: var(--blue-dark);
            border-color: var(--blue-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37,99,235,0.25);
        }

        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1.5px solid var(--border);
            padding: 0.6rem 1rem;
            box-shadow: none !important;
            font-size: 0.9rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1) !important;
        }

        .table-borderless-custom th {
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.06em;
            padding-bottom: 0.85rem;
        }

        .table-borderless-custom td {
            border-bottom: 1px solid #f4f6fb;
            vertical-align: middle;
            padding: 0.85rem 0.5rem;
        }

        .badge {
            font-weight: 600;
            padding: 0.35em 0.75em;
            border-radius: 6px;
            font-size: 0.75rem;
        }

        /* ═══════════════════════════
           BACKDROP
        ═══════════════════════════ */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,0.45);
            z-index: 1040;
            backdrop-filter: blur(2px);
        }

        .sidebar-backdrop.show { display: block; }

        /* ═══════════════════════════
           RESPONSIVE — TABLET/MOBILE
        ═══════════════════════════ */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(calc(-1 * var(--sidebar-w))); box-shadow: none; }
            .sidebar.show { transform: translateX(0); box-shadow: 8px 0 32px rgba(15,23,42,0.12); }
            .main-wrapper { margin-left: 0 !important; }
            .hdr-toggle { display: flex; }
        }

        @media (max-width: 768px) {
            .content-area { padding: 1.25rem 1rem 2rem; }
            .top-header { padding: 0 1rem; }
        }

        @media (max-width: 480px) {
            :root { --sidebar-w: 100vw; }
            .hdr-title { font-size: 0.9rem; }
            .content-area { padding: 1rem 0.875rem 2rem; }
        }
    </style>
</head>

<body>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- ═══ SIDEBAR ═══ -->
    <aside class="sidebar" id="sidebar">

        <!-- Brand -->
        <div class="sb-brand">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Logo">
                <div class="brand-text">
                    <div class="brand-name">Paperless Mail</div>
                    <div class="brand-sub">YPI Al Azhar</div>
                </div>
            </a>
        </div>

        <!-- Menu Scroll -->
        <div class="sb-scroll">
            @php $role = Auth::user()->role; @endphp

            {{-- UTAMA --}}
            <div class="sb-section">Menu Utama</div>

            <a href="{{ route('dashboard') }}" class="sb-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="sb-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                <span class="sb-label">Dashboard</span>
            </a>

            {{-- ── SURAT MASUK ── --}}
            <div class="sb-section">Surat Masuk</div>

            <a href="{{ route('letters.inbound') }}" class="sb-item {{ request()->routeIs('letters.inbound') ? 'active' : '' }}">
                <span class="sb-icon"><i class="bi bi-envelope-arrow-down-fill"></i></span>
                <span class="sb-label">Masuk Internal</span>
            </a>

            <a href="{{ route('letters.inboundExternal') }}" class="sb-item {{ request()->routeIs('letters.inboundExternal') ? 'active' : '' }}">
                <span class="sb-icon"><i class="bi bi-envelope-exclamation-fill"></i></span>
                <span class="sb-label">Masuk Eksternal</span>
            </a>

            {{-- ── SURAT KELUAR (staf_tu & staf_unit) ── --}}
            @if(in_array($role, ['staf_tu', 'staf_unit']))
                <div class="sb-section">Surat Keluar</div>

                <a href="{{ route('letters.outbound') }}" class="sb-item {{ request()->routeIs('letters.outbound') ? 'active' : '' }}">
                    <span class="sb-icon"><i class="bi bi-send-fill"></i></span>
                    <span class="sb-label">Keluar Internal</span>
                </a>

                <a href="{{ route('letters.outboundExternal') }}" class="sb-item {{ request()->routeIs('letters.outboundExternal') ? 'active' : '' }}">
                    <span class="sb-icon"><i class="bi bi-send-arrow-up-fill"></i></span>
                    <span class="sb-label">Keluar Eksternal</span>
                </a>
            @endif

            {{-- ── LAPORAN ── --}}
            <div class="sb-section">Laporan</div>

            <a href="{{ route('letters.index') }}" class="sb-item {{ request()->routeIs('letters.index') ? 'active' : '' }}">
                <span class="sb-icon"><i class="bi bi-bar-chart-line-fill"></i></span>
                <span class="sb-label">Laporan Surat</span>
            </a>

            {{-- ── MASTER DATA (staf_tu only) ── --}}
            @if($role === 'staf_tu')
                <div class="sb-section">Master Data</div>

                <a href="{{ route('users.index') }}" class="sb-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <span class="sb-icon"><i class="bi bi-people-fill"></i></span>
                    <span class="sb-label">Pengguna</span>
                </a>

                <a href="{{ route('branches.index') }}" class="sb-item {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                    <span class="sb-icon"><i class="bi bi-building-fill"></i></span>
                    <span class="sb-label">Cabang</span>
                </a>

                <a href="{{ route('units.index') }}" class="sb-item {{ request()->routeIs('units.*') ? 'active' : '' }}">
                    <span class="sb-icon"><i class="bi bi-diagram-3-fill"></i></span>
                    <span class="sb-label">Unit Kerja</span>
                </a>
            @endif
        </div>

        <!-- Profile Footer -->
        <div class="sb-footer">
            <div class="dropdown">
                <div class="sb-profile" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="sb-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                    <div style="flex:1; overflow:hidden;">
                        <div class="sb-profile-name text-truncate">{{ Auth::user()->name }}</div>
                        <div class="sb-profile-role">{{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}</div>
                    </div>
                    <i class="bi bi-chevron-up" style="font-size:0.65rem; color:#b0bcd0;"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-start shadow border-0 w-100 mb-1" style="border-radius:0.75rem; font-size:0.875rem;">
                    <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person-fill me-2 text-primary"></i>Profil Akun</a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item py-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </aside>

    <!-- ═══ MAIN ═══ -->
    <main class="main-wrapper">

        <!-- Top Header -->
        <header class="top-header">
            <div class="hdr-left">
                <button class="hdr-toggle" id="btnToggleSidebar">
                    <i class="bi bi-list"></i>
                </button>
                <span class="hdr-title">@yield('title')</span>
            </div>
            <span class="hdr-date d-none d-sm-inline">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
        </header>

        <section class="content-area">
            @yield('content')
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar  = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const btnToggle = document.getElementById('btnToggleSidebar');

            function openSidebar() {
                sidebar.classList.add('show');
                backdrop.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
            function closeSidebar() {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
                document.body.style.overflow = '';
            }

            btnToggle.addEventListener('click', () => sidebar.classList.contains('show') ? closeSidebar() : openSidebar());
            backdrop.addEventListener('click', closeSidebar);

            // Close sidebar on nav link click (mobile)
            sidebar.querySelectorAll('.sb-item[href]').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 1024) closeSidebar();
                });
            });

            // SweetAlert Notifications
            @if(session('success'))
                Swal.fire({ toast:true, icon:'success', title:'{{ session("success") }}', position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true });
            @endif
            @if(session('error'))
                Swal.fire({ toast:true, icon:'error', title:'{{ session("error") }}', position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true });
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>