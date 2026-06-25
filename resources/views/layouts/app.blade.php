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
            --primary:#6366f1;--primary-dark:#4f46e5;--primary-light:#e0e7ff;
            --accent:#06b6d4;--accent2:#8b5cf6;
            --bg:#f4f6fb;--text:#0f172a;--muted:#64748b;
            --border:#e8edf4;--white:#ffffff;--header-h:64px;
        }
        *,*::before,*::after{box-sizing:border-box}
        html{font-size:90%}
        html,body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);overflow-x:hidden;min-height:100vh}
        body{padding-top:var(--header-h)}
        a{text-decoration:none;color:inherit}

        /* ══ NAVBAR ══ */
        .top-nav{
            position:fixed;top:0;left:0;right:0;z-index:1030;
            height:var(--header-h);
            background:rgba(255,255,255,0.85);
            backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);
            border-bottom:1px solid rgba(0,0,0,0.06);
            box-shadow:0 1px 8px rgba(15,23,42,0.04);
            display:flex;align-items:center;
            padding:0 1.25rem;
            transition:box-shadow .3s;
        }
        .top-nav.scrolled{box-shadow:0 4px 20px rgba(15,23,42,0.08)}
        .nav-inner{display:flex;align-items:center;width:100%;max-width:1440px;margin:0 auto;gap:.5rem}

        /* Brand */
        .nav-brand{display:flex;align-items:center;gap:.6rem;margin-right:1.5rem;flex-shrink:0}
        .nav-brand img{width:34px;height:34px;object-fit:contain;border-radius:9px;border:1px solid var(--border);padding:3px;background:#fff}
        .nav-brand-name{font-size:.88rem;font-weight:800;color:var(--text);line-height:1.15;letter-spacing:-.02em}
        .nav-brand-sub{font-size:.62rem;font-weight:600;color:var(--muted)}

        /* Desktop nav links */
        .nav-links{display:flex;align-items:center;gap:.25rem;flex:1}
        .nav-link-item{
            font-size:.8rem;font-weight:600;color:var(--muted);
            padding:.45rem .75rem;border-radius:.55rem;
            display:inline-flex;align-items:center;gap:.4rem;
            transition:all .2s;cursor:pointer;position:relative;
            white-space:nowrap;border:none;background:none;
        }
        .nav-link-item:hover,.nav-link-item.active{color:var(--primary);background:var(--primary-light)}
        .nav-link-item i{font-size:.85rem}

        /* Dropdown */
        .nav-dropdown{position:relative}
        .nav-dd-menu{
            position:absolute;top:calc(100% + 6px);left:0;min-width:180px;
            background:#fff;border:1px solid var(--border);border-radius:.75rem;
            box-shadow:0 10px 30px rgba(15,23,42,0.1);padding:.35rem;
            opacity:0;visibility:hidden;transform:translateY(6px);
            transition:all .2s cubic-bezier(.16,1,.3,1);z-index:100;
        }
        .nav-dropdown:hover .nav-dd-menu,.nav-dropdown.open .nav-dd-menu{opacity:1;visibility:visible;transform:translateY(0)}
        .nav-dd-item{
            display:flex; align-items:center; font-size:.8rem;font-weight:500;color:var(--text);
            padding:.5rem .75rem;border-radius:.5rem;transition:all .15s; text-decoration:none;
        }
        .nav-dd-item:hover{background:var(--primary-light);color:var(--primary)}

        /* Profile */
        .nav-profile{margin-left:auto;position:relative;flex-shrink:0}
        .profile-trigger{
            display:flex;align-items:center;gap:.55rem;
            padding:.3rem .5rem;border-radius:.6rem;cursor:pointer;
            transition:background .2s;border:none;background:none;
        }
        .profile-trigger:hover{background:var(--primary-light)}
        .profile-avatar{
            width:32px;height:32px;border-radius:9px;
            background:linear-gradient(135deg,var(--primary),var(--accent2));
            color:#fff;font-size:.8rem;font-weight:700;
            display:flex;align-items:center;justify-content:center;flex-shrink:0;
        }
        .profile-info{line-height:1.15;display:none}
        .profile-name{font-size:.78rem;font-weight:700;color:var(--text)}
        .profile-role{font-size:.62rem;font-weight:600;color:var(--muted)}
        @media(min-width:1200px){.profile-info{display:block}}
        .profile-dd{
            position:absolute;top:calc(100% + 6px);right:0;min-width:200px;
            background:#fff;border:1px solid var(--border);border-radius:.75rem;
            box-shadow:0 10px 30px rgba(15,23,42,0.1);padding:.35rem;
            opacity:0;visibility:hidden;transform:translateY(6px);
            transition:all .2s cubic-bezier(.16,1,.3,1);z-index:100;
        }
        .nav-profile.open .profile-dd{opacity:1;visibility:visible;transform:translateY(0)}

        /* Hamburger */
        .hamburger{
            display:none;width:36px;height:36px;border:none;background:none;
            cursor:pointer;padding:0;margin-left:auto;border-radius:.5rem;
            position:relative;flex-shrink:0;
            transition:background .2s;
        }
        .hamburger:hover{background:var(--primary-light)}
        .hamburger span{
            display:block;width:20px;height:2px;background:var(--text);
            position:absolute;left:8px;border-radius:2px;
            transition:all .3s cubic-bezier(.68,-.55,.27,1.55);
        }
        .hamburger span:nth-child(1){top:10px}
        .hamburger span:nth-child(2){top:17px}
        .hamburger span:nth-child(3){top:24px}
        .hamburger.active span:nth-child(1){top:17px;transform:rotate(45deg)}
        .hamburger.active span:nth-child(2){opacity:0;transform:translateX(-8px)}
        .hamburger.active span:nth-child(3){top:17px;transform:rotate(-45deg)}

        /* Mobile overlay */
        .mobile-overlay{
            display:none;position:fixed;inset:0;background:rgba(15,23,42,0.4);
            backdrop-filter:blur(4px);z-index:1028;
            opacity:0;pointer-events:none;transition:opacity .3s;
        }
        .mobile-overlay.show{opacity:1;pointer-events:auto}

        /* Mobile menu */
        .mobile-menu{
            display:none;position:fixed;
            top:var(--header-h);left:0;right:0;bottom:0;
            background:#fff;z-index:1029;
            overflow-y:auto;padding:1rem;
            transform:translateY(-10px);opacity:0;pointer-events:none;
            transition:all .3s cubic-bezier(.16,1,.3,1);
        }
        .mobile-menu.show{transform:translateY(0);opacity:1;pointer-events:auto}
        .mobile-menu .mm-section{margin-bottom:.35rem}
        .mobile-menu .mm-link{
            display:flex;align-items:center;
            font-size:.88rem;font-weight:600;color:var(--text);
            padding:.65rem .75rem;border-radius:.6rem;transition:all .15s; text-decoration:none;
        }
        .mobile-menu .mm-link:hover,.mobile-menu .mm-link.active{background:var(--primary-light);color:var(--primary)}
        .mobile-menu .mm-link i{font-size:.95rem;width:1.25rem;text-align:center}
        .mobile-menu .mm-divider{border-top:1px solid var(--border);margin:.75rem 0}
        .mobile-menu .mm-profile{
            display:flex;align-items:center;gap:.65rem;
            padding:.75rem;background:#f8fafc;border-radius:.75rem;margin-bottom:.75rem;
        }
        .mobile-menu .mm-profile-avatar{
            width:40px;height:40px;border-radius:10px;
            background:linear-gradient(135deg,var(--primary),var(--accent2));
            color:#fff;font-size:.95rem;font-weight:700;
            display:flex;align-items:center;justify-content:center;flex-shrink:0;
        }
        .mobile-menu .mm-profile-name{font-size:.85rem;font-weight:700;color:var(--text)}
        .mobile-menu .mm-profile-email{font-size:.7rem;color:var(--muted)}

        /* Collapsible accordion */
        .mm-acc-trigger{
            display:flex;align-items:center;gap:.6rem;width:100%;
            font-size:.85rem;font-weight:700;color:var(--text);
            padding:.65rem .75rem;border-radius:.6rem;transition:all .2s;
            border:none;background:none;cursor:pointer;text-align:left;
        }
        .mm-acc-trigger:hover{background:var(--primary-light);color:var(--primary)}
        .mm-acc-trigger i.acc-icon{font-size:.95rem;width:1.25rem;text-align:center;flex-shrink:0}
        .mm-acc-trigger .acc-chevron{
            margin-left:auto;font-size:.65rem;color:var(--muted);
            transition:transform .3s cubic-bezier(.4,0,.2,1);
        }
        .mm-acc-trigger.open .acc-chevron{transform:rotate(180deg)}
        .mm-acc-trigger.open{color:var(--primary);background:var(--primary-light)}
        .mm-acc-body{
            max-height:0;overflow:hidden;
            transition:max-height .35s cubic-bezier(.4,0,.2,1),padding .3s;
            padding-left:2.25rem;
        }
        .mm-acc-body.open{max-height:300px}
        .mm-acc-body .mm-link{font-size:.82rem;font-weight:500;padding:.5rem .75rem}

        @media(max-width:991px){
            .nav-links,.nav-profile{display:none!important}
            .hamburger{display:flex;align-items:center;justify-content:center}
            .mobile-overlay,.mobile-menu{display:block}
        }

        /* ══ MAIN ══ */
        .main-wrapper{max-width:1440px;margin:0 auto;padding:1.5rem;display:flex;flex-direction:column}
        .card{border:1px solid var(--border);border-radius:1rem;box-shadow:0 1px 6px rgba(15,23,42,0.04);background:var(--white);margin-bottom:1.5rem}
        .btn-primary{background:var(--primary);border-color:var(--primary);font-weight:600;border-radius:.5rem;padding:.5rem 1.25rem;transition:all .15s}
        .btn-primary:hover{background:var(--primary-dark);border-color:var(--primary-dark);transform:translateY(-1px);box-shadow:0 4px 12px rgba(99,102,241,0.3)}
        .form-control,.form-select{border-radius:.5rem;border:1.5px solid var(--border);padding:.6rem 1rem;box-shadow:none!important;font-size:.9rem}
        .form-control:focus,.form-select:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(99,102,241,0.1)!important}
        .table-borderless-custom th{border-bottom:1px solid var(--border);color:var(--muted);font-weight:700;text-transform:uppercase;font-size:.72rem;letter-spacing:.06em;padding-bottom:.85rem}
        .table-borderless-custom td{border-bottom:1px solid #f4f6fb;vertical-align:middle;padding:.85rem .5rem}
        .badge{font-weight:600;padding:.35em .75em;border-radius:6px;font-size:.75rem}
        .dropdown-menu{border:1px solid var(--border);box-shadow:0 4px 20px rgba(15,23,42,0.06)!important;border-radius:.85rem!important;padding:.5rem;margin-top:.5rem}
        .dropdown-item{font-size:.85rem;font-weight:500;color:var(--text);border-radius:.5rem;padding:.45rem .85rem;transition:all .15s}
        .dropdown-item:hover{background:var(--primary-light);color:var(--primary)}
        @media(max-width:768px){.main-wrapper{padding:1.25rem 1rem}}
    </style>
</head>
<body>
    @php $role = Auth::user()->role ?? ''; @endphp

    <!-- ══ TOP NAVBAR ══ -->
    <nav class="top-nav" id="topNav">
        <div class="nav-inner">
            <!-- Brand -->
            <a class="nav-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Logo">
                <div>
                    <div class="nav-brand-name">Paperless Mail</div>
                    <div class="nav-brand-sub">YPI Al Azhar</div>
                </div>
            </a>

            <!-- Desktop Links -->
            <div class="nav-links">
                <a class="nav-link-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>

                @if(in_array($role, ['admin_sekretariat', 'admin_unit', 'admin']))
                <div class="nav-dropdown">
                    <button class="nav-link-item {{ request()->routeIs('letters.inbound*') ? 'active' : '' }}">
                        <i class="bi bi-envelope-arrow-down-fill"></i> Surat Masuk <i class="bi bi-chevron-down" style="font-size:.6rem;margin-left:2px"></i>
                    </button>
                    <div class="nav-dd-menu">
                        <a class="nav-dd-item" href="{{ route('letters.inbound') }}"><i class="bi bi-envelope-fill me-2" style="color:var(--primary)"></i>Masuk Internal</a>
                        <a class="nav-dd-item" href="{{ route('letters.inboundExternal') }}"><i class="bi bi-envelope-open-fill me-2" style="color:var(--primary)"></i>Masuk Eksternal</a>
                    </div>
                </div>

                <div class="nav-dropdown">
                    <button class="nav-link-item {{ request()->routeIs('letters.outbound*') ? 'active' : '' }}">
                        <i class="bi bi-send-fill"></i> Surat Keluar <i class="bi bi-chevron-down" style="font-size:.6rem;margin-left:2px"></i>
                    </button>
                    <div class="nav-dd-menu">
                        <a class="nav-dd-item" href="{{ route('letters.outbound') }}"><i class="bi bi-send-fill me-2" style="color:var(--primary)"></i>Keluar Internal</a>
                        <a class="nav-dd-item" href="{{ route('letters.outboundExternal') }}"><i class="bi bi-send-check-fill me-2" style="color:var(--primary)"></i>Keluar Eksternal</a>
                    </div>
                </div>
                @endif

                @if(in_array($role, ['subag_persuratan', 'kepala_unit']))
                <div class="nav-dropdown">
                    <button class="nav-link-item {{ request()->routeIs('tugas.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check-fill"></i> Tugas <i class="bi bi-chevron-down" style="font-size:.6rem;margin-left:2px"></i>
                    </button>
                    <div class="nav-dd-menu">
                        <a class="nav-dd-item" href="{{ route('tugas.disposisi') }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-check-fill" style="color:var(--primary)"></i>Disposisi
                            </div>
                            @if(isset($pendingDispCount) && $pendingDispCount > 0)
                                <span class="badge bg-danger ms-auto" style="padding:0.35em 0.5em; border-radius:10px;">{{ $pendingDispCount }}</span>
                            @endif
                        </a>
                        <a class="nav-dd-item" href="{{ route('tugas.accSurat') }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-pen-fill" style="color:var(--primary)"></i>ACC Surat
                            </div>
                            @if(isset($pendingAccCount) && $pendingAccCount > 0)
                                <span class="badge bg-danger ms-auto" style="padding:0.35em 0.5em; border-radius:10px;">{{ $pendingAccCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
                @endif

                @if(in_array($role, ['kepala_sekretariat', 'sub_unit', 'bagian_tu']))
                <a class="nav-link-item {{ request()->routeIs('tugas.myDisposisi') ? 'active' : '' }}" href="{{ route('tugas.myDisposisi') }}">
                    <i class="bi bi-inboxes-fill"></i> Disposisi
                </a>
                @endif

                @if(in_array($role, ['subag_persuratan', 'bagian_tu', 'admin_sekretariat']))
                <div class="nav-dropdown">
                    <button class="nav-link-item {{ request()->routeIs('letters.index') || request()->routeIs('letters.arsip') || request()->routeIs('letters.reportOutboundInternal') || request()->routeIs('letters.reportOutboundExternal') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-line-fill"></i> Laporan <i class="bi bi-chevron-down" style="font-size:.6rem;margin-left:2px"></i>
                    </button>
                    <div class="nav-dd-menu">
                        @if(in_array($role, ['subag_persuratan', 'bagian_tu']))
                        <a class="nav-dd-item" href="{{ route('letters.reportOutboundInternal') }}"><i class="bi bi-send-fill me-2" style="color:var(--primary)"></i>Surat Keluar Internal</a>
                        <a class="nav-dd-item" href="{{ route('letters.reportOutboundExternal') }}"><i class="bi bi-send-check-fill me-2" style="color:var(--primary)"></i>Surat Keluar Eksternal</a>
                        @endif
                        <a class="nav-dd-item" href="{{ route('letters.index') }}"><i class="bi bi-clock-history me-2" style="color:var(--primary)"></i>History</a>
                        @if($role === 'admin_sekretariat')
                        <a class="nav-dd-item" href="{{ route('letters.arsip') }}"><i class="bi bi-archive-fill me-2" style="color:var(--primary)"></i>Arsip Surat</a>
                        @endif
                    </div>
                </div>
                @endif

                @if(in_array($role, ['admin', 'admin_sekretariat']))
                <div class="nav-dropdown">
                    <button class="nav-link-item {{ request()->routeIs('users.*', 'branches.*', 'units.*') ? 'active' : '' }}">
                        <i class="bi bi-database-fill"></i> Master Data <i class="bi bi-chevron-down" style="font-size:.6rem;margin-left:2px"></i>
                    </button>
                    <div class="nav-dd-menu">
                        <a class="nav-dd-item" href="{{ route('branches.index') }}"><i class="bi bi-building-fill me-2" style="color:var(--primary)"></i>Cabang</a>
                        <a class="nav-dd-item" href="{{ route('units.index') }}"><i class="bi bi-diagram-3-fill me-2" style="color:var(--primary)"></i>Unit Kerja</a>
                        <a class="nav-dd-item" href="{{ route('organs.index') }}"><i class="bi bi-layers-fill me-2" style="color:var(--primary)"></i>Organ</a>
                        <a class="nav-dd-item" href="{{ route('users.index') }}"><i class="bi bi-people-fill me-2" style="color:var(--primary)"></i>Pengguna</a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Profile Desktop -->
            <div class="nav-profile" id="navProfile">
                <button class="profile-trigger" id="profileTrigger">
                    <div class="profile-avatar">{{ substr(Auth::user()->unit->name ?? 'A', 0, 1) }}</div>
                    <div class="profile-info">
                        <div class="profile-name">{{ Auth::user()->unit->name ?? 'Administrator' }}</div>
                        <div class="profile-role">{{ ucwords(str_replace('_', ' ', $role)) }}</div>
                    </div>
                    <i class="bi bi-chevron-down" style="font-size:.6rem;color:var(--muted)"></i>
                </button>
                <div class="profile-dd">
                    <a class="nav-dd-item" href="{{ route('profile.edit') }}"><i class="bi bi-person-fill me-2" style="color:var(--primary)"></i>Profil Akun</a>
                    <div style="border-top:1px solid var(--border);margin:.3rem 0"></div>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button class="nav-dd-item w-100 text-start border-0 bg-transparent" style="color:var(--error)">
                            <i class="bi bi-box-arrow-right me-2"></i>Keluar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Hamburger -->
            <button class="hamburger" id="hamburger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mm-profile">
            <div class="mm-profile-avatar">{{ substr(Auth::user()->unit->name ?? 'A', 0, 1) }}</div>
            <div>
                <div class="mm-profile-name">{{ Auth::user()->unit->name ?? 'Administrator' }}</div>
                <div class="mm-profile-email">{{ Auth::user()->email ?? '' }}</div>
            </div>
        </div>

        {{-- Dashboard (no accordion needed) --}}
        <div class="mm-section">
            <a class="mm-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        </div>

        {{-- Surat Masuk & Keluar --}}
        @if(in_array($role, ['admin_sekretariat', 'admin_unit', 'admin']))
        <div class="mm-section mm-accordion">
            <button class="mm-acc-trigger {{ request()->routeIs('letters.inbound*') ? 'open' : '' }}" data-acc="accInbound">
                <i class="bi bi-envelope-arrow-down-fill acc-icon"></i> Surat Masuk
                <i class="bi bi-chevron-down acc-chevron"></i>
            </button>
            <div class="mm-acc-body {{ request()->routeIs('letters.inbound*') ? 'open' : '' }}" id="accInbound">
                <a class="mm-link" href="{{ route('letters.inbound') }}"><i class="bi bi-envelope-fill"></i> Internal</a>
                <a class="mm-link" href="{{ route('letters.inboundExternal') }}"><i class="bi bi-envelope-open-fill"></i> Eksternal</a>
            </div>
        </div>

        <div class="mm-section mm-accordion">
            <button class="mm-acc-trigger {{ request()->routeIs('letters.outbound*') ? 'open' : '' }}" data-acc="accOutbound">
                <i class="bi bi-send-fill acc-icon"></i> Surat Keluar
                <i class="bi bi-chevron-down acc-chevron"></i>
            </button>
            <div class="mm-acc-body {{ request()->routeIs('letters.outbound*') ? 'open' : '' }}" id="accOutbound">
                <a class="mm-link" href="{{ route('letters.outbound') }}"><i class="bi bi-send-fill"></i> Internal</a>
                <a class="mm-link" href="{{ route('letters.outboundExternal') }}"><i class="bi bi-send-check-fill"></i> Eksternal</a>
            </div>
        </div>
        @endif

        {{-- Tugas --}}
        @if(in_array($role, ['subag_persuratan', 'kepala_unit']))
        <div class="mm-section mm-accordion">
            <button class="mm-acc-trigger {{ request()->routeIs('tugas.*') ? 'open' : '' }}" data-acc="accTugas">
                <i class="bi bi-clipboard-check-fill acc-icon"></i> Tugas
                <i class="bi bi-chevron-down acc-chevron"></i>
            </button>
            <div class="mm-acc-body {{ request()->routeIs('tugas.*') ? 'open' : '' }}" id="accTugas">
                <a class="mm-link" href="{{ route('tugas.disposisi') }}">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-check-fill"></i> Disposisi
                    </div>
                    @if(isset($pendingDispCount) && $pendingDispCount > 0)
                        <span class="badge bg-danger ms-auto">{{ $pendingDispCount }}</span>
                    @endif
                </a>
                <a class="mm-link" href="{{ route('tugas.accSurat') }}">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-pen-fill"></i> ACC Surat
                    </div>
                    @if(isset($pendingAccCount) && $pendingAccCount > 0)
                        <span class="badge bg-danger ms-auto">{{ $pendingAccCount }}</span>
                    @endif
                </a>
            </div>
        </div>
        @endif

        {{-- Disposisi --}}
        @if(in_array($role, ['kepala_sekretariat', 'sub_unit', 'bagian_tu']))
        <div class="mm-section">
            <a class="mm-link {{ request()->routeIs('tugas.myDisposisi') ? 'active' : '' }}" href="{{ route('tugas.myDisposisi') }}">
                <i class="bi bi-inboxes-fill"></i> Disposisi
            </a>
        </div>
        @endif

        {{-- Laporan — collapsible --}}
        @if(in_array($role, ['subag_persuratan', 'bagian_tu', 'admin_sekretariat']))
        <div class="mm-section mm-accordion">
            <button class="mm-acc-trigger {{ request()->routeIs('letters.index') || request()->routeIs('letters.arsip') || request()->routeIs('letters.reportOutboundInternal') || request()->routeIs('letters.reportOutboundExternal') ? 'open' : '' }}" data-acc="accLaporan">
                <i class="bi bi-bar-chart-line-fill acc-icon"></i> Laporan
                <i class="bi bi-chevron-down acc-chevron"></i>
            </button>
            <div class="mm-acc-body {{ request()->routeIs('letters.index') || request()->routeIs('letters.arsip') || request()->routeIs('letters.reportOutboundInternal') || request()->routeIs('letters.reportOutboundExternal') ? 'open' : '' }}" id="accLaporan">
                @if(in_array($role, ['subag_persuratan', 'bagian_tu']))
                <a class="mm-link" href="{{ route('letters.reportOutboundInternal') }}"><i class="bi bi-send-fill"></i> Surat Keluar Internal</a>
                <a class="mm-link" href="{{ route('letters.reportOutboundExternal') }}"><i class="bi bi-send-check-fill"></i> Surat Keluar Eksternal</a>
                @endif
                <a class="mm-link" href="{{ route('letters.index') }}"><i class="bi bi-clock-history"></i> History</a>
                @if($role === 'admin_sekretariat')
                <a class="mm-link" href="{{ route('letters.arsip') }}"><i class="bi bi-archive-fill"></i> Arsip Surat</a>
                @endif
            </div>
        </div>
        @endif

        {{-- Master Data — collapsible --}}
        @if(in_array($role, ['admin', 'admin_sekretariat']))
        <div class="mm-section mm-accordion">
            <button class="mm-acc-trigger {{ request()->routeIs('users.*', 'branches.*', 'units.*', 'organs.*') ? 'open' : '' }}" data-acc="accMaster">
                <i class="bi bi-database-fill acc-icon"></i> Master Data
                <i class="bi bi-chevron-down acc-chevron"></i>
            </button>
            <div class="mm-acc-body {{ request()->routeIs('users.*', 'branches.*', 'units.*', 'organs.*') ? 'open' : '' }}" id="accMaster">
                <a class="mm-link" href="{{ route('branches.index') }}"><i class="bi bi-building-fill"></i> Cabang</a>
                <a class="mm-link" href="{{ route('units.index') }}"><i class="bi bi-diagram-3-fill"></i> Unit Kerja</a>
                <a class="mm-link" href="{{ route('organs.index') }}"><i class="bi bi-layers-fill"></i> Organ</a>
                <a class="mm-link" href="{{ route('users.index') }}"><i class="bi bi-people-fill"></i> Pengguna</a>
            </div>
        </div>
        @endif

        <div class="mm-divider"></div>
        <a class="mm-link" href="{{ route('profile.edit') }}"><i class="bi bi-person-fill"></i> Profil Akun</a>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="mm-link w-100 text-start border-0 bg-transparent" style="color:#ef4444">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </button>
        </form>
    </div>

    <!-- ══ MAIN CONTENT ══ -->
    <main class="main-wrapper">
        <div class="w-100">@yield('content')</div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const hamburger=document.getElementById('hamburger');
        const menu=document.getElementById('mobileMenu');
        const overlay=document.getElementById('mobileOverlay');
        const profile=document.getElementById('navProfile');
        const profileTrigger=document.getElementById('profileTrigger');

        function toggleMenu(){
            const isOpen=hamburger.classList.toggle('active');
            if(isOpen){menu.classList.add('show');overlay.classList.add('show');document.body.style.overflow='hidden'}
            else{menu.classList.remove('show');overlay.classList.remove('show');document.body.style.overflow=''}
        }
        hamburger.addEventListener('click',toggleMenu);
        overlay.addEventListener('click',toggleMenu);

        // Close on link click
        menu.querySelectorAll('a.mm-link').forEach(function(a){a.addEventListener('click',function(){if(hamburger.classList.contains('active'))toggleMenu()})});

        // Collapsible accordion toggles
        menu.querySelectorAll('.mm-acc-trigger').forEach(function(trigger){
            trigger.addEventListener('click', function(){
                var targetId = this.getAttribute('data-acc');
                var body = document.getElementById(targetId);
                var isOpen = this.classList.contains('open');
                // Close all other accordions
                menu.querySelectorAll('.mm-acc-trigger').forEach(function(t){
                    if(t !== trigger){ t.classList.remove('open'); }
                });
                menu.querySelectorAll('.mm-acc-body').forEach(function(b){
                    if(b !== body){ b.classList.remove('open'); }
                });
                // Toggle current
                this.classList.toggle('open', !isOpen);
                body.classList.toggle('open', !isOpen);
            });
        });

        // Profile dropdown toggle
        if(profileTrigger){profileTrigger.addEventListener('click',function(e){e.stopPropagation();profile.classList.toggle('open')})}
        document.addEventListener('click',function(){if(profile)profile.classList.remove('open')});

        // Scroll shadow
        window.addEventListener('scroll',function(){document.getElementById('topNav').classList.toggle('scrolled',window.scrollY>10)});
    });
    </script>
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({toast:true,icon:'success',title:'{!! session("success") !!}',position:'top-end',showConfirmButton:false,timer:3000,timerProgressBar:true});
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({toast:true,icon:'error',title:'{!! session("error") !!}',position:'top-end',showConfirmButton:false,timer:3000,timerProgressBar:true});
        });
    </script>
    @endif
    @stack('scripts')
</body>
</html>