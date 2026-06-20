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
    <link href="{{ asset('css/modern-admin.css') }}" rel="stylesheet">
    @stack('styles')
    <style>
        :root {
            --blue:        #2563eb;
            --blue-dark:   #1d4ed8;
            --blue-soft:   #eff6ff;
            --blue-mid:    #dbeafe;
            --bg:          #f4f6fb;
            --text:        #0f172a;
            --muted:       #64748b;
            --border:      #e8edf4;
            --white:       #ffffff;
            --header-h:    72px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow-x: hidden;
            min-height: 100vh;
        }
        
        body {
            padding-top: var(--header-h);
        }

        a { text-decoration: none; color: inherit; }

        /* ═══════════════════════════
           TOP NAVBAR
        ═══════════════════════════ */
        .top-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            min-height: var(--header-h);
            box-shadow: 0 1px 12px rgba(15,23,42,0.03);
            padding: 0.5rem 0;
        }

        .navbar-brand img {
            width: 38px; height: 38px;
            object-fit: contain;
            border-radius: 10px;
            border: 1px solid var(--border);
            padding: 4px;
            background: #fff;
        }

        .nav-link {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--muted);
            padding: 0.6rem 0.85rem !important;
            border-radius: 0.6rem;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
        }

        .nav-link:hover, .nav-link:focus {
            color: var(--blue);
            background: var(--blue-soft);
        }

        .nav-link.active {
            color: var(--blue);
            background: var(--blue-soft);
        }

        .dropdown-menu {
            border: 1px solid var(--border);
            box-shadow: 0 4px 20px rgba(15,23,42,0.06) !important;
            border-radius: 0.85rem !important;
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        .dropdown-item {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text);
            border-radius: 0.5rem;
            padding: 0.45rem 0.85rem;
            transition: all .15s;
        }

        .dropdown-item:hover {
            background: var(--blue-soft);
            color: var(--blue);
        }

        /* Profile */
        .sb-avatar {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--blue), #7c3aed);
            color: #fff;
            font-size: 0.9rem;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .profile-btn {
            padding: 0.35rem 0.6rem;
            border-radius: 0.65rem;
            transition: background .2s;
        }
        .profile-btn:hover { background: var(--blue-soft); }

        /* ═══════════════════════════
           MAIN WRAPPER
        ═══════════════════════════ */
        .main-wrapper {
            max-width: 1440px;
            margin: 0 auto;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
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

        @media (max-width: 991px) {
            .navbar-collapse {
                background: #fff;
                padding: 1rem;
                border-radius: 1rem;
                box-shadow: 0 10px 25px rgba(15,23,42,0.05);
                border: 1px solid var(--border);
                margin-top: 1rem;
            }
            .nav-link { margin-bottom: 0.25rem; }
        }

        @media (max-width: 768px) {
            .main-wrapper { padding: 1.25rem 1rem; }
        }
    </style>
</head>

<body>
    @php $role = Auth::user()->role ?? ''; @endphp

    <!-- ═══ TOP NAVBAR ═══ -->
    <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container-fluid px-3 px-xl-4">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center gap-2 me-lg-4" href="{{ route('dashboard') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Logo">
                <div>
                    <div style="font-size:0.95rem;font-weight:800;line-height:1.2;color:var(--text);letter-spacing:-0.03em;">Paperless Mail</div>
                    <div style="font-size:0.68rem;font-weight:600;color:var(--muted);">YPI Al Azhar</div>
                </div>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" style="box-shadow:none;">
                <i class="bi bi-list fs-2" style="color:var(--text);"></i>
            </button>

            <!-- Nav Items -->
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
                        </a>
                    </li>
                    
                    <!-- Dropdown Surat Masuk -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('letters.inbound*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-envelope-arrow-down-fill me-2"></i> Surat Masuk
                        </a>
                        <ul class="dropdown-menu border-0">
                            <li><a class="dropdown-item" href="{{ route('letters.inbound') }}">Masuk Internal</a></li>
                            <li><a class="dropdown-item" href="{{ route('letters.inboundExternal') }}">Masuk Eksternal</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Surat Keluar -->
                    @if(in_array($role, ['staf_tu', 'staf_unit']))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('letters.outbound*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-send-fill me-2"></i> Surat Keluar
                        </a>
                        <ul class="dropdown-menu border-0">
                            <li><a class="dropdown-item" href="{{ route('letters.outbound') }}">Keluar Internal</a></li>
                            <li><a class="dropdown-item" href="{{ route('letters.outboundExternal') }}">Keluar Eksternal</a></li>
                        </ul>
                    </li>
                    @endif
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('letters.index') || request()->routeIs('letters.arsip') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-bar-chart-line-fill me-2"></i> Laporan
                        </a>
                        <ul class="dropdown-menu border-0">
                            <li><a class="dropdown-item" href="{{ route('letters.index') }}">Laporan Surat</a></li>
                            <li><a class="dropdown-item" href="{{ route('letters.arsip') }}">Arsip Surat</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Master Data -->
                    @if($role === 'staf_tu')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('users.*', 'branches.*', 'units.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-database-fill me-2"></i> Master Data
                        </a>
                        <ul class="dropdown-menu border-0">
                            <li><a class="dropdown-item" href="{{ route('branches.index') }}">Cabang</a></li>
                            <li><a class="dropdown-item" href="{{ route('units.index') }}">Unit Kerja</a></li>
                            <li><a class="dropdown-item" href="{{ route('users.index') }}">Pengguna</a></li>
                        </ul>
                    </li>
                    @endif
                </ul>

                <!-- Profile -->
                <div class="d-flex align-items-center mt-3 mt-lg-0 border-top border-lg-0 pt-3 pt-lg-0 ms-lg-3">
                    <div class="dropdown">
                        <div class="d-flex align-items-center gap-2 profile-btn" data-bs-toggle="dropdown" style="cursor:pointer;">
                            <div class="sb-avatar">{{ substr(Auth::user()->unit->name ?? 'A', 0, 1) }}</div>
                            <div class="d-none d-xl-block" style="line-height:1.2;">
                                <div style="font-size:0.82rem;font-weight:700;color:var(--text);">
                                    {{ Auth::user()->unit->name ?? 'Administrator' }}
                                    <span style="font-weight:600; color:var(--blue); font-size:0.7rem; margin-left:2px;">
                                        &bull; {{ ucwords(str_replace('_', ' ', Auth::user()->role ?? '')) }}
                                    </span>
                                </div>
                                <div style="font-size:0.65rem;font-weight:600;color:var(--muted); margin-top:2px;">{{ Auth::user()->email ?? '' }}</div>
                            </div>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
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
            </div>
        </div>
    </nav>

    <!-- ═══ MAIN CONTENT ═══ -->
    <main class="main-wrapper">
        <!-- Optional: We can add page header inside content area if needed, but since titles were moved out, we rely on yield -->
        <div class="w-100">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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