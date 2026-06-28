@extends('layouts.mailbox')
@section('title', 'Manajemen Pengguna')

@section('content')
<style>
    /* ══ GMAIL-STYLE LAYOUT ══ */
    .compose-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: #f6f8fc;
        overflow: hidden;
    }

    /* Top bar */
    .compose-topbar {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .85rem 1.25rem;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .compose-topbar h1 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        flex: 1;
    }
    .btn-back-compose {
        display: inline-flex; align-items: center; gap: .4rem;
        background: none; border: 1.5px solid #e2e8f0; color: #475569;
        border-radius: 100px; padding: .4rem 1rem; font-size: .82rem;
        font-weight: 600; text-decoration: none; transition: all .2s;
        cursor: pointer;
    }
    .btn-back-compose:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }

    /* Scrollable body */
    .compose-body {
        flex: 1;
        overflow-y: auto;
        display: flex;
        justify-content: center;
        padding: 1.5rem 1rem 2rem;
    }

    /* Card */
    .compose-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 24px rgba(15,23,42,.08), 0 1px 4px rgba(15,23,42,.04);
        width: 100%;
        max-width: 1000px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        height: fit-content;
    }

    /* Toolbar / Filter Bar */
    .add-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
        align-items: center;
        justify-content: space-between;
    }
    
    .search-form {
        display: flex;
        align-items: center;
        gap: .5rem;
        flex: 1;
        max-width: 500px;
    }
    .search-wrapper {
        position: relative;
        flex: 1;
    }
    .search-wrapper i {
        position: absolute;
        left: .75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    .search-input {
        width: 100%;
        border: none;
        outline: none;
        font-size: .95rem;
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        color: #0f172a;
        background: transparent;
        padding: .5rem .5rem .5rem 2rem;
        border-bottom: 1.5px solid transparent;
        transition: border-color 0.2s;
    }
    .search-input:focus { border-bottom-color: #4f46e5; }
    .search-input::placeholder { color: #cbd5e1; font-weight: 400; }
    
    .btn-search {
        background: transparent;
        border: none;
        color: #4f46e5;
        font-weight: 600;
        font-size: .85rem;
        padding: .4rem .8rem;
        border-radius: 100px;
        cursor: pointer;
        transition: background .2s;
    }
    .btn-search:hover { background: #e0e7ff; }

    .btn-reset {
        background: transparent;
        border: none;
        color: #64748b;
        font-weight: 600;
        font-size: .85rem;
        padding: .4rem .8rem;
        border-radius: 100px;
        cursor: pointer;
        text-decoration: none;
        transition: background .2s;
    }
    .btn-reset:hover { background: #f1f5f9; color: #475569; }

    .btn-add {
        display: inline-flex; align-items: center; gap: .45rem;
        background: #4f46e5; color: #fff;
        border: none; border-radius: 100px;
        padding: .55rem 1.4rem; font-size: .875rem; font-weight: 700;
        cursor: pointer; transition: all .2s;
        box-shadow: 0 2px 10px rgba(79,70,229,.25);
        text-decoration: none;
        white-space: nowrap;
    }
    .btn-add:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(79,70,229,.35); color: #fff; }

    /* Table Styling */
    .table-container {
        width: 100%;
        overflow-x: auto;
    }
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .modern-table th {
        background: #fff; padding: 1rem 1.25rem; font-weight: 700; color: #94a3b8; 
        font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px solid #e2e8f0;
    }
    .modern-table td { padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .modern-table tbody tr:hover { background: #f8fafc; }
    .modern-table tbody tr:last-child td { border-bottom: none; }

    /* User Avatar & Info */
    .user-avatar { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; flex-shrink: 0; }
    .user-name { font-weight: 600; font-size: .9rem; color: #0f172a; margin-bottom: 0.15rem; }
    .user-email { font-size: .75rem; color: #64748b; font-weight: 500; }

    /* Role Pills */
    .role-pill { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.3rem 0.6rem; border-radius: 100px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; }
    .rp-admin { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
    .rp-staftu { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .rp-kasubag { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .rp-kepala { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .rp-stafunit { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
    .rp-default { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

    /* Action Buttons */
    .action-btn { display: inline-flex; align-items: center; justify-content: center; border-radius: 100px; border: none; transition: all .2s; text-decoration: none; font-weight: 600; font-size: .8rem; padding: .4rem .8rem; gap: .3rem; cursor: pointer; }
    .btn-edit { background: #eef2ff; color: #4f46e5; border: 1.5px solid #c7d2fe; }
    .btn-edit:hover { background: #4f46e5; color: #fff; border-color: #4f46e5; }
    .btn-del { background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; width: 32px; height: 32px; padding: 0; border-radius: 50%; }
    .btn-del:hover { background: #dc2626; color: #fff; border-color: #dc2626; }
    .btn-lock { background: #f8fafc; color: #94a3b8; font-size: 0.75rem; padding: 0.4rem 0.8rem; border-radius: 100px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem; border: 1.5px solid #e2e8f0; }

    /* Empty State */
    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-state i { font-size: 2.5rem; color: #cbd5e1; margin-bottom: 1rem; display: block; }
    .empty-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
    .empty-desc { color: #94a3b8; font-size: .9rem; }

    /* Pagination container */
    .pagination-wrapper { padding: 1rem 1.25rem; border-top: 1px solid #f1f5f9; background: #fff; }
    .pagination-wrapper nav { margin-bottom: 0; }
    .pagination-wrapper p { margin-bottom: 0; }

    /* Responsive */
    @media (max-width: 640px) {
        .compose-topbar { padding: .65rem .9rem; }
        .compose-body { padding: .75rem .5rem 1.5rem; }
        .add-bar { flex-direction: column-reverse; align-items: stretch; }
        .search-form { max-width: 100%; }
        .search-input { border-bottom: 1.5px solid #e2e8f0; }
        .btn-add { justify-content: center; }
        .action-btn span { display: none; }
        .btn-edit { padding: .4rem; width: 32px; height: 32px; border-radius: 50%; }
        .btn-edit i { margin: 0; }
        .table-responsive-hide { display: none; }
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

<div class="compose-wrapper">

    <!-- Top bar -->
    <div class="compose-topbar">
        <a href="{{ url()->previous() }}" class="btn-back-compose">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1><i class="bi bi-people-fill me-2" style="color:#4f46e5;"></i>Manajemen Pengguna</h1>
    </div>

    <!-- Body -->
    <div class="compose-body">
        <div class="compose-card">
            
            <!-- Toolbar -->
            <div class="add-bar">
                <form method="GET" action="{{ route('users.index') }}" class="search-form">
                    <div class="search-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="search-input" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn-search">Filter</button>
                    @if(request('search'))
                        <a href="{{ route('users.index') }}" class="btn-reset">Reset</a>
                    @endif
                </form>

                <a href="{{ route('users.create') }}" class="btn-add">
                    <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
                </a>
            </div>

            <!-- Table -->
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">#</th>
                            <th>Profil Pengguna</th>
                            <th>Hak Akses (Role)</th>
                            <th style="text-align: right; width: 120px; padding-right: 1.25rem;">Aksi</th>
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
                                <td style="text-align: center; color: #94a3b8; font-weight: 600; font-size: .85rem;">
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
                                <td style="text-align: right; padding-right: 1.25rem;">
                                    @if($user->role === 'admin')
                                        <span class="btn-lock"><i class="bi bi-shield-lock-fill"></i> Dikunci</span>
                                    @else
                                        <div class="d-flex gap-2 justify-content-end align-items-center">
                                            <a href="{{ route('users.edit', $user) }}" class="action-btn btn-edit" title="Edit Pengguna">
                                                <i class="bi bi-pencil-fill"></i> <span class="table-responsive-hide">Edit</span>
                                            </a>
                                            @if(!in_array($user->role, ['admin_sekretariat', 'subag_persuratan', 'bagian_tu']))
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" style="margin:0;">
                                                @csrf @method('DELETE')
                                                <button class="action-btn btn-del" type="submit" title="Hapus Pengguna" onclick="return confirm('Yakin ingin menghapus pengguna ini secara permanen?')">
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

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="pagination-wrapper">
                    {{ $users->links() }}
                </div>
            @endif

        </div>
    </div>
</div>
<style>
    @media (max-width: 640px) {
        .table-responsive-show-sm { display: block !important; }
    }
</style>
@endsection