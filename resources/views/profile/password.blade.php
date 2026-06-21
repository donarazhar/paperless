@extends('layouts.app')
@section('title', 'Ubah Password')

@push('styles')
<style>
    .profile-page{max-width:720px;margin:0 auto}
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
    .btn-profile-save{
        display:inline-flex;align-items:center;gap:.4rem;
        background:linear-gradient(135deg,#6366f1,#8b5cf6);
        color:#fff;border:none;border-radius:.65rem;
        padding:.65rem 1.5rem;font-size:.88rem;font-weight:700;
        font-family:inherit;cursor:pointer;transition:all .2s;
    }
    .btn-profile-save:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(99,102,241,0.35)}
    .btn-cancel{
        display:inline-flex;align-items:center;gap:.4rem;
        background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;
        border-radius:.65rem;padding:.65rem 1.25rem;font-size:.85rem;
        font-weight:600;font-family:inherit;cursor:pointer;transition:all .2s;
        text-decoration:none;
    }
    .btn-cancel:hover{background:#e2e8f0;color:#0f172a}
    .btn-row{display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-top:1.5rem}

    /* Password strength */
    .pw-strength{margin-top:.35rem}
    .pw-bar{height:4px;border-radius:4px;background:#e2e8f0;overflow:hidden}
    .pw-bar-fill{height:100%;border-radius:4px;width:0;transition:all .3s}
    .pw-text{font-size:.68rem;font-weight:600;margin-top:.2rem}

    /* Security tips */
    .security-tips{
        background:#f8fafc;border:1px solid #f1f5f9;border-radius:.75rem;
        padding:1rem 1.25rem;margin-bottom:1.5rem;
    }
    .st-title{font-size:.78rem;font-weight:700;color:#0f172a;margin-bottom:.5rem;display:flex;align-items:center;gap:.4rem}
    .st-item{
        display:flex;align-items:flex-start;gap:.4rem;
        font-size:.75rem;color:#64748b;margin-bottom:.3rem;line-height:1.4;
    }
    .st-item i{color:#94a3b8;font-size:.65rem;margin-top:3px;flex-shrink:0}

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
        <a class="profile-tab" href="{{ route('profile.edit') }}">
            <i class="bi bi-person-fill"></i> Informasi Profil
        </a>
        <a class="profile-tab active" href="{{ route('profile.password') }}">
            <i class="bi bi-lock-fill"></i> Ubah Password
        </a>
    </div>

    <!-- Security Tips -->
    <div class="security-tips">
        <div class="st-title"><i class="bi bi-shield-lock-fill" style="color:#6366f1"></i> Tips Keamanan Password</div>
        <div class="st-item"><i class="bi bi-check-circle-fill"></i> Minimal 6 karakter</div>
        <div class="st-item"><i class="bi bi-check-circle-fill"></i> Kombinasi huruf besar, kecil, dan angka</div>
        <div class="st-item"><i class="bi bi-check-circle-fill"></i> Jangan gunakan nama atau tanggal lahir</div>
    </div>

    <!-- Password Form -->
    <div class="profile-card">
        <div class="pc-title"><i class="bi bi-key-fill" style="color:#6366f1;margin-right:.35rem"></i> Ubah Password</div>
        <div class="pc-desc">Masukkan password saat ini lalu buat password baru.</div>

        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf @method('PUT')

            <div class="field-group">
                <label class="field-label" for="current_password">Password Saat Ini</label>
                <div class="field-pw-wrap">
                    <input type="password" id="current_password" name="current_password"
                        class="field-input {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                        required placeholder="Masukkan password saat ini">
                    <button type="button" class="pw-eye" data-target="current_password" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('current_password')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field-group">
                <label class="field-label" for="password">Password Baru</label>
                <div class="field-pw-wrap">
                    <input type="password" id="password" name="password"
                        class="field-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        required placeholder="Minimal 6 karakter">
                    <button type="button" class="pw-eye" data-target="password" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')<div class="field-error">{{ $message }}</div>@enderror
                <div class="pw-strength" id="pwStrength" style="display:none">
                    <div class="pw-bar"><div class="pw-bar-fill" id="pwBarFill"></div></div>
                    <div class="pw-text" id="pwText"></div>
                </div>
            </div>

            <div class="field-group">
                <label class="field-label" for="password_confirmation">Konfirmasi Password Baru</label>
                <div class="field-pw-wrap">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="field-input" required placeholder="Ulangi password baru">
                    <button type="button" class="pw-eye" data-target="password_confirmation" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="field-hint" id="matchHint"></div>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-profile-save">
                    <i class="bi bi-shield-check"></i> Ubah Password
                </button>
                <a href="{{ route('profile.edit') }}" class="btn-cancel">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Toggle password visibility
    document.querySelectorAll('.pw-eye').forEach(function(btn){
        btn.addEventListener('click', function(){
            var inp = document.getElementById(this.dataset.target);
            var ico = this.querySelector('i');
            if(inp.type==='password'){inp.type='text';ico.className='bi bi-eye-slash'}
            else{inp.type='password';ico.className='bi bi-eye'}
        });
    });

    // Password strength
    var pwInput = document.getElementById('password');
    var strength = document.getElementById('pwStrength');
    var bar = document.getElementById('pwBarFill');
    var text = document.getElementById('pwText');
    pwInput.addEventListener('input', function(){
        var v = this.value;
        if(!v){strength.style.display='none';return}
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

    // Confirm match
    var confirm = document.getElementById('password_confirmation');
    var hint = document.getElementById('matchHint');
    confirm.addEventListener('input', function(){
        if(!this.value){hint.textContent='';return}
        if(this.value===pwInput.value){hint.textContent='✓ Password cocok';hint.style.color='#16a34a'}
        else{hint.textContent='✗ Password belum cocok';hint.style.color='#ef4444'}
    });
});
</script>
@endpush
@endsection