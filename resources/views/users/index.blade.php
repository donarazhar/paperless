@extends('layouts.mailbox')
@section('title', 'Manajemen Pengguna')

@section('content')
<style>
    /* Modern Dashboard Styling */
    .page-container { padding: 2rem; max-width: 1400px; margin: 0 auto; width: 100%; }
    
    /* Header Section */
    .hero-card { 
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); 
        border-radius: 1.5rem; 
        padding: 2.5rem; 
        color: white; 
        position: relative; 
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(79,70,229,0.2);
    }
    .hero-card::after {
        content: ''; position: absolute; right: 0; top: 0; width: 50%; height: 100%;
        background: url('data:image/svg+xml;utf8,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.05)"/></svg>') repeat;
        background-size: 100px; opacity: 0.5; pointer-events: none;
    }
    .hero-title { font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; letter-spacing: -0.02em; }
    .hero-sub { font-size: 1rem; color: rgba(255,255,255,0.8); }
    .btn-hero { 
        background: #ffffff; color: #4f46e5; border-radius: 100px; padding: 0.75rem 1.5rem; 
        font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; 
        transition: all 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
    }
    .btn-hero:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,0.15); color: #4338ca; }

    /* Filter Card */
    .filter-card {
        background: #ffffff; border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.04);
        display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;
    }
    .search-input {
        background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; 
        padding: 0.75rem 1rem 0.75rem 2.5rem; width: 100%; max-width: 400px; font-weight: 500; transition: all 0.2s;
    }
    .search-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79,70,229,0.1); outline: none; background: #ffffff; }
    .search-wrapper { position: relative; flex-grow: 1; max-width: 400px; }
    .search-wrapper i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .btn-search { background: #0f172a; color: #fff; border: none; border-radius: 0.75rem; padding: 0.75rem 1.5rem; font-weight: 600; transition: all 0.2s; }
    .btn-search:hover { background: #1e293b; color: #fff; transform: translateY(-1px); }
    .btn-reset { background: #f1f5f9; color: #475569; text-decoration: none; border-radius: 0.75rem; padding: 0.75rem 1.5rem; font-weight: 600; transition: all 0.2s; }
    .btn-reset:hover { background: #e2e8f0; color: #0f172a; }

    /* Table Styling */
    .table-container {
        background: #ffffff; border-radius: 1.5rem; overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.04);
    }
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .modern-table th {
        background: #f8fafc; padding: 1.25rem 1.5rem; font-weight: 700; color: #64748b; 
        font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;
    }
    .modern-table td { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .modern-table tbody tr { transition: all 0.2s; }
    .modern-table tbody tr:hover { background: #f8fafc; }
    .modern-table tbody tr:last-child td { border-bottom: none; }

    /* User Avatar & Info */
    .user-avatar { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem; flex-shrink: 0; }
    .user-name { font-weight: 700; font-size: 0.95rem; color: #0f172a; margin-bottom: 0.15rem; }
    .user-email { font-size: 0.8rem; color: #64748b; }

    /* Role Pills */
    .role-pill { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.8rem; border-radius: 100px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; }
    .rp-admin { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
    .rp-staftu { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .rp-kasubag { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .rp-kepala { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .rp-stafunit { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
    .rp-default { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

    /* Action Buttons */
    .action-btn { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px; border: none; transition: all 0.2s; text-decoration: none; }
    .btn-edit { background: #eef2ff; color: #4f46e5; }
    .btn-edit:hover { background: #4f46e5; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(79,70,229,0.2); }
    .btn-del { background: #fef2f2; color: #dc2626; }
    .btn-del:hover { background: #dc2626; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(220,38,38,0.2); }
    .btn-lock { background: #f8fafc; color: #94a3b8; font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem; }

    /* Empty State */
    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-state i { font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem; display: block; }
    .empty-title { font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
    .empty-desc { color: #64748b; font-size: 0.95rem; }

    /* Mobile Cards */
    .mobile-cards { display: none; }
    .m-card { background: #fff; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.04); }

    @media (max-width: 992px) {
        .table-container { display: none; }
        .mobile-cards { display: block; }
    }
</style>

@php
    function rolePill($role) {
        return match($role) {
            'admin'              => ['class'=>'rp-admin',   'label'=>'Admin',             'icon'=>'bi-shield-fill-check'],
            'admin_sekretariat'  => ['class'=>'rp-staftu',  'label'=>'Admin Sekre',       'icon'=>'bi-calendar-plus'],
            'subag_persuratan'   => ['class'=>'rp-staftu',  'label'=>'Subag Persuratan',  'icon'=>'bi-envelope-paper'],
            'bagian_tu'          => ['class'=>'rp-kasubag', 'label'=>'Bagian TU',         'icon'=>'bi-diagram-3-fill'],
            'kepala_sekretariat' => ['class'=>'rp-kepala',  'label'=>'Kepala Sekre',      'icon'=>'bi-star-fill'],
            'admin_unit'         => ['class'=>'rp-stafunit','label'=>'Admin Unit',        'icon'=>'bi-person-gear'],
            'kepala_unit'        => ['class'=>'rp-kasubag', 'label'=>'Kepala Unit',       'icon'=>'bi-person-badge-fill'],
            'sub_unit'           => ['class'=>'rp-stafunit','label'=>'Sub Unit',          'icon'=>'bi-person-check-fill'],
            default              => ['class'=>'rp-default', 'label'=>ucfirst($role),    'icon'=>'bi-person'],
        };
    }
    function avatarColor($name) {
        $colors = [['#e0e7ff','#4f46e5'],['#ede9fe','#7c3aed'],['#dcfce7','#15803d'],['#fce7f3','#be185d'],['#fef9c3','#a16207'],['#ffedd5','#c2410c']];
        return $colors[abs(crc32($name)) % count($colors)];
    }
@endphp

<div class="mail-scroll">
    <div class="page-container">
        
        {{-- Hero Header --}}
        <div class="hero-card">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-4" style="position:relative; z-index:2;">
                <div>
                    <h1 class="hero-title">Manajemen Pengguna</h1>
                    <p class="hero-sub mb-0">Kelola akun dan hak akses pengguna sistem persuratan</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div style="background: rgba(255,255,255,0.1); padding: 0.5rem 1rem; border-radius: 100px; font-weight: 600; font-size: 0.9rem;">
                        <i class="bi bi-people-fill me-2"></i> {{ $users->count() }} Pengguna
                    </div>
                    <a href="{{ route('users.create') }}" class="btn-hero">
                        <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
                    </a>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <form method="GET" action="{{ route('users.index') }}" class="filter-card">
            <div class="search-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="search-input" placeholder="Cari nama atau email..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn-search"><i class="bi bi-funnel-fill me-2"></i>Filter</button>
            @if(request('search'))
                <a href="{{ route('users.index') }}" class="btn-reset"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset</a>
            @endif
        </form>

        {{-- Desktop Table --}}
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th>Profil Pengguna</th>
                        <th>Hak Akses (Role)</th>
                        <th>Cabang & Unit</th>
                        <th style="text-align: right; padding-right: 2rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $rp    = rolePill($user->role);
                            $av    = avatarColor($user->name);
                            $inits = mb_strtoupper(mb_substr($user->name, 0, 1));
                        @endphp
                        <tr>
                            <td style="text-align: center; color: #94a3b8; font-weight: 600; font-size: 0.85rem;">
                                {{ $users->firstItem() + $loop->index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar" style="background:{{ $av[0] }}; color:{{ $av[1] }};">{{ $inits }}</div>
                                    <div>
                                        <div class="user-name">{{ $user->name }}</div>
                                        <div class="user-email">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="role-pill {{ $rp['class'] }}">
                                    <i class="bi {{ $rp['icon'] }}"></i> {{ $rp['label'] }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #334155; font-size: 0.9rem;">{{ $user->unit->branch->name ?? '—' }}</div>
                                <div style="color: #64748b; font-size: 0.8rem; margin-top: 0.2rem;">{{ $user->unit->name ?? '—' }} <span style="opacity:0.5">•</span> {{ $user->organ->name ?? '' }}</div>
                            </td>
                            <td style="text-align: right; padding-right: 2rem;">
                                @if($user->role === 'admin')
                                    <span class="btn-lock"><i class="bi bi-shield-lock-fill"></i> Dikunci</span>
                                @else
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('users.edit', $user) }}" class="action-btn btn-edit" title="Edit Pengguna">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        @if(!in_array($user->role, ['admin_sekretariat', 'subag_persuratan', 'bagian_tu']))
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="action-btn btn-del" title="Hapus Pengguna" onclick="return confirm('Yakin ingin menghapus pengguna ini secara permanen?')">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-people"></i>
                                    <div class="empty-title">Tidak Ada Pengguna</div>
                                    <div class="empty-desc">Data pengguna kosong atau tidak ditemukan dalam pencarian.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="mobile-cards">
            @forelse($users as $user)
                @php
                    $rp = rolePill($user->role);
                    $av = avatarColor($user->name);
                @endphp
                <div class="m-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="user-avatar" style="background:{{ $av[0] }};color:{{ $av[1] }};">{{ mb_strtoupper(mb_substr($user->name,0,1)) }}</div>
                        <div class="flex-grow-1">
                            <div class="user-name">{{ $user->name }}</div>
                            <div class="user-email">{{ $user->email }}</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <span class="role-pill {{ $rp['class'] }} mb-2"><i class="bi {{ $rp['icon'] }}"></i> {{ $rp['label'] }}</span>
                        <div style="font-size: 0.85rem; color: #475569;"><i class="bi bi-building me-2"></i>{{ $user->unit->branch->name ?? '—' }}</div>
                        <div style="font-size: 0.85rem; color: #475569; margin-top: 0.25rem;"><i class="bi bi-diagram-3-fill me-2"></i>{{ $user->unit->name ?? '—' }}</div>
                    </div>
                    
                    <div class="pt-3 border-top d-flex gap-2 justify-content-end">
                        @if($user->role !== 'admin')
                            <a href="{{ route('users.edit', $user) }}" class="btn-hero" style="font-size: 0.85rem; padding: 0.5rem 1rem; box-shadow: none; border: 1px solid #e2e8f0; color: #4f46e5;"><i class="bi bi-pencil-fill"></i> Edit</a>
                            @if(!in_array($user->role, ['admin_sekretariat', 'subag_persuratan', 'bagian_tu']))
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn-hero" style="font-size: 0.85rem; padding: 0.5rem 1rem; box-shadow: none; background: #fef2f2; color: #dc2626; border: none;" onclick="return confirm('Yakin hapus?')"><i class="bi bi-trash3-fill"></i></button>
                            </form>
                            @endif
                        @else
                            <span class="btn-lock w-100 justify-content-center"><i class="bi bi-shield-lock-fill"></i> Akun Admin Terlindungi</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="bi bi-people"></i>
                    <div class="empty-title">Tidak Ada Pengguna</div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>

    </div>
</div>
@endsection