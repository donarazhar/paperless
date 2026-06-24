@extends('layouts.mailbox')
@section('title', 'Profil Saya')

@push('styles')
<style>
    .profile-page { max-width: 900px; margin: 0 auto; }

    /* ─── Hero ─── */
    .profile-hero {
        background: #fff; border: 1px solid #e8edf4;
        border-radius: 1rem; padding: 1.75rem 1.75rem 3.5rem;
        position: relative; overflow: hidden; margin-bottom: -2.75rem;
        box-shadow: 0 1px 6px rgba(15,23,42,0.04);
    }
    .profile-hero::before, .profile-hero::after { display: none; }
    .ph-z { position: relative; z-index: 1; }
    .ph-avatar-lg {
        width: 64px; height: 64px; border-radius: 1rem; flex-shrink: 0;
        background: #eef2ff; border: 2px solid #e0e7ff;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; font-weight: 800; color: #6366f1;
    }
    .ph-name { font-size: 1.25rem; font-weight: 800; color: #0f172a; line-height: 1.2; }
    .ph-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: .7rem; font-weight: 600; color: #6366f1;
        background: #eef2ff; border: 1px solid #e0e7ff;
        padding: .25rem .7rem; border-radius: 100px; margin-top: .4rem;
    }
    .ph-email { font-size: .8rem; color: #94a3b8; margin-top: .2rem; }

    /* ─── Content wrapper ─── */
    .profile-body { position: relative; z-index: 2; }

    /* ─── Tabs ─── */
    .profile-tabs {
        display: flex; gap: .35rem; background: #fff;
        border-radius: 1rem; padding: .3rem; margin-bottom: 1.25rem;
        box-shadow: 0 2px 10px rgba(15,23,42,0.06); border: 1px solid #e8edf4;
    }
    .profile-tab {
        flex: 1; padding: .65rem .75rem; border-radius: .75rem;
        font-size: .83rem; font-weight: 600; color: #64748b;
        text-align: center; cursor: pointer; transition: all .2s;
        border: none; background: none; display: flex; align-items: center;
        justify-content: center; gap: .4rem; text-decoration: none;
    }
    .profile-tab:hover { color: #0f172a; background: #f8fafc; }
    .profile-tab.active { background: #f1f5f9; color: #0f172a; font-weight: 700; }
    .profile-tab i { font-size: .9rem; }

    /* ─── Grid ─── */
    .profile-grid { display: grid; grid-template-columns: 1fr 1.65fr; gap: 1.25rem; align-items: start; }

    /* ─── Cards ─── */
    .profile-card {
        background: #fff; border: 1px solid #e8edf4; border-radius: 1.25rem;
        padding: 1.5rem; box-shadow: 0 2px 10px rgba(15,23,42,0.05);
    }
    .pc-section-title {
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; color: #94a3b8; margin-bottom: 1rem;
        display: flex; align-items: center; gap: .4rem;
    }
    .pc-section-title::after {
        content: ''; flex: 1; height: 1px; background: #f1f5f9;
    }

    /* ─── Info rows ─── */
    .info-item {
        display: flex; align-items: center; gap: .85rem;
        padding: .75rem .9rem; border-radius: .85rem;
        transition: background .15s; margin-bottom: .4rem;
    }
    .info-item:hover { background: #f8fafc; }
    .info-icon {
        width: 36px; height: 36px; border-radius: .6rem; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: .9rem;
    }
    .info-label { font-size: .68rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em; }
    .info-value { font-size: .9rem; font-weight: 700; color: #0f172a; margin-top: 1px; }

    /* ─── Divider ─── */
    .pc-divider { height: 1px; background: #f1f5f9; margin: 1rem 0; }

    /* ─── Form fields ─── */
    .field-group { margin-bottom: 1.15rem; }
    .field-label {
        display: block; font-size: .72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em; color: #475569; margin-bottom: .4rem;
    }
    .field-input {
        width: 100%; height: 2.8rem; border: 1.5px solid #e2e8f0;
        border-radius: .7rem; padding: 0 .9rem; font-size: .875rem;
        font-family: inherit; color: #0f172a; background: #f8fafc;
        outline: none; transition: all .25s;
    }
    .field-input:focus { border-color: #6366f1; background: #fff; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    .field-input.is-invalid { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
    .field-error { color: #ef4444; font-size: .75rem; margin-top: .3rem; display: flex; align-items: center; gap: 4px; }
    .field-hint { font-size: .7rem; color: #94a3b8; margin-top: .3rem; }

    /* ─── Buttons ─── */
    .btn-save {
        display: inline-flex; align-items: center; gap: .45rem;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff; border: none; border-radius: .7rem;
        padding: .7rem 1.6rem; font-size: .875rem; font-weight: 700;
        font-family: inherit; cursor: pointer; transition: all .2s;
        box-shadow: 0 3px 12px rgba(99,102,241,0.3);
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 7px 20px rgba(99,102,241,0.4); }
    .btn-save:active { transform: translateY(0); }
    .btn-back {
        display: inline-flex; align-items: center; gap: .4rem;
        background: #f1f5f9; color: #64748b; border: 1.5px solid #e2e8f0;
        border-radius: .7rem; padding: .7rem 1.25rem; font-size: .85rem;
        font-weight: 600; font-family: inherit; cursor: pointer; transition: all .2s;
        text-decoration: none;
    }
    .btn-back:hover { background: #e2e8f0; color: #0f172a; }
    .btn-row { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #f1f5f9; }

    /* ─── Google badge ─── */
    .google-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: .72rem; font-weight: 600; color: #4285f4;
        background: #eff6ff; border: 1px solid #bfdbfe;
        padding: .25rem .65rem; border-radius: 100px;
    }

    /* ─── Quick nav ─── */
    .quick-nav-card {
        background: linear-gradient(135deg, #f8faff, #fff);
        border: 1px solid #e0e7ff; border-radius: 1rem; padding: 1rem;
    }
    .qn-item {
        display: flex; align-items: center; gap: .75rem;
        padding: .65rem .75rem; border-radius: .75rem; cursor: pointer;
        text-decoration: none; transition: all .2s; color: #374151;
    }
    .qn-item:hover { background: #eef2ff; color: #6366f1; }
    .qn-icon { width: 34px; height: 34px; border-radius: .55rem; display: flex; align-items: center; justify-content: center; font-size: .85rem; flex-shrink: 0; }
    .qn-label { font-size: .83rem; font-weight: 600; }
    .qn-arrow { margin-left: auto; font-size: .7rem; color: #94a3b8; }

    /* ─── Responsive ─── */
    @media (max-width: 768px) {
        .profile-hero { padding: 1.75rem 1.25rem 4rem; }
        .profile-grid { grid-template-columns: 1fr; }
        .ph-avatar-lg { width: 64px; height: 64px; font-size: 1.5rem; }
        .ph-name { font-size: 1.15rem; }
        .profile-tabs { gap: .25rem; }
    }
</style>
@endpush

@section('content')
@php
    $user = Auth::user();
    $initials = collect(explode(' ', $user->name))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->join('');
    $roleLabel = ucwords(str_replace('_', ' ', $user->role ?? ''));
    $hasGoogle = !empty($user->google_id);
@endphp

<div class="profile-page">

    {{-- Hero Banner --}}
    <div class="profile-hero">
        <div class="ph-z d-flex align-items-center gap-3">
            <div class="ph-avatar-lg">{{ $initials }}</div>
            <div>
                <div class="ph-name">{{ $user->name }}</div>
                <div>
                    <span class="ph-badge"><i class="bi bi-shield-check-fill"></i> {{ $roleLabel }}</span>
                    @if($hasGoogle)
                        <span class="ph-badge ms-1" style="background:rgba(255,255,255,0.2);">
                            <i class="bi bi-google"></i> Google
                        </span>
                    @endif
                </div>
                <div class="ph-email"><i class="bi bi-envelope-fill" style="font-size:.7rem;"></i> {{ $user->email }}</div>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="profile-body">

        {{-- Tabs --}}
        <div class="profile-tabs">
            <a class="profile-tab active" href="{{ route('profile.edit') }}">
                <i class="bi bi-person-fill"></i> Informasi Profil
            </a>
            <a class="profile-tab" href="{{ route('profile.password') }}">
                <i class="bi bi-lock-fill"></i> Ubah Password
            </a>
        </div>

        {{-- Grid --}}
        <div class="profile-grid">

            {{-- LEFT: Account Info --}}
            <div class="d-flex flex-column gap-3">
                <div class="profile-card">
                    <div class="pc-section-title"><i class="bi bi-person-vcard-fill" style="color:#6366f1;"></i> Informasi Akun</div>

                    <div style="display:flex; flex-direction:column; gap:.75rem; padding-top:.25rem;">
                        <div style="display:flex; align-items:center; gap:.6rem; font-size:.85rem; color:#374151; font-weight:600;">
                            <i class="bi bi-building-fill" style="color:#6366f1; font-size:1.05rem; width:20px; text-align:center;"></i>
                            {{ $user->unit->name ?? '—' }}
                        </div>
                        <div style="display:flex; align-items:center; gap:.6rem; font-size:.85rem; color:#374151; font-weight:600;">
                            <i class="bi bi-layers-fill" style="color:#db2777; font-size:1.05rem; width:20px; text-align:center;"></i>
                            {{ $user->organ->name ?? '—' }}
                        </div>
                        <div style="display:flex; align-items:center; gap:.6rem; font-size:.85rem; color:#374151; font-weight:600;">
                            <i class="bi bi-person-badge-fill" style="color:#16a34a; font-size:1.05rem; width:20px; text-align:center;"></i>
                            {{ $roleLabel }}
                        </div>
                        <div style="display:flex; align-items:center; gap:.6rem; font-size:.85rem; color:#374151; font-weight:600;">
                            <i class="bi bi-calendar-event-fill" style="color:#ca8a04; font-size:1.05rem; width:20px; text-align:center;"></i>
                            Terdaftar {{ $user->created_at->translatedFormat('d F Y') }}
                        </div>
                        @if($hasGoogle)
                            <div class="pc-divider" style="margin:.25rem 0;"></div>
                            <div style="display:flex; align-items:center; gap:.6rem; font-size:.85rem; color:#4285f4; font-weight:600;">
                                <i class="bi bi-google" style="font-size:1.05rem; width:20px; text-align:center;"></i>
                                Google Sign-In Aktif
                            </div>
                        @endif
                    </div>
                </div>


            </div>

            {{-- RIGHT: Edit Form --}}
            <div class="profile-card">
                <div class="pc-section-title"><i class="bi bi-pencil-fill" style="color:#6366f1;"></i> Edit Profil</div>

                @if(session('success'))
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:.6rem;font-size:.83rem;color:#166534;font-weight:600;">
                        <i class="bi bi-check-circle-fill" style="color:#16a34a;"></i> {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PUT')

                    <div class="field-group">
                        <label class="field-label" for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name"
                            class="field-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama lengkap">
                        @error('name')
                            <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="email">Alamat Email</label>
                        <input type="email" id="email" name="email"
                            class="field-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            value="{{ old('email', $user->email) }}" required placeholder="nama@al-azhar.or.id"
                            {{ $hasGoogle ? 'readonly' : '' }}>
                        @error('email')
                            <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                        @enderror
                        @if($hasGoogle)
                            <div class="field-hint"><i class="bi bi-info-circle"></i> Email dikelola oleh Google, tidak dapat diubah di sini.</div>
                        @else
                            <div class="field-hint">Email digunakan untuk login dan notifikasi sistem.</div>
                        @endif
                    </div>

                    <div class="btn-row">
                        <button type="submit" class="btn-save">
                            <i class="bi bi-check2-circle"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn-back">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection