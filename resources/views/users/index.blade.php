@extends('layouts.app')
@section('title', 'Manajemen Pengguna')

@section('content')


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
<div class="inbox-hero" style="background: linear-gradient(135deg, #10b981 0%, #059669 45%, #047857 100%);">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="hero-title mb-0">Manajemen Pengguna</div>
            <div class="hero-sub">Kelola akun dan hak akses pengguna sistem</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="stat-chip">
                <i class="bi bi-people-fill"></i> {{ $users->count() }} pengguna
            </div>
            <a href="{{ route('users.create') }}" class="btn-custom outline" style="width: auto; background: rgba(255,255,255,0.15); color: #fff; border-color: rgba(255,255,255,0.25);">
                <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
            </a>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="filter-card">
    <form method="GET" action="{{ route('users.index') }}" class="row gy-2 gx-2 align-items-end">
        <div class="col-12 col-md-9">
            <label class="f-label">Cari Pengguna</label>
            <input type="text" name="search" class="form-control" placeholder="Ketik nama atau email..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-3 d-flex gap-2 align-items-end">
            <button type="submit" class="btn-filter flex-grow-1 justify-content-center">
                <i class="bi bi-search"></i> Cari
            </button>
            <a href="{{ route('users.index') }}" class="btn-reset flex-grow-1 justify-content-center text-center">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </div>
    </form>
</div>

{{-- DESKTOP TABLE --}}
<div class="table-wrap" style="background:#fff;border:1px solid #e8edf4;border-radius:1rem;overflow:hidden;">
    <table class="inbox-table">
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
                    <td style="color:#94a3b8;font-size:0.78rem;font-weight:600;">{{ $users->firstItem() + $loop->index }}</td>
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

<div class="mt-4">
    {{ $users->links() }}
</div>

@endsection