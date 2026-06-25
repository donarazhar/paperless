@extends('layouts.mailbox')
@section('title', 'Ubah Password')

@push('styles')
<style>
    /* ══ GMAIL-STYLE LAYOUT ══ */
    .compose-wrapper { display: flex; flex-direction: column; height: 100%; background: #f6f8fc; overflow: hidden; }
    .compose-topbar { display: flex; align-items: center; gap: .75rem; padding: .85rem 1.25rem; background: #fff; border-bottom: 1px solid #e2e8f0; flex-shrink: 0; }
    .compose-topbar h1 { font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0; flex: 1; }
    .btn-back-compose { display: inline-flex; align-items: center; gap: .4rem; background: none; border: 1.5px solid #e2e8f0; color: #475569; border-radius: 100px; padding: .4rem 1rem; font-size: .82rem; font-weight: 600; text-decoration: none; transition: all .2s; cursor: pointer; }
    .btn-back-compose:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
    .compose-body { flex: 1; overflow-y: auto; display: flex; justify-content: center; padding: 1.5rem 1rem 2rem; }

    /* ─── Profile specific ─── */
    .profile-page { width: 100%; max-width: 900px; display: flex; flex-direction: column; gap: 1.25rem; }

    .profile-hero {
        background: #fff; border: 1px solid #e2e8f0;
        border-radius: 1rem; padding: 1.75rem;
        position: relative; overflow: hidden;
        box-shadow: 0 4px 24px rgba(15,23,42,.04);
    }
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

    .profile-grid { display: grid; grid-template-columns: 1fr 1.65fr; gap: 1.25rem; align-items: start; }

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

    .field-group { margin-bottom: 1.15rem; }
    .field-label {
        display: block; font-size: .72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em; color: #475569; margin-bottom: .4rem;
    }
    
    .field-pw-input {
        width: 100%; height: 2.8rem; border: none; border-bottom: 1.5px solid #e2e8f0;
        border-radius: 0; padding: .25rem 2.75rem .25rem 0; font-size: .95rem;
        font-family: inherit; color: #0f172a; background: transparent;
        outline: none; transition: all .2s;
    }
    .field-pw-input:focus { border-color: #6366f1; }
    .field-pw-input.is-invalid { border-color: #ef4444; }
    .field-error { color: #ef4444; font-size: .75rem; margin-top: .3rem; display: flex; align-items: center; gap: 4px; }
    .field-hint { font-size: .7rem; color: #94a3b8; margin-top: .3rem; }

    .field-pw-wrap { position: relative; }
    .pw-eye {
        position: absolute; right: .25rem; top: 50%; transform: translateY(-50%);
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
        background: #6366f1; color: #fff; border: none; border-radius: 100px;
        padding: .55rem 1.4rem; font-size: .875rem; font-weight: 700;
        font-family: inherit; cursor: pointer; transition: all .2s;
        box-shadow: 0 2px 10px rgba(99,102,241,0.25);
    }
    .btn-save:hover { background: #4f46e5; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(99,102,241,0.35); }
    
    .btn-row { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #f1f5f9; }

    .tips-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.1rem 1.25rem;
        box-shadow: 0 4px 24px rgba(15,23,42,.04);
    }
    .tip-item {
        display: flex; align-items: flex-start; gap: .5rem;
        font-size: .78rem; color: #64748b; margin-bottom: .5rem; line-height: 1.45;
    }
    .tip-item:last-child { margin-bottom: 0; }
    .tip-item i { color: #6366f1; font-size: .75rem; margin-top: 2px; flex-shrink: 0; }

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
                    <div class="ph-avatar-lg">{{ $initials }}</div>
                    <div>
                        <div class="ph-name">{{ $user->name }}</div>
                        <span class="ph-badge"><i class="bi bi-shield-check-fill"></i> {{ $roleLabel }}</span>
                        <div class="ph-email"><i class="bi bi-envelope-fill" style="font-size:.7rem;"></i> {{ $user->email }}</div>
                    </div>
                </div>
            </div>

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
                        </div>
                    </form>
                </div>
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