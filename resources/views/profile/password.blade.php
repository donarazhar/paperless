@extends('layouts.mailbox')
@section('title', 'Ubah Password')

@push('styles')
<style>
    .profile-page { max-width: 900px; margin: 0 auto; }

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

    .profile-body { position: relative; z-index: 2; }

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

    .profile-grid { display: grid; grid-template-columns: 1fr 1.65fr; gap: 1.25rem; align-items: start; }

    .profile-card {
        background: #fff; border: 1px solid #e8edf4; border-radius: 1.25rem;
        padding: 1.5rem; box-shadow: 0 2px 10px rgba(15,23,42,0.05);
    }
    .pc-section-title {
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; color: #94a3b8; margin-bottom: 1rem;
        display: flex; align-items: center; gap: .4rem;
    }
    .pc-section-title::after { content: ''; flex: 1; height: 1px; background: #f1f5f9; }

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
    .field-pw-input {
        width: 100%; height: 2.8rem; border: 1.5px solid #e2e8f0;
        border-radius: .7rem; padding: 0 2.75rem 0 .9rem; font-size: .875rem;
        font-family: inherit; color: #0f172a; background: #f8fafc;
        outline: none; transition: all .25s;
    }
    .field-input:focus, .field-pw-input:focus { border-color: #6366f1; background: #fff; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    .field-input.is-invalid, .field-pw-input.is-invalid { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
    .field-error { color: #ef4444; font-size: .75rem; margin-top: .3rem; display: flex; align-items: center; gap: 4px; }
    .field-hint { font-size: .7rem; color: #94a3b8; margin-top: .3rem; }

    .field-pw-wrap { position: relative; }
    .pw-eye {
        position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
        background: none; border: none; color: #94a3b8; cursor: pointer;
        font-size: .95rem; padding: 0; transition: color .2s; z-index: 2;
    }
    .pw-eye:hover { color: #6366f1; }

    .pw-strength { margin-top: .4rem; }
    .pw-bar { height: 4px; border-radius: 4px; background: #e2e8f0; overflow: hidden; }
    .pw-bar-fill { height: 100%; border-radius: 4px; width: 0; transition: all .3s; }
    .pw-text { font-size: .68rem; font-weight: 600; margin-top: .2rem; }

    .btn-save {
        display: inline-flex; align-items: center; gap: .45rem;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff; border: none; border-radius: .7rem;
        padding: .7rem 1.6rem; font-size: .875rem; font-weight: 700;
        font-family: inherit; cursor: pointer; transition: all .2s;
        box-shadow: 0 3px 12px rgba(99,102,241,0.3);
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 7px 20px rgba(99,102,241,0.4); }
    .btn-back {
        display: inline-flex; align-items: center; gap: .4rem;
        background: #f1f5f9; color: #64748b; border: 1.5px solid #e2e8f0;
        border-radius: .7rem; padding: .7rem 1.25rem; font-size: .85rem;
        font-weight: 600; font-family: inherit; cursor: pointer; transition: all .2s;
        text-decoration: none;
    }
    .btn-back:hover { background: #e2e8f0; color: #0f172a; }
    .btn-row { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #f1f5f9; }

    .tips-card {
        background: linear-gradient(135deg, #f8faff, #fff);
        border: 1px solid #e0e7ff; border-radius: 1rem; padding: 1.1rem 1.25rem;
    }
    .tip-item {
        display: flex; align-items: flex-start; gap: .5rem;
        font-size: .78rem; color: #64748b; margin-bottom: .5rem; line-height: 1.45;
    }
    .tip-item:last-child { margin-bottom: 0; }
    .tip-item i { color: #6366f1; font-size: .75rem; margin-top: 2px; flex-shrink: 0; }

    @media (max-width: 768px) {
        .profile-hero { padding: 1.75rem 1.25rem 4rem; }
        .profile-grid { grid-template-columns: 1fr; }
        .ph-avatar-lg { width: 64px; height: 64px; font-size: 1.5rem; }
        .ph-name { font-size: 1.15rem; }
    }
</style>
@endpush

@section('content')
@php
    $user = Auth::user();
    $initials = collect(explode(' ', $user->name))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->join('');
    $roleLabel = ucwords(str_replace('_', ' ', $user->role ?? ''));
@endphp

<div class="profile-page">

    {{-- Hero Banner --}}
    <div class="profile-hero">
        <div class="ph-z d-flex align-items-center gap-3">
            <div class="ph-avatar-lg">{{ $initials }}</div>
            <div>
                <div class="ph-name">{{ $user->name }}</div>
                <span class="ph-badge"><i class="bi bi-shield-check-fill"></i> {{ $roleLabel }}</span>
                <div class="ph-email"><i class="bi bi-envelope-fill" style="font-size:.7rem;"></i> {{ $user->email }}</div>
            </div>
        </div>
    </div>

    <div class="profile-body">

        {{-- Tabs --}}
        <div class="profile-tabs">
            <a class="profile-tab" href="{{ route('profile.edit') }}">
                <i class="bi bi-person-fill"></i> Informasi Profil
            </a>
            <a class="profile-tab active" href="{{ route('profile.password') }}">
                <i class="bi bi-lock-fill"></i> Ubah Password
            </a>
        </div>

        {{-- Grid --}}
        <div class="profile-grid">

            {{-- LEFT: Info + Tips --}}
            <div class="d-flex flex-column gap-3">
                <div class="profile-card">
                    <div class="pc-section-title"><i class="bi bi-person-vcard-fill" style="color:#6366f1;"></i> Informasi Akun</div>

                    <div style="display:flex; flex-direction:column; gap:.75rem; padding-top:.25rem;">
                        <div style="display:flex; align-items:center; gap:.6rem; font-size:.85rem; color:#374151; font-weight:600;">
                            <i class="bi bi-building-fill" style="color:#6366f1; font-size:1.05rem; width:20px; text-align:center;"></i>
                            {{ $user->unit->name ?? '—' }}
                        </div>
                        <div style="display:flex; align-items:center; gap:.6rem; font-size:.85rem; color:#374151; font-weight:600;">
                            <i class="bi bi-person-badge-fill" style="color:#16a34a; font-size:1.05rem; width:20px; text-align:center;"></i>
                            {{ $roleLabel }}
                        </div>
                    </div>
                </div>

                <div class="tips-card">
                    <div class="pc-section-title" style="margin-bottom:.75rem;">
                        <i class="bi bi-shield-lock-fill" style="color:#6366f1;"></i> Tips Keamanan
                    </div>
                    <div class="tip-item"><i class="bi bi-check-circle-fill"></i> Gunakan minimal 8 karakter</div>
                    <div class="tip-item"><i class="bi bi-check-circle-fill"></i> Kombinasi huruf besar, kecil & angka</div>
                    <div class="tip-item"><i class="bi bi-check-circle-fill"></i> Tambahkan karakter khusus (!@#$)</div>
                    <div class="tip-item"><i class="bi bi-x-circle-fill" style="color:#ef4444;"></i> Hindari nama atau tanggal lahir</div>
                    <div class="tip-item"><i class="bi bi-x-circle-fill" style="color:#ef4444;"></i> Jangan gunakan ulang password lama</div>
                </div>
            </div>

            {{-- RIGHT: Password Form --}}
            <div class="profile-card">
                <div class="pc-section-title"><i class="bi bi-key-fill" style="color:#6366f1;"></i> Ubah Password</div>

                @if(session('success'))
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:.6rem;font-size:.83rem;color:#166534;font-weight:600;">
                        <i class="bi bi-check-circle-fill" style="color:#16a34a;"></i> {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf @method('PUT')

                    <div class="field-group">
                        <label class="field-label" for="current_password">Password Saat Ini</label>
                        <div class="field-pw-wrap">
                            <input type="password" id="current_password" name="current_password"
                                class="field-pw-input {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                                required placeholder="Masukkan password saat ini">
                            <button type="button" class="pw-eye" data-target="current_password" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="password">Password Baru</label>
                        <div class="field-pw-wrap">
                            <input type="password" id="password" name="password"
                                class="field-pw-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                required placeholder="Minimal 6 karakter">
                            <button type="button" class="pw-eye" data-target="password" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                        @enderror
                        <div class="pw-strength" id="pwStrength" style="display:none">
                            <div class="pw-bar"><div class="pw-bar-fill" id="pwBarFill"></div></div>
                            <div class="pw-text" id="pwText"></div>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="password_confirmation">Konfirmasi Password Baru</label>
                        <div class="field-pw-wrap">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="field-pw-input" required placeholder="Ulangi password baru">
                            <button type="button" class="pw-eye" data-target="password_confirmation" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="field-hint" id="matchHint"></div>
                    </div>

                    <div class="btn-row">
                        <button type="submit" class="btn-save">
                            <i class="bi bi-shield-check"></i> Ubah Password
                        </button>
                        <a href="{{ route('profile.edit') }}" class="btn-back">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.pw-eye').forEach(function(btn){
        btn.addEventListener('click', function(){
            var inp = document.getElementById(this.dataset.target);
            var ico = this.querySelector('i');
            if(inp.type==='password'){inp.type='text';ico.className='bi bi-eye-slash';}
            else{inp.type='password';ico.className='bi bi-eye';}
        });
    });

    var pwInput = document.getElementById('password');
    var strength = document.getElementById('pwStrength');
    var bar = document.getElementById('pwBarFill');
    var text = document.getElementById('pwText');
    pwInput.addEventListener('input', function(){
        var v = this.value;
        if(!v){strength.style.display='none';return;}
        strength.style.display='block';
        var s=0;
        if(v.length>=6)s++;if(v.length>=10)s++;
        if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
        var pct=[0,20,40,60,80,100][s];
        var colors=['#ef4444','#f97316','#eab308','#22c55e','#16a34a'];
        var labels=['Sangat Lemah','Lemah','Cukup','Kuat','Sangat Kuat'];
        var i=Math.max(0,s-1);
        bar.style.width=pct+'%';bar.style.background=colors[i];
        text.style.color=colors[i];text.textContent=labels[i];
    });

    var confirmInput = document.getElementById('password_confirmation');
    var hint = document.getElementById('matchHint');
    confirmInput.addEventListener('input', function(){
        if(!this.value){hint.textContent='';return;}
        if(this.value===pwInput.value){
            hint.innerHTML='<span style="color:#16a34a;font-weight:600;"><i class="bi bi-check-circle-fill"></i> Password cocok</span>';
        } else {
            hint.innerHTML='<span style="color:#ef4444;font-weight:600;"><i class="bi bi-x-circle-fill"></i> Password belum cocok</span>';
        }
    });
});
</script>
@endpush
@endsection