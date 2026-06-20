@extends('layouts.app')
@section('title', 'Manajemen Pengguna')

@section('content')
<style>
    .filter-bar { background:#fff;border:1px solid #e8edf4;border-radius:1rem;padding:1.1rem 1.35rem;margin-bottom:1.25rem; }
    .filter-bar .f-label { font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#94a3b8;margin-bottom:0.35rem;display:block; }
    .filter-bar .form-select { height:40px;font-size:0.865rem;border-radius:0.6rem;border:1.5px solid #e8edf4;background:#fafbfd;padding:0 0.9rem; }
    .filter-bar .form-select:focus { border-color:#2563eb;background:#fff;box-shadow:0 0 0 3px rgba(37,99,235,0.09)!important; }

    .users-table { width:100%;border-collapse:separate;border-spacing:0; }
    .users-table thead th { font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;padding:0.6rem 0.85rem;border-bottom:1px solid #e8edf4;white-space:nowrap;background:#fafbfd; }
    .users-table thead th:first-child { border-radius:0.75rem 0 0 0;padding-left:1.25rem; }
    .users-table thead th:last-child  { border-radius:0 0.75rem 0 0;padding-right:1.25rem; }
    .users-table tbody tr:hover td { background:#f8faff; }
    .users-table tbody td { padding:0.85rem 0.85rem;border-bottom:1px solid #f1f5f9;vertical-align:middle;font-size:0.865rem;color:#334155; }
    .users-table tbody td:first-child { padding-left:1.25rem; }
    .users-table tbody td:last-child  { padding-right:1.25rem; }
    .users-table tbody tr:last-child td { border-bottom:none; }

    /* Avatar */
    .user-avatar { width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:800;flex-shrink:0; }

    /* Role pills */
    .role-pill { display:inline-flex;align-items:center;gap:4px;font-size:0.68rem;font-weight:700;padding:0.25rem 0.7rem;border-radius:100px; }
    .rp-admin   { background:#fef3c7;color:#92400e; }
    .rp-kasubag { background:#ede9fe;color:#7c3aed; }
    .rp-staftu  { background:#dbeafe;color:#1d4ed8; }
    .rp-stafunit{ background:#dcfce7;color:#166534; }
    .rp-kepala  { background:#fce7f3;color:#9d174d; }
    .rp-default { background:#f1f5f9;color:#475569; }

    /* Buttons */
    .btn-edit { display:inline-flex;align-items:center;gap:4px;background:#fef9c3;color:#92400e;border:none;border-radius:0.45rem;font-size:0.76rem;font-weight:700;padding:0.35rem 0.75rem;text-decoration:none;transition:background .15s; }
    .btn-edit:hover { background:#fef08a;color:#713f12; }
    .btn-del  { display:inline-flex;align-items:center;gap:4px;background:#fef2f2;color:#dc2626;border:none;border-radius:0.45rem;font-size:0.76rem;font-weight:700;padding:0.35rem 0.75rem;cursor:pointer;transition:background .15s; }
    .btn-del:hover { background:#fee2e2;color:#b91c1c; }
    .btn-lock { display:inline-flex;align-items:center;gap:4px;background:#f1f5f9;color:#94a3b8;border:none;border-radius:0.45rem;font-size:0.76rem;font-weight:600;padding:0.35rem 0.75rem;cursor:default; }
    .btn-new  { display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;border:none;border-radius:0.65rem;font-size:0.85rem;font-weight:700;padding:0.55rem 1.1rem;text-decoration:none;transition:opacity .15s,transform .12s; }
    .btn-new:hover { opacity:0.9;transform:translateY(-1px);color:#fff; }

    /* Mobile cards */
    .user-card { background:#fff;border:1px solid #e8edf4;border-radius:0.9rem;padding:1rem 1.1rem;margin-bottom:0.65rem; }
    .uc-name { font-size:0.9rem;font-weight:700;color:#0f172a;margin-bottom:2px; }
    .uc-email { font-size:0.78rem;color:#64748b;margin-bottom:0.5rem; }
    .uc-meta { font-size:0.75rem;color:#64748b;display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:0.65rem; }
    .uc-meta i { font-size:0.7rem; }

    .empty-state { text-align:center;padding:4rem 1rem;color:#94a3b8; }
    .empty-state i { font-size:3rem;display:block;margin-bottom:0.75rem;color:#cbd5e1; }

    .table-wrap { display:block; }
    .cards-wrap { display:none; }
    @media(max-width:900px) { .table-wrap{display:none;} .cards-wrap{display:block;} }
    @media(max-width:600px) { .filter-bar{padding:0.9rem 1rem;} }
</style>

@php
    function rolePill($role) {
        return match($role) {
            'admin'              => ['class'=>'rp-admin',   'label'=>'Admin',           'icon'=>'bi-shield-fill-check'],
            'kasubag_tu'         => ['class'=>'rp-kasubag', 'label'=>'Kasubag TU',      'icon'=>'bi-person-badge-fill'],
            'staf_tu'            => ['class'=>'rp-staftu',  'label'=>'Staf TU',         'icon'=>'bi-person-fill'],
            'staf_unit'          => ['class'=>'rp-stafunit','label'=>'Staf Unit',        'icon'=>'bi-person-fill'],
            'kepala_sekretariat' => ['class'=>'rp-kepala',  'label'=>'Kepala Sekretariat','icon'=>'bi-star-fill'],
            default              => ['class'=>'rp-default', 'label'=>ucfirst($role),    'icon'=>'bi-person'],
        };
    }
    function avatarColor($name) {
        $colors = [['#dbeafe','#2563eb'],['#ede9fe','#7c3aed'],['#dcfce7','#16a34a'],['#fce7f3','#db2777'],['#fef9c3','#ca8a04'],['#ffedd5','#ea580c']];
        return $colors[abs(crc32($name)) % count($colors)];
    }
@endphp

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <h1 class="h5 fw-bold mb-0" style="letter-spacing:-0.03em;">Manajemen Pengguna</h1>
        <p class="text-muted mb-0" style="font-size:0.82rem;">Kelola akun dan hak akses pengguna sistem</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge" style="background:#eff6ff;color:#2563eb;font-size:0.78rem;padding:0.45rem 0.9rem;border-radius:100px;">
            <i class="bi bi-people-fill me-1"></i>{{ $users->count() }} pengguna
        </span>
        <a href="{{ route('users.create') }}" class="btn-new">
            <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('users.index') }}" class="row gy-2 gx-2 align-items-end">
        <div class="col-12 col-sm-5 col-md-4">
            <label class="f-label">Cabang</label>
            <select name="branch_id" class="form-select" onchange="this.form.submit()">
                <option value="">Semua Cabang</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ request('branch_id')==$b->id ? 'selected':'' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-sm-5 col-md-4">
            <label class="f-label">Unit Kerja</label>
            <select name="unit_id" class="form-select" onchange="this.form.submit()">
                <option value="">Semua Unit</option>
                @foreach($units as $u)
                    <option value="{{ $u->id }}" {{ request('unit_id')==$u->id ? 'selected':'' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-sm-auto d-flex gap-2">
            <button class="btn btn-primary" style="height:40px;border-radius:0.6rem;font-size:0.85rem;padding:0 1rem;">
                <i class="bi bi-funnel-fill"></i>
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-light border" style="height:40px;border-radius:0.6rem;font-size:0.85rem;padding:0 0.9rem;">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>
</div>

{{-- DESKTOP TABLE --}}
<div class="table-wrap" style="background:#fff;border:1px solid #e8edf4;border-radius:1rem;overflow:hidden;">
    <table class="users-table">
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>Pengguna</th>
                <th style="width:130px;">Role</th>
                <th style="width:140px;">Cabang</th>
                <th style="width:160px;">Unit</th>
                <th style="width:110px;">Aksi</th>
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
                    <td style="color:#94a3b8;font-size:0.78rem;font-weight:600;">{{ $loop->iteration }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar" style="background:{{ $av[0] }};color:{{ $av[1] }};">{{ $inits }}</div>
                            <div>
                                <div style="font-weight:700;font-size:0.875rem;color:#0f172a;">{{ $user->name }}</div>
                                <div style="font-size:0.72rem;color:#94a3b8;">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="role-pill {{ $rp['class'] }}">
                            <i class="bi {{ $rp['icon'] }}"></i> {{ $rp['label'] }}
                        </span>
                    </td>
                    <td style="font-size:0.845rem;">{{ $user->unit->branch->name ?? '—' }}</td>
                    <td style="font-size:0.845rem;">{{ $user->unit->name ?? '—' }}</td>
                    <td>
                        @if($user->role === 'admin')
                            <span class="btn-lock"><i class="bi bi-lock-fill"></i> Dikunci</span>
                        @else
                            <div class="d-flex gap-1">
                                <a href="{{ route('users.edit', $user) }}" class="btn-edit">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn-del" onclick="return confirm('Yakin hapus pengguna ini?')">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">
                    <div class="empty-state">
                        <i class="bi bi-people"></i>
                        <p class="fw-semibold mb-1" style="color:#475569;">Belum ada pengguna</p>
                        <span style="font-size:0.8rem;">Tambahkan pengguna baru menggunakan tombol di atas.</span>
                    </div>
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MOBILE CARDS --}}
<div class="cards-wrap">
    @forelse($users as $user)
        @php
            $rp = rolePill($user->role);
            $av = avatarColor($user->name);
        @endphp
        <div class="user-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="user-avatar" style="background:{{ $av[0] }};color:{{ $av[1] }};width:40px;height:40px;">
                    {{ mb_strtoupper(mb_substr($user->name,0,1)) }}
                </div>
                <div class="flex-grow-1">
                    <div class="uc-name">{{ $user->name }}</div>
                    <div class="uc-email">{{ $user->email }}</div>
                </div>
                <span class="role-pill {{ $rp['class'] }}"><i class="bi {{ $rp['icon'] }}"></i> {{ $rp['label'] }}</span>
            </div>
            <div class="uc-meta">
                <span><i class="bi bi-building"></i> {{ $user->unit->branch->name ?? '—' }}</span>
                <span><i class="bi bi-diagram-3-fill"></i> {{ $user->unit->name ?? '—' }}</span>
            </div>
            @if($user->role !== 'admin')
                <div class="d-flex gap-2">
                    <a href="{{ route('users.edit', $user) }}" class="btn-edit"><i class="bi bi-pencil-fill"></i> Edit</a>
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn-del" onclick="return confirm('Yakin hapus?')"><i class="bi bi-trash3-fill"></i> Hapus</button>
                    </form>
                </div>
            @else
                <span class="btn-lock"><i class="bi bi-lock-fill"></i> Akun terlindungi</span>
            @endif
        </div>
    @empty
        <div class="empty-state">
            <i class="bi bi-people"></i>
            <p class="fw-semibold mb-1" style="color:#475569;">Belum ada pengguna</p>
        </div>
    @endforelse
</div>

@endsection