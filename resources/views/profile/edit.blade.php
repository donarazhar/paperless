@extends('layouts.mailbox')
@section('title', 'Profil Saya')

@push('styles')
<style>
    /* ══ GMAIL-STYLE LAYOUT ══ */
    .compose-wrapper { display: flex; flex-direction: column; height: 100%; background: #f6f8fc; overflow: hidden; }
    .compose-topbar { display: flex; align-items: center; gap: .75rem; padding: .85rem 1.25rem; background: #fff; border-bottom: 1px solid #e2e8f0; flex-shrink: 0; }
    .compose-topbar h1 { font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0; flex: 1; }
    .btn-back-compose { display: inline-flex; align-items: center; gap: .4rem; background: none; border: 1.5px solid #e2e8f0; color: #475569; border-radius: 100px; padding: .4rem 1rem; font-size: .82rem; font-weight: 600; text-decoration: none; transition: all .2s; cursor: pointer; }
    .btn-back-compose:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
    .compose-body { flex: 1; overflow-y: auto; display: flex; justify-content: center; align-items: flex-start; padding: 1.5rem 1rem 2rem; }

    /* ─── Profile specific ─── */

    /* ─── Hero ─── */
    .profile-hero {
        background: #fff; border: 1px solid #e2e8f0;
        border-radius: 1rem; padding: 1.75rem;
        position: relative; overflow: hidden;
        box-shadow: 0 4px 24px rgba(15,23,42,.04);
    }
    .ph-z { position: relative; z-index: 1; }
    .ph-avatar-lg {
        width: 64px; height: 64px; border-radius: 1rem;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #fff; font-size: 1.5rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 12px rgba(99,102,241,.25);
        flex-shrink: 0;
    }
    .ph-name { font-size: 1.25rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; line-height: 1.2; }
    .ph-email { font-size: .85rem; color: #64748b; margin-top: .2rem; }
    .ph-badge {
        display: inline-flex; align-items: center; gap: .3rem;
        background: #f1f5f9; color: #475569;
        padding: .2rem .6rem; border-radius: 100px;
        font-size: .7rem; font-weight: 700;
        margin-top: .4rem; border: 1px solid #e2e8f0;
    }

    /* ─── Cards & Layout ─── */
    .profile-page { width: 100%; max-width: 900px; display: flex; flex-direction: column; gap: 1.25rem; margin: 0 auto; }
    .profile-grid { display: flex; flex-direction: column; gap: 1.25rem; width: 100%; }

    /* ─── Tabs ─── */
    .profile-tabs {
        display: flex; gap: .35rem; background: #fff;
        border-radius: 1rem; padding: .3rem;
        box-shadow: 0 4px 24px rgba(15,23,42,.04); border: 1px solid #e2e8f0;
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

    /* ─── Cards ─── */
    .profile-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem;
        padding: 1.5rem; box-shadow: 0 4px 24px rgba(15,23,42,.04);
    }
    .pc-section-title {
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; color: #94a3b8; margin-bottom: 1rem;
        display: flex; align-items: center; gap: .4rem;
    }
    .pc-section-title::after { content: ''; flex: 1; height: 1px; background: #f1f5f9; }

    /* ─── Divider ─── */
    .pc-divider { height: 1px; background: #f1f5f9; margin: 1rem 0; }

    /* ─── Form fields ─── */
    .field-group { margin-bottom: 1.15rem; }
    .field-label {
        display: block; font-size: .72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em; color: #475569; margin-bottom: .4rem;
    }
    .field-input {
        width: 100%; height: 2.8rem; border: none; border-bottom: 1.5px solid #e2e8f0;
        border-radius: 0; padding: .25rem 0; font-size: .95rem;
        font-family: inherit; color: #0f172a; background: transparent;
        outline: none; transition: all .2s;
    }
    .field-input:focus { border-color: #6366f1; }
    .field-input.is-invalid { border-color: #ef4444; }
    .field-error { color: #ef4444; font-size: .75rem; margin-top: .3rem; display: flex; align-items: center; gap: 4px; }
    .field-hint { font-size: .7rem; color: #94a3b8; margin-top: .3rem; }

    /* ─── Buttons ─── */
    .btn-save {
        display: inline-flex; align-items: center; gap: .45rem;
        background: #6366f1; color: #fff; border: none; border-radius: 100px;
        padding: .55rem 1.4rem; font-size: .875rem; font-weight: 700;
        font-family: inherit; cursor: pointer; transition: all .2s;
        box-shadow: 0 2px 10px rgba(99,102,241,0.25);
    }
    .btn-save:hover { background: #4f46e5; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(99,102,241,0.35); }
    
    .btn-row { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #f1f5f9; }

    /* ─── Responsive ─── */
    @media (max-width: 768px) {
        .compose-topbar { padding: .65rem .9rem; }
        .compose-body { padding: .75rem .5rem 1.5rem; }
        .profile-grid { grid-template-columns: 1fr; }
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

<div class="compose-wrapper">
    <!-- Top bar -->
    <div class="compose-topbar">
        <a href="{{ route('dashboard') }}" class="btn-back-compose">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1><i class="bi bi-person-circle me-2" style="color:#6366f1;"></i>Profil Saya</h1>
    </div>

    <!-- Body -->
    <div class="compose-body">
        <div class="profile-page">

            {{-- Hero Banner --}}
            <div class="profile-hero">
                <div class="ph-z d-flex align-items-center gap-3">
                    @if($user->photo)
                        <img src="{{ \Illuminate\Support\Str::startsWith($user->photo, 'http') ? $user->photo : asset('storage/' . $user->photo) }}" style="width: 64px; height: 64px; border-radius: 1rem; object-fit: cover; object-position: center; flex-shrink: 0; border: 2px solid #e0e7ff;" alt="Avatar">
                    @else
                        <div class="ph-avatar-lg">{{ $initials }}</div>
                    @endif
                    <div style="min-width: 0; flex: 1;">
                        <div class="ph-name" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $user->name }}</div>
                        <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: .4rem;">
                            <span class="ph-badge" style="margin-top:0;"><i class="bi bi-shield-check-fill"></i> {{ $roleLabel }}</span>
                            @if($hasGoogle)
                                <span class="ph-badge" style="margin-top:0; background:rgba(99,102,241,0.1);">
                                    <i class="bi bi-google"></i> Google
                                </span>
                            @endif
                        </div>
                        <div class="ph-email" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><i class="bi bi-envelope-fill" style="font-size:.7rem;"></i> {{ $user->email }}</div>
                    </div>
                </div>
            </div>

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

                {{-- RIGHT: Edit Form --}}
                <div class="profile-card">
                    <div class="pc-section-title"><i class="bi bi-pencil-fill" style="color:#6366f1;"></i> Edit Profil</div>

                    @if(session('success'))
                        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:.6rem;font-size:.83rem;color:#166534;font-weight:600;">
                            <i class="bi bi-check-circle-fill" style="color:#16a34a;"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        <div class="field-group">
                            <label class="field-label" for="photo">Foto Profil (Opsional)</label>
                            <input type="file" id="photo" name="photo" accept="image/*"
                                class="field-input {{ $errors->has('photo') ? 'is-invalid' : '' }}" style="padding-top: .4rem; font-size: .85rem;">
                            @error('photo')
                                <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                            @enderror
                            <div class="field-hint">Format yang diizinkan: JPG, PNG, GIF. Maks. 2MB.</div>
                        </div>

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
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection