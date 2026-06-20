<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">

    <title>@yield('title') – Paperless Mail | YPI Al Azhar</title>

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @stack('styles')

    <style>
        :root {
            --primary-color: #0f4c81; /* Biru Dongker Premium */
            --primary-hover: #0a3d62;
            --sidebar-width: 260px;
            --bg-body: #f8fafc;
            --text-main: #334155;
            --text-muted: #64748b;
        }

        html, body {
            font-family: 'Inter', sans-serif !important;
            background-color: var(--bg-body);
            color: var(--text-main);
            overflow-x: hidden;
        }

        a { text-decoration: none; }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background-color: #ffffff;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 2px 0 20px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #f1f5f9;
        }

        .sidebar-brand img {
            height: 50px;
            object-fit: contain;
        }

        .sidebar-menu {
            padding: 1.5rem 1rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 0.75rem;
            color: var(--text-main);
            font-weight: 500;
            transition: all 0.2s;
        }

        .sidebar-item:hover {
            background-color: #f1f5f9;
            color: var(--primary-color);
        }

        .sidebar-item.active {
            background-color: var(--primary-color);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(15, 76, 129, 0.2);
        }

        .sidebar-item i {
            font-size: 1.2rem;
            margin-right: 1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid #f1f5f9;
        }

        /* Main Wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        /* Top Header (Mobile & Profile) */
        .top-header {
            background-color: transparent;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-area {
            padding: 1rem 2rem 2rem 2rem;
            flex-grow: 1;
        }

        /* UI Components */
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background-color: #ffffff;
            margin-bottom: 1.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 500;
            border-radius: 0.5rem;
            padding: 0.5rem 1.25rem;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            padding: 0.6rem 1rem;
            box-shadow: none !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(15, 76, 129, 0.1) !important;
        }

        .table-borderless-custom th {
            border-bottom: 1px solid #f1f5f9;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            padding-bottom: 1rem;
        }
        .table-borderless-custom td {
            border-bottom: 1px solid #f8fafc;
            vertical-align: middle;
            padding: 1rem 0.5rem;
        }

        .badge {
            font-weight: 500;
            padding: 0.4em 0.8em;
            border-radius: 6px;
        }

        .btn-toggle-sidebar {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-main);
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
            .btn-toggle-sidebar {
                display: block;
            }
            /* Backdrop */
            .sidebar-backdrop {
                display: none;
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.4);
                z-index: 999;
            }
            .sidebar-backdrop.show {
                display: block;
            }
            .content-area, .top-header {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Mobile Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Al Azhar">
            </a>
        </div>
        
        <div class="sidebar-menu">
            <div class="text-uppercase text-muted small fw-bold mb-2 px-3">Main Menu</div>
            
            <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>

            @php $role = Auth::user()->role; @endphp

            <a class="sidebar-item d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#collapseSuratMasuk" role="button" aria-expanded="{{ request()->routeIs('letters.inbound', 'letters.inboundExternal') ? 'true' : 'false' }}">
                <div><i class="bi bi-inbox-fill"></i> Surat Masuk</div>
                <i class="bi bi-chevron-down small text-muted"></i>
            </a>
            <div class="collapse {{ request()->routeIs('letters.inbound', 'letters.inboundExternal') ? 'show' : '' }}" id="collapseSuratMasuk">
                <div class="ps-4 ms-2 border-start border-2 border-light my-1">
                    <a href="{{ route('letters.inbound') }}" class="sidebar-item {{ request()->routeIs('letters.inbound') ? 'active' : '' }} py-2 mb-1" style="font-size: 0.9rem;">
                        Internal
                    </a>
                    <a href="{{ route('letters.inboundExternal') }}" class="sidebar-item {{ request()->routeIs('letters.inboundExternal') ? 'active' : '' }} py-2 mb-1" style="font-size: 0.9rem;">
                        Eksternal
                    </a>
                </div>
            </div>

            @if(in_array($role, ['staf_tu', 'staf_unit']))
                <a class="sidebar-item d-flex justify-content-between align-items-center mt-2" data-bs-toggle="collapse" href="#collapseSuratKeluar" role="button" aria-expanded="{{ request()->routeIs('letters.outbound', 'letters.outboundExternal') ? 'true' : 'false' }}">
                    <div><i class="bi bi-send-fill"></i> Surat Keluar</div>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </a>
                <div class="collapse {{ request()->routeIs('letters.outbound', 'letters.outboundExternal') ? 'show' : '' }}" id="collapseSuratKeluar">
                    <div class="ps-4 ms-2 border-start border-2 border-light my-1">
                        <a href="{{ route('letters.outbound') }}" class="sidebar-item {{ request()->routeIs('letters.outbound') ? 'active' : '' }} py-2 mb-1" style="font-size: 0.9rem;">
                            Internal
                        </a>
                        <a href="{{ route('letters.outboundExternal') }}" class="sidebar-item {{ request()->routeIs('letters.outboundExternal') ? 'active' : '' }} py-2 mb-1" style="font-size: 0.9rem;">
                            Eksternal
                        </a>
                    </div>
                </div>
            @endif

            <div class="text-uppercase text-muted small fw-bold mb-2 mt-4 px-3">Laporan</div>
            <a class="sidebar-item d-flex justify-content-between align-items-center {{ request()->routeIs('letters.index') ? 'active' : '' }}" data-bs-toggle="collapse" href="#collapseLaporan" role="button" aria-expanded="{{ request()->routeIs('letters.index') ? 'true' : 'false' }}">
                <div><i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan Surat</div>
                <i class="bi bi-chevron-down small text-muted"></i>
            </a>
            <div class="collapse {{ request()->routeIs('letters.index') ? 'show' : '' }}" id="collapseLaporan">
                <div class="ps-4 ms-2 border-start border-2 border-light my-1">
                    <a href="{{ route('letters.index', ['category' => 'masuk_internal']) }}" class="sidebar-item {{ request('category') == 'masuk_internal' ? 'active' : '' }} py-2 mb-1" style="font-size: 0.9rem;">
                        Surat Masuk Internal
                    </a>
                    <a href="{{ route('letters.index', ['category' => 'masuk_eksternal']) }}" class="sidebar-item {{ request('category') == 'masuk_eksternal' ? 'active' : '' }} py-2 mb-1" style="font-size: 0.9rem;">
                        Surat Masuk Eksternal
                    </a>
                    <a href="{{ route('letters.index', ['category' => 'keluar_internal']) }}" class="sidebar-item {{ request('category') == 'keluar_internal' ? 'active' : '' }} py-2 mb-1" style="font-size: 0.9rem;">
                        Surat Keluar Internal
                    </a>
                    <a href="{{ route('letters.index', ['category' => 'keluar_eksternal']) }}" class="sidebar-item {{ request('category') == 'keluar_eksternal' ? 'active' : '' }} py-2 mb-1" style="font-size: 0.9rem;">
                        Surat Keluar Eksternal
                    </a>
                </div>
            </div>

            @if($role === 'staf_tu')
                
                <a class="sidebar-item d-flex justify-content-between align-items-center mt-2" data-bs-toggle="collapse" href="#collapseMasterData" role="button" aria-expanded="{{ request()->routeIs('users.*', 'branches.*', 'units.*') ? 'true' : 'false' }}">
                    <div><i class="bi bi-database-fill"></i> Master Data</div>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </a>
                <div class="collapse {{ request()->routeIs('users.*', 'branches.*', 'units.*') ? 'show' : '' }}" id="collapseMasterData">
                    <div class="ps-4 ms-2 border-start border-2 border-light my-1">
                        <a href="{{ route('users.index') }}" class="sidebar-item {{ request()->routeIs('users.*') ? 'active' : '' }} py-2 mb-1" style="font-size: 0.9rem;">
                            Pengguna
                        </a>
                        <a href="{{ route('branches.index') }}" class="sidebar-item {{ request()->routeIs('branches.*') ? 'active' : '' }} py-2 mb-1" style="font-size: 0.9rem;">
                            Cabang
                        </a>
                        <a href="{{ route('units.index') }}" class="sidebar-item {{ request()->routeIs('units.*') ? 'active' : '' }} py-2 mb-1" style="font-size: 0.9rem;">
                            Unit Kerja
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div class="sidebar-footer">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none text-dark dropdown-toggle p-2 rounded" data-bs-toggle="dropdown" style="background:#f8fafc;">
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:35px;height:35px;font-weight:600;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="text-truncate" style="max-width:140px;">
                        <span class="d-block fw-bold" style="font-size:0.9rem;">{{ Auth::user()->name }}</span>
                        <span class="d-block text-muted" style="font-size:0.75rem;">
                            {{ str_replace('_', ' ', Auth::user()->role) }}
                        </span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-start w-100 shadow-sm border-0 mt-2">
                    <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person-fill me-2"></i> Profil Akun</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger py-2"><i class="bi bi-box-arrow-right me-2"></i> Keluar Aplikasi</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-wrapper">
        <header class="top-header">
            <div class="d-flex align-items-center">
                <button class="btn-toggle-sidebar me-3" id="btnToggleSidebar">
                    <i class="bi bi-list"></i>
                </button>
                <h4 class="mb-0 fw-bold d-none d-lg-block text-capitalize">@yield('title')</h4>
            </div>
            <div>
                <span class="text-muted d-none d-md-inline-block">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
            </div>
        </header>

        <section class="content-area">
            @yield('content')
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Sidebar Toggle Logic
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const btnToggle = document.getElementById('btnToggleSidebar');

            btnToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('show');
            });

            backdrop.addEventListener('click', () => {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
            });

            // SweetAlert Notifications
            @if(session('success'))
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: '{{ session("success") }}',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: '{{ session("error") }}',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif
        });
    </script>
    
    @stack('scripts')
</body>
</html>