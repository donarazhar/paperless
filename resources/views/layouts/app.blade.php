<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">


    <title>@yield('title') – MailApp | Bank Jateng Syariah Pekalongan</title>


    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Global font */
        html,
        body {
            font-family: 'Inter', Arial, sans-serif !important;
            background-color: #f8f9fa;
        }

        /* Navbar styling */
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .nav-link {
            font-weight: 500;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        /* Buttons */
        .btn-primary {
            background-color: #005f9e;
            border-color: #005f9e;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #004a7c;
            border-color: #004a7c;
        }

        /* Tables */
        .table thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }

        /* Form controls */
        .form-control,
        .form-select {
            border-radius: 0.375rem;
        }

        /* Utility: hover shadow */
        .hover-shadow:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .brand-logo {
            width: 58px;
            height: 58px;
            object-fit: contain;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <img src="https://bucket-api.baznas.go.id/bucket-api/file?bucket=bzn-fdr-smb-p5739641&file=attachments/rekening/172292650008666680_497-v2-Bank-Jateng-Syariah.png"
                    class="brand-logo mb-2"
                    alt="Logo Bank Jateng"
                    style="height: 60px; width: auto;"> 
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            
            </button>

            {{-- Menu --}}
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @php $role = Auth::user()->role; @endphp

                    {{-- Dashboard: semua role --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    {{-- Admin --}}
                    @if($role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('letters.index') ? 'active' : '' }}"
                                href="{{ route('letters.index') }}">
                                <i class="bi bi-envelope-fill"></i> All Letters
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                                href="{{ route('users.index') }}">
                                <i class="bi bi-people-fill"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}"
                                href="{{ route('units.index') }}">
                                <i class="bi bi-diagram-3-fill"></i> Units
                            </a>
                        </li>

                        {{-- Manager --}}
                    @elseif($role === 'manager')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('letters.inbound') ? 'active' : '' }}"
                                href="{{ route('letters.inbound') }}">
                                <i class="bi bi-inbox-fill"></i> Surat Masuk
                            </a>
                        </li>

                        {{-- Staff --}}
                    @elseif($role === 'staff')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('letters.inbound') ? 'active' : '' }}"
                                href="{{ route('letters.inbound') }}">
                                <i class="bi bi-inbox-fill"></i> Surat Masuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('letters.outbound') ? 'active' : '' }}"
                                href="{{ route('letters.outbound') }}">
                                <i class="bi bi-send-fill"></i> Surat Keluar
                            </a>
                        </li>
                    @endif

                    {{-- Profil & Logout --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                            href="#" id="userMenu" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                                    href="{{ route('profile.edit') }}">
                                    <i class="bi bi-gear"></i> Profil
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4 container">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
</body>

</html>