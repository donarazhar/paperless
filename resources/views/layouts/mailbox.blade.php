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
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #e0e7ff;
            --bg: #f8fafc;
            --surface: #ffffff;
            --border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --sidebar-width: 260px;
            --header-height: 64px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            margin: 0;
            overflow: hidden; /* Prevent body scrolling, handle inside panes */
        }

        /* Top Header */
        .mb-header {
            height: var(--header-height);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 1rem;
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
        }

        .mb-brand {
            width: var(--sidebar-width);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--text-main);
            text-decoration: none;
        }

        .mb-brand img { width: 32px; height: 32px; border-radius: 8px; }

        .mb-search {
            flex: 1;
            max-width: 600px;
            margin: 0 2rem;
            position: relative;
        }

        .mb-search input {
            width: 100%;
            background: #f1f5f9;
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .mb-search input:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-light);
            outline: none;
        }

        .mb-search i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .mb-profile {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* App Layout */
        .mb-app {
            display: flex;
            height: 100vh;
            padding-top: var(--header-height);
        }

        /* Sidebar */
        .mb-sidebar {
            width: var(--sidebar-width);
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 1rem;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .mb-compose-btn {
            background: #c2e7ff;
            color: #001d35;
            border: none;
            border-radius: 16px;
            padding: 1rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
            text-decoration: none;
            margin-bottom: 1.5rem;
            width: fit-content;
        }

        .mb-compose-btn:hover {
            background: #b3dcf8;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            color: #001d35;
        }

        .mb-compose-btn i { font-size: 1.25rem; }

        .mb-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .mb-nav-item { margin-bottom: 0.15rem; }

        .mb-nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.65rem 1.25rem;
            border-radius: 0 100px 100px 0;
            margin-left: -1rem; /* extend to edge */
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.15s;
        }

        .mb-nav-link:hover { background: #f1f5f9; }
        .mb-nav-link.active {
            background: #d3e3fd;
            color: #041e49;
            font-weight: 700;
        }

        .mb-nav-link i { font-size: 1.1rem; color: var(--text-muted); }
        .mb-nav-link.active i { color: #041e49; }

        .mb-nav-badge {
            margin-left: auto;
            background: var(--primary);
            color: #fff;
            font-size: 0.7rem;
            padding: 0.15rem 0.4rem;
            border-radius: 10px;
        }

        .mb-section-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.05em;
            margin: 1.5rem 0 0.5rem 0.5rem;
        }

        /* Main Content */
        .mb-main {
            flex: 1;
            background: var(--surface);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            margin: 0.5rem 0.5rem 0 0;
            border-radius: 16px 16px 0 0;
            box-shadow: 0 0 10px rgba(0,0,0,0.02);
            border: 1px solid var(--border);
        }

        /* Split Pane Support */
        .mb-split {
            display: flex;
            height: 100%;
        }

        .mb-list-pane {
            flex: 1;
            min-width: 300px;
            max-width: 450px;
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

        /* Mail Item */
        .mail-item {
            display: flex;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: background 0.15s;
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
        .mail-header-row { display: flex; justify-content: space-between; margin-bottom: 0.2rem; }
        .mail-sender { font-size: 0.9rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight:inherit;}
        .mail-date { font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; }
        .mail-subject { font-size: 0.85rem; color: #0f172a; margin-bottom: 0.2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight:inherit;}
        .mail-snippet { font-size: 0.8rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight:400;}

        /* Utilities */
        .list-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .mail-scroll {
            overflow-y: auto;
            flex: 1;
        }

        @media (max-width: 768px) {
            .mb-sidebar { position: fixed; top:var(--header-height); bottom:0; z-index: 1010; transform: translateX(-100%); transition: transform 0.3s; }
            .mb-sidebar.show { transform: translateX(0); }
            .mb-list-pane { max-width: 100%; border-right: none; }
            .mb-detail-pane { display: none; } /* On mobile, detail should be full screen */
            .mb-detail-pane.show { display: flex; position: fixed; inset: 0; z-index: 1020; top: var(--header-height); }
            .mb-main { margin: 0; border-radius: 0; border-left: none; border-right: none; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php $role = Auth::user()->role ?? ''; @endphp

    <!-- Header -->
    <header class="mb-header">
        <a href="{{ route('dashboard') }}" class="mb-brand">
            <i class="bi bi-list d-md-none me-2" id="mobileMenuBtn" style="font-size: 1.5rem; cursor: pointer;"></i>
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
            <div class="d-flex flex-column" style="line-height: 1.2;">
                <span>Paperless Mail</span>
                <span class="text-muted" style="font-size: 0.65rem; font-weight: 600;">YPI Al Azhar v.1.0</span>
            </div>
        </a>
        
        <div class="mb-search">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Telusuri surat...">
        </div>

        <div class="mb-profile dropdown">
            <a href="#" class="text-muted d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration:none;">
                <div class="d-none d-md-flex flex-column text-end">
                    <span style="font-size: 0.85rem; font-weight: 700; color: #0f172a; line-height: 1.2;">{{ Auth::user()->name ?? 'User' }}</span>
                    <span style="font-size: 0.7rem; color: #64748b; line-height: 1.2; text-transform: lowercase;">{{ Auth::user()->email ?? '' }}</span>
                </div>
                <div style="width: 36px; height: 36px; border-radius: 50%; background: #4f46e5; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius: 0.75rem; margin-top: 0.5rem; min-width: 180px;">
                <li><a class="dropdown-item py-2 mt-1" href="{{ route('profile.edit') }}" style="font-size: 0.85rem;"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 mb-1 text-danger" style="font-size: 0.85rem;"><i class="bi bi-box-arrow-right me-2"></i> Keluar</button>
                    </form>
                </li>
            </ul>
        </div>
    </header>

    <!-- App Layout -->
    <div class="mb-app">
        <!-- Sidebar -->
        <aside class="mb-sidebar" id="mbSidebar">
            @if(in_array($role, ['admin_sekretariat', 'admin_unit', 'admin']))
            <a href="{{ route('letters.create') }}" class="mb-compose-btn">
                <i class="bi bi-pencil-square"></i> Tulis Surat
            </a>
            @endif

            <ul class="mb-nav">
                @if(in_array($role, ['admin_sekretariat', 'admin_unit', 'admin']))
                <li class="mb-nav-item">
                    <a href="{{ route('letters.createExternal') }}" class="mb-nav-link {{ request()->routeIs('letters.createExternal') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-plus-fill"></i> Catat Surat
                    </a>
                </li>
                <li class="mb-nav-item">
                    <a href="{{ route('letters.inbound') }}" class="mb-nav-link {{ request()->routeIs('letters.inbound*') ? 'active' : '' }}">
                        <i class="bi bi-inbox-fill"></i> Inbox
                        @if(isset($unreadInboxCount) && $unreadInboxCount > 0)
                            <span class="mb-nav-badge">{{ $unreadInboxCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="mb-nav-item">
                    <a href="{{ route('letters.drafts') }}" class="mb-nav-link {{ request()->routeIs('letters.drafts') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text-fill"></i> Draft
                        @if(isset($draftCount) && $draftCount > 0)
                            <span class="mb-nav-badge" style="background:#f59e0b; color:#fff;">{{ $draftCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="mb-nav-item">
                    <a href="{{ route('letters.outbound') }}" class="mb-nav-link {{ request()->routeIs('letters.outbound*') ? 'active' : '' }}">
                        <i class="bi bi-send-fill"></i> Outbox
                        @if(isset($pendingSendingCount) && $pendingSendingCount > 0)
                        <span class="mb-nav-badge bg-danger">{{ $pendingSendingCount }}</span>
                        @endif
                    </a>
                </li>
                @endif

                @if(in_array($role, ['subag_persuratan', 'kepala_unit']))
                <li class="mb-nav-item">
                    <a href="{{ route('tugas.accSurat') }}" class="mb-nav-link {{ request()->routeIs('tugas.accSurat') ? 'active' : '' }}">
                        <i class="bi bi-check2-circle"></i> Draft
                        @if(isset($unreadAccCount) && $unreadAccCount > 0)
                            <span class="mb-nav-badge" style="background:#dc2626;">{{ $unreadAccCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="mb-nav-item">
                    <a href="{{ route('tugas.disposisi') }}" class="mb-nav-link {{ request()->routeIs('tugas.disposisi') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-check-fill"></i> Disposisi
                        @if(isset($pendingDispCount) && $pendingDispCount > 0)
                        <span class="mb-nav-badge">{{ $pendingDispCount }}</span>
                        @endif
                    </a>
                </li>
                @endif

                @if(in_array($role, ['kepala_sekretariat', 'sub_unit', 'bagian_tu']))
                <li class="mb-nav-item">
                    <a href="{{ route('tugas.myDisposisi') }}" class="mb-nav-link {{ request()->routeIs('tugas.myDisposisi') ? 'active' : '' }}">
                        <i class="bi bi-inboxes-fill"></i> Disposisi
                        @if(isset($unreadMyDispCount) && $unreadMyDispCount > 0)
                            <span class="mb-nav-badge" style="background:#dc2626;">{{ $unreadMyDispCount }}</span>
                        @endif
                    </a>
                </li>
                @endif
                
                <li class="mb-nav-item">
                    <a href="{{ route('letters.arsip') }}" class="mb-nav-link {{ request()->routeIs('letters.arsip') ? 'active' : '' }}">
                        <i class="bi bi-archive-fill"></i> Arsip
                    </a>
                </li>

                @if(in_array($role, ['admin', 'admin_sekretariat']))
                @php
                    $isMoreActive = request()->routeIs('users.*', 'units.*', 'branches.*', 'organs.*');
                @endphp
                <li class="mb-nav-item mt-2">
                    <a class="mb-nav-link text-muted" data-bs-toggle="collapse" href="#collapseMore" role="button" aria-expanded="{{ $isMoreActive ? 'true' : 'false' }}" aria-controls="collapseMore" onclick="let i = this.querySelector('i'); i.classList.toggle('bi-chevron-down'); i.classList.toggle('bi-chevron-up');">
                        <i class="bi bi-chevron-{{ $isMoreActive ? 'up' : 'down' }}"></i> Selengkapnya
                    </a>
                </li>
                <div class="collapse {{ $isMoreActive ? 'show' : '' }}" id="collapseMore">
                    <li class="mb-nav-item">
                        <a href="{{ route('branches.index') }}" class="mb-nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                            <i class="bi bi-geo-alt-fill"></i> Kelola Cabang
                        </a>
                    </li>
                    <li class="mb-nav-item">
                        <a href="{{ route('units.index') }}" class="mb-nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                            <i class="bi bi-building-fill"></i> Kelola Unit
                        </a>
                    </li>
                    <li class="mb-nav-item">
                        <a href="{{ route('organs.index') }}" class="mb-nav-link {{ request()->routeIs('organs.*') ? 'active' : '' }}">
                            <i class="bi bi-diagram-3-fill"></i> Kelola Organ
                        </a>
                    </li>
                    <li class="mb-nav-item">
                        <a href="{{ route('users.index') }}" class="mb-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i> Kelola Pengguna
                        </a>
                    </li>
                </div>
                @endif
            </ul>
        </aside>

        <!-- Main Content (List + Detail) -->
        <main class="mb-main">
            @yield('content')
        </main>
    </div>

    <script>
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('mbSidebar').classList.toggle('show');
        });
    </script>
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
