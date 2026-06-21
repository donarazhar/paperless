@extends('layouts.app')
@section('title', 'Edit Profil')

@push('styles')
<style>
    .profile-page{max-width:720px;margin:0 auto}

    /* Header card */
    .profile-header{
        background:linear-gradient(135deg,#6366f1 0%,#8b5cf6 50%,#06b6d4 100%);
        border-radius:1.25rem;padding:2rem 2rem 1.5rem;margin-bottom:1.5rem;
        position:relative;overflow:hidden;
    }
    .profile-header::before{
        content:'';position:absolute;width:200px;height:200px;border-radius:50%;
        background:rgba(255,255,255,0.08);top:-60px;right:-40px;
    }
    .profile-header::after{
        content:'';position:absolute;width:120px;height:120px;border-radius:50%;
        background:rgba(255,255,255,0.06);bottom:-30px;left:30px;
    }
    .ph-inner{position:relative;z-index:1;display:flex;align-items:center;gap:1.25rem}
    .ph-avatar{
        width:64px;height:64px;border-radius:1rem;flex-shrink:0;
        background:rgba(255,255,255,0.2);border:2px solid rgba(255,255,255,0.3);
        display:flex;align-items:center;justify-content:center;
        font-size:1.5rem;font-weight:800;color:#fff;
        backdrop-filter:blur(10px);
    }
    .ph-name{font-size:1.25rem;font-weight:800;color:#fff;line-height:1.2}
    .ph-role{
        display:inline-flex;align-items:center;gap:4px;
        font-size:.7rem;font-weight:600;color:rgba(255,255,255,0.8);
        background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);
        padding:.2rem .6rem;border-radius:100px;margin-top:.35rem;
    }
    .ph-email{font-size:.78rem;color:rgba(255,255,255,0.7);margin-top:.15rem}

    /* Tab navigation */
    .profile-tabs{
        display:flex;gap:.35rem;background:#f1f5f9;
        border-radius:.75rem;padding:.3rem;margin-bottom:1.5rem;
    }
    .profile-tab{
        flex:1;padding:.6rem .75rem;border-radius:.55rem;
        font-size:.82rem;font-weight:600;color:#64748b;
        text-align:center;cursor:pointer;transition:all .2s;
        border:none;background:none;display:flex;align-items:center;
        justify-content:center;gap:.4rem;text-decoration:none;
    }
    .profile-tab:hover{color:#0f172a}
    .profile-tab.active{background:#fff;color:#6366f1;box-shadow:0 1px 4px rgba(0,0,0,0.06)}
    .profile-tab i{font-size:.9rem}

    /* Form card */
    .profile-card{
        background:#fff;border:1px solid #e8edf4;border-radius:1rem;
        padding:1.75rem;box-shadow:0 1px 6px rgba(15,23,42,0.04);
    }
    .pc-title{font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:.25rem}
    .pc-desc{font-size:.8rem;color:#64748b;margin-bottom:1.5rem}

    .field-group{margin-bottom:1.25rem}
    .field-label{
        display:block;font-size:.72rem;font-weight:700;
        text-transform:uppercase;letter-spacing:.06em;
        color:#0f172a;margin-bottom:.35rem;
    }
    .field-hint{font-size:.7rem;color:#94a3b8;margin-top:.25rem}
    .field-input{
        width:100%;height:2.85rem;border:1.5px solid #e2e8f0;
        border-radius:.65rem;padding:0 .85rem;font-size:.88rem;
        font-family:inherit;color:#0f172a;background:#f8fafc;
        outline:none;transition:all .25s;
    }
    .field-input:focus{border-color:#6366f1;background:#fff;box-shadow:0 0 0 3px rgba(99,102,241,0.1)}
    .field-input.is-invalid{border-color:#ef4444}
    .field-error{color:#ef4444;font-size:.75rem;margin-top:.25rem}

    .field-pw-wrap{position:relative}
    .field-pw-wrap .pw-eye{
        position:absolute;right:.75rem;top:50%;transform:translateY(-50%);
        background:none;border:none;color:#94a3b8;cursor:pointer;
        font-size:.95rem;padding:0;transition:color .2s;z-index:2;
    }
    .field-pw-wrap .pw-eye:hover{color:#6366f1}

    /* Buttons */
    .btn-profile-save{
        display:inline-flex;align-items:center;gap:.4rem;
        background:linear-gradient(135deg,#6366f1,#8b5cf6);
        color:#fff;border:none;border-radius:.65rem;
        padding:.65rem 1.5rem;font-size:.88rem;font-weight:700;
        font-family:inherit;cursor:pointer;transition:all .2s;
    }
    .btn-profile-save:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(99,102,241,0.35)}
    .btn-profile-save:active{transform:translateY(0)}
    .btn-cancel{
        display:inline-flex;align-items:center;gap:.4rem;
        background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;
        border-radius:.65rem;padding:.65rem 1.25rem;font-size:.85rem;
        font-weight:600;font-family:inherit;cursor:pointer;transition:all .2s;
        text-decoration:none;
    }
    .btn-cancel:hover{background:#e2e8f0;color:#0f172a}

    .btn-row{display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-top:1.5rem}

    /* Info readonly */
    .info-row{
        display:flex;align-items:center;gap:.75rem;
        padding:.65rem .85rem;background:#f8fafc;border-radius:.6rem;
        border:1px solid #f1f5f9;margin-bottom:.65rem;
    }
    .info-icon{
        width:32px;height:32px;border-radius:8px;flex-shrink:0;
        display:flex;align-items:center;justify-content:center;
        font-size:.85rem;
    }
    .info-label{font-size:.68rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em}
    .info-value{font-size:.85rem;font-weight:600;color:#0f172a}

    /* Responsive */
    @media(max-width:640px){
        .profile-header{padding:1.5rem 1.25rem 1.25rem}
        .ph-avatar{width:48px;height:48px;font-size:1.15rem}
        .ph-name{font-size:1.05rem}
        .profile-card{padding:1.25rem}
        .profile-tabs{flex-direction:column}
        .btn-row{flex-direction:column}
        .btn-row .btn-profile-save,.btn-row .btn-cancel{width:100%;justify-content:center}
    }
</style>
@endpush

@section('content')
@php
    $user = Auth::user();
    $initials = collect(explode(' ', $user->name))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->join('');
    $role = ucwords(str_replace('_', ' ', $user->role ?? ''));
@endphp

<div class="profile-page">

    <!-- Header -->
    <div class="profile-header">
        <div class="ph-inner">
            <div class="ph-avatar">{{ $initials }}</div>
            <div>
                <div class="ph-name">{{ $user->name }}</div>
                <div class="ph-role"><i class="bi bi-shield-check-fill"></i> {{ $role }} — {{ $user->unit->name ?? 'Administrator' }}</div>
                <div class="ph-email">{{ $user->email }}</div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="profile-tabs">
        <a class="profile-tab active" href="{{ route('profile.edit') }}">
            <i class="bi bi-person-fill"></i> Informasi Profil
        </a>
        <a class="profile-tab" href="{{ route('profile.password') }}">
            <i class="bi bi-lock-fill"></i> Ubah Password
        </a>
    </div>

    <!-- Account Info (read-only) -->
    <div class="profile-card" style="margin-bottom:1rem">
        <div class="pc-title">Informasi Akun</div>
        <div class="pc-desc">Data unit dan role Anda yang dikelola administrator.</div>
        <div class="info-row">
            <div class="info-icon" style="background:#eef2ff;color:#6366f1"><i class="bi bi-building-fill"></i></div>
            <div><div class="info-label">Unit Kerja</div><div class="info-value">{{ $user->unit->name ?? '-' }}</div></div>
        </div>
        <div class="info-row">
            <div class="info-icon" style="background:#f0fdf4;color:#16a34a"><i class="bi bi-person-badge-fill"></i></div>
            <div><div class="info-label">Role</div><div class="info-value">{{ $role }}</div></div>
        </div>
        <div class="info-row">
            <div class="info-icon" style="background:#fefce8;color:#ca8a04"><i class="bi bi-calendar-event-fill"></i></div>
            <div><div class="info-label">Terdaftar Sejak</div><div class="info-value">{{ $user->created_at->translatedFormat('d F Y') }}</div></div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="profile-card">
        <div class="pc-title">Edit Profil</div>
        <div class="pc-desc">Perbarui nama dan email akun Anda.</div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf @method('PUT')

            <div class="field-group">
                <label class="field-label" for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" class="field-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama lengkap">
                @error('name')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field-group">
                <label class="field-label" for="email">Alamat Email</label>
                <input type="email" id="email" name="email" class="field-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    value="{{ old('email', $user->email) }}" required placeholder="nama@al-azhar.or.id">
                @error('email')<div class="field-error">{{ $message }}</div>@enderror
                <div class="field-hint">Email digunakan untuk login dan notifikasi.</div>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-profile-save">
                    <i class="bi bi-check2-circle"></i> Simpan Perubahan
                </button>
                <a href="{{ route('dashboard') }}" class="btn-cancel">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection