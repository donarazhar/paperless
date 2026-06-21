<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login – Paperless Mail | YPI Al Azhar</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --primary:#6366f1;--primary-dark:#4f46e5;--primary-light:#a5b4fc;
            --accent:#06b6d4;--accent2:#8b5cf6;
            --surface:#ffffff;--surface-dim:rgba(255,255,255,0.06);
            --text:#0f172a;--text-secondary:#64748b;--border:rgba(148,163,184,0.25);
            --error:#ef4444;--success:#22c55e;
        }
        html,body{height:100%;font-family:'Inter',system-ui,sans-serif;overflow:hidden}

        /* ── FULL-SCREEN BACKGROUND ── */
        .login-bg{
            position:fixed;inset:0;
            background:linear-gradient(135deg,#0c0a1a 0%,#1a1145 25%,#0f1729 50%,#0d1f3c 75%,#0c0a1a 100%);
            z-index:0;
        }
        .login-bg::before{
            content:'';position:absolute;inset:0;
            background:
                radial-gradient(ellipse 600px 600px at 20% 30%,rgba(99,102,241,0.15),transparent),
                radial-gradient(ellipse 500px 500px at 80% 70%,rgba(6,182,212,0.12),transparent),
                radial-gradient(ellipse 400px 400px at 50% 50%,rgba(139,92,246,0.08),transparent);
        }

        /* ── ANIMATED PARTICLES ── */
        .particles{position:fixed;inset:0;z-index:1;pointer-events:none;overflow:hidden}
        .particle{
            position:absolute;border-radius:50%;
            background:rgba(255,255,255,0.06);
            animation:floatParticle linear infinite;
        }
        @keyframes floatParticle{
            0%{transform:translateY(100vh) rotate(0deg);opacity:0}
            10%{opacity:1}90%{opacity:1}
            100%{transform:translateY(-100px) rotate(720deg);opacity:0}
        }

        /* ── MESH GRID OVERLAY ── */
        .mesh-grid{
            position:fixed;inset:0;z-index:1;pointer-events:none;
            background-image:
                linear-gradient(rgba(99,102,241,0.03) 1px,transparent 1px),
                linear-gradient(90deg,rgba(99,102,241,0.03) 1px,transparent 1px);
            background-size:60px 60px;
            animation:gridShift 20s linear infinite;
        }
        @keyframes gridShift{0%{transform:translate(0,0)}100%{transform:translate(60px,60px)}}

        /* ── PAGE LAYOUT ── */
        .page{
            position:relative;z-index:2;
            min-height:100vh;min-height:100dvh;
            display:flex;align-items:center;justify-content:center;
            padding:1rem;
        }

        /* ── MAIN CARD ── */
        .login-card{
            width:100%;max-width:960px;
            display:grid;grid-template-columns:1fr 1fr;
            border-radius:1.5rem;overflow:hidden;
            background:rgba(255,255,255,0.03);
            border:1px solid rgba(255,255,255,0.08);
            backdrop-filter:blur(40px);-webkit-backdrop-filter:blur(40px);
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.05),
                0 25px 50px rgba(0,0,0,0.5),
                0 0 100px rgba(99,102,241,0.1);
            animation:cardIn .8s cubic-bezier(.16,1,.3,1) both;
        }
        @keyframes cardIn{
            from{opacity:0;transform:translateY(30px) scale(0.97)}
            to{opacity:1;transform:translateY(0) scale(1)}
        }

        /* ── LEFT: HERO ── */
        .hero{
            background:linear-gradient(160deg,rgba(99,102,241,0.15),rgba(6,182,212,0.1),rgba(139,92,246,0.12));
            padding:2.5rem;display:flex;flex-direction:column;justify-content:center;
            position:relative;overflow:hidden;
            border-right:1px solid rgba(255,255,255,0.06);
        }
        .hero::before{
            content:'';position:absolute;
            width:300px;height:300px;border-radius:50%;
            background:radial-gradient(circle,rgba(99,102,241,0.2),transparent 70%);
            top:-80px;right:-80px;
        }
        .hero::after{
            content:'';position:absolute;
            width:200px;height:200px;border-radius:50%;
            background:radial-gradient(circle,rgba(6,182,212,0.15),transparent 70%);
            bottom:-60px;left:-60px;
        }
        .hero-inner{position:relative;z-index:1}
        .hero-badge{
            display:inline-flex;align-items:center;gap:6px;
            background:rgba(99,102,241,0.15);border:1px solid rgba(99,102,241,0.3);
            color:var(--primary-light);font-size:.65rem;font-weight:700;
            letter-spacing:.1em;text-transform:uppercase;
            padding:.3rem .8rem;border-radius:100px;margin-bottom:1.5rem;
            animation:glowBadge 3s ease-in-out infinite alternate;
        }
        @keyframes glowBadge{
            0%{box-shadow:0 0 8px rgba(99,102,241,0.2)}
            100%{box-shadow:0 0 20px rgba(99,102,241,0.4)}
        }
        .hero h1{
            font-size:clamp(1.5rem,2.5vw,2.2rem);font-weight:800;
            color:#fff;line-height:1.2;letter-spacing:-.03em;margin-bottom:.75rem;
        }
        .hero h1 em{
            font-style:normal;
            background:linear-gradient(135deg,#a5b4fc,#67e8f9,#c4b5fd);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;
            background-clip:text;
        }
        .hero-desc{color:rgba(255,255,255,0.5);font-size:.85rem;line-height:1.7;margin-bottom:1.75rem}
        .hero-features{display:flex;flex-direction:column;gap:.6rem}
        .hero-feat{
            display:flex;align-items:center;gap:.7rem;
            color:rgba(255,255,255,0.7);font-size:.8rem;font-weight:500;
        }
        .feat-icon{
            width:30px;height:30px;border-radius:8px;flex-shrink:0;
            background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);
            display:flex;align-items:center;justify-content:center;
            font-size:.8rem;color:var(--primary-light);
            transition:all .3s;
        }
        .hero-feat:hover .feat-icon{
            background:rgba(99,102,241,0.2);border-color:rgba(99,102,241,0.4);
            transform:scale(1.1);
        }

        /* Floating glass */
        .hero-glass{
            position:absolute;bottom:1.5rem;right:1.5rem;
            background:rgba(255,255,255,0.05);backdrop-filter:blur(20px);
            border:1px solid rgba(255,255,255,0.1);border-radius:1rem;
            padding:.85rem 1rem;z-index:2;min-width:180px;
            animation:float 4s ease-in-out infinite;
        }
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
        .hg-label{font-size:.6rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:rgba(255,255,255,0.35);margin-bottom:.35rem}
        .hg-row{display:flex;align-items:center;gap:.5rem;color:#fff;font-size:.72rem;font-weight:600;margin-bottom:.2rem}
        .hg-dot{width:7px;height:7px;border-radius:50%;background:#4ade80;flex-shrink:0;animation:pulse 2s ease-in-out infinite}
        @keyframes pulse{0%,100%{opacity:1;box-shadow:0 0 0 0 rgba(74,222,128,0.4)}50%{opacity:.7;box-shadow:0 0 0 6px rgba(74,222,128,0)}}

        /* ── RIGHT: FORM ── */
        .form-side{
            background:rgba(255,255,255,0.97);
            display:flex;align-items:center;justify-content:center;
            padding:2.5rem 2rem;position:relative;
        }
        .form-box{width:100%;max-width:22rem}

        /* Brand */
        .brand-wrap{display:flex;align-items:center;gap:.65rem;margin-bottom:1.5rem}
        .brand-logo{
            width:2.5rem;height:2.5rem;object-fit:contain;border-radius:10px;
            border:1.5px solid #e2e8f0;padding:4px;background:#fff;
            box-shadow:0 2px 8px rgba(0,0,0,0.06);
        }
        .b-name{font-size:.85rem;font-weight:800;color:var(--text);line-height:1.2}
        .b-sub{font-size:.65rem;color:var(--text-secondary);font-weight:500}

        .form-box h2{
            font-size:1.65rem;font-weight:800;color:var(--text);
            letter-spacing:-.03em;margin-bottom:.3rem;
        }
        .welcome-sub{color:var(--text-secondary);font-size:.88rem;margin-bottom:1.5rem}

        /* Error alert */
        .err-alert{
            display:flex;align-items:flex-start;gap:.5rem;
            background:#fef2f2;border:1px solid #fecaca;border-radius:.7rem;
            padding:.75rem .85rem;color:#b91c1c;font-size:.82rem;margin-bottom:1.25rem;
            animation:shake .5s ease;
        }
        @keyframes shake{0%,100%{transform:translateX(0)}20%,60%{transform:translateX(-4px)}40%,80%{transform:translateX(4px)}}

        /* Fields */
        .f-group{margin-bottom:1rem}
        .f-label{
            display:block;font-size:.72rem;font-weight:700;
            letter-spacing:.06em;text-transform:uppercase;
            color:var(--text);margin-bottom:.3rem;
        }
        .f-wrap{position:relative}
        .f-wrap .f-icon{
            position:absolute;left:.85rem;top:50%;transform:translateY(-50%);
            color:#94a3b8;font-size:.9rem;pointer-events:none;
            transition:color .25s;z-index:1;
        }
        .f-wrap input{
            width:100%;height:2.85rem;
            border:1.5px solid #e2e8f0;border-radius:.7rem;
            padding:0 .85rem 0 2.5rem;font-size:.88rem;
            font-family:inherit;color:var(--text);background:#f8fafc;
            outline:none;transition:all .25s;
        }
        .f-wrap input::placeholder{color:#c0cad8}
        .f-wrap input:focus{
            border-color:var(--primary);background:#fff;
            box-shadow:0 0 0 3px rgba(99,102,241,0.12);
        }
        .f-wrap:focus-within .f-icon{color:var(--primary)}
        .f-err{color:var(--error);font-size:.75rem;margin-top:.25rem}

        .pw-toggle{
            position:absolute;right:.8rem;top:50%;transform:translateY(-50%);
            background:none;border:none;color:#94a3b8;cursor:pointer;
            font-size:.95rem;padding:0;line-height:1;transition:color .2s;z-index:2;
        }
        .pw-toggle:hover{color:var(--primary)}

        /* Remember */
        .remember{display:flex;align-items:center;gap:.5rem;margin:.6rem 0 1.25rem}
        .remember input{width:15px;height:15px;accent-color:var(--primary);cursor:pointer;border-radius:4px}
        .remember label{font-size:.8rem;color:var(--text-secondary);cursor:pointer}

        /* Submit */
        .btn-submit{
            width:100%;height:2.95rem;
            background:linear-gradient(135deg,var(--primary) 0%,var(--accent2) 100%);
            color:#fff;border:none;border-radius:.7rem;
            font-size:.9rem;font-weight:700;font-family:inherit;cursor:pointer;
            display:flex;align-items:center;justify-content:center;gap:.4rem;
            position:relative;overflow:hidden;
            transition:all .25s;letter-spacing:.01em;
        }
        .btn-submit::before{
            content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,transparent,rgba(255,255,255,0.15),transparent);
            transform:translateX(-100%);transition:transform .5s;
        }
        .btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(99,102,241,0.4)}
        .btn-submit:hover::before{transform:translateX(100%)}
        .btn-submit:active{transform:translateY(0)}

        /* Separator */
        .sep{display:flex;align-items:center;gap:.75rem;margin:1.25rem 0}
        .sep hr{flex:1;border:none;border-top:1px solid #e2e8f0}
        .sep span{font-size:.7rem;color:var(--text-secondary);font-weight:600;white-space:nowrap}

        /* Google button */
        .btn-google{
            width:100%;height:2.95rem;background:#fff;color:var(--text);
            border:1.5px solid #e2e8f0;border-radius:.7rem;
            font-size:.88rem;font-weight:600;font-family:inherit;cursor:pointer;
            display:flex;align-items:center;justify-content:center;gap:.5rem;
            text-decoration:none;transition:all .25s;
        }
        .btn-google:hover{background:#f8fafc;border-color:#cbd5e1;transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,0.06)}
        .btn-google svg{width:18px;height:18px}

        /* Footer */
        .footer-copy{text-align:center;margin-top:1.5rem;font-size:.7rem;color:#b0bad0}

        /* Mobile header */
        .mobile-hero{display:none}

        /* ── RESPONSIVE ── */

        /* Tablet */
        @media(max-width:1024px){
            .login-card{max-width:800px}
            .hero{padding:2rem}
            .hero h1{font-size:1.6rem}
        }

        /* Mobile — single column */
        @media(max-width:768px){
            .page{padding:0;align-items:stretch}
            .login-card{
                grid-template-columns:1fr;
                border-radius:0;max-width:100%;
                min-height:100vh;min-height:100dvh;
                background:rgba(255,255,255,0.97);
                border:none;box-shadow:none;
                backdrop-filter:none;
            }
            .hero{display:none}
            .form-side{
                min-height:100vh;min-height:100dvh;
                padding:1.5rem;align-items:center;
                background:transparent;
            }
            .form-box{max-width:24rem}

            /* Mobile header */
            .mobile-hero{
                display:flex!important;align-items:center;gap:.7rem;
                margin-bottom:1.25rem;padding-bottom:1rem;
                border-bottom:1px solid rgba(0,0,0,0.06);
            }
            .mobile-hero .mh-logo{
                width:2.75rem;height:2.75rem;object-fit:contain;
                border-radius:12px;border:1.5px solid #e2e8f0;
                padding:5px;background:#fff;
                box-shadow:0 2px 8px rgba(99,102,241,0.1);
            }
            .mobile-hero .mh-name{font-size:.9rem;font-weight:800;color:var(--text);line-height:1.2}
            .mobile-hero .mh-sub{font-size:.7rem;color:var(--text-secondary);font-weight:500}

            .brand-wrap{display:none}
        }

        @media(max-width:480px){
            .form-side{padding:1.25rem}
            .form-box h2{font-size:1.35rem}
            .f-wrap input{height:2.75rem;font-size:.85rem}
            .btn-submit,.btn-google{height:2.75rem;font-size:.85rem}
        }

        @media(max-width:360px){
            .form-side{padding:1rem}
            .form-box h2{font-size:1.25rem}
            .welcome-sub{font-size:.82rem}
            .footer-copy{font-size:.65rem}
        }
    </style>
</head>
<body>

<!-- Background layers -->
<div class="login-bg"></div>
<div class="mesh-grid"></div>
<div class="particles" id="particles"></div>

<div class="page">
    <div class="login-card">

        <!-- ── HERO LEFT ── -->
        <div class="hero">
            <div class="hero-inner">
                <div class="hero-badge">
                    <i class="bi bi-envelope-paper-fill"></i>
                    Paperless Mail System
                </div>
                <h1>Persuratan<br>Digital yang<br><em>Efisien</em> & Rapi.</h1>
                <p class="hero-desc">Platform manajemen surat digital untuk seluruh unit di lingkungan YPI Al Azhar. Cepat, aman, dan terlacak.</p>
                <div class="hero-features">
                    <div class="hero-feat">
                        <div class="feat-icon"><i class="bi bi-shield-lock-fill"></i></div>
                        <span>Keamanan data berlapis & terenkripsi</span>
                    </div>
                    <div class="hero-feat">
                        <div class="feat-icon"><i class="bi bi-diagram-3-fill"></i></div>
                        <span>Disposisi multi-unit real-time</span>
                    </div>
                    <div class="hero-feat">
                        <div class="feat-icon"><i class="bi bi-clock-history"></i></div>
                        <span>Lacak setiap perjalanan surat</span>
                    </div>
                </div>
            </div>
            <div class="hero-glass">
                <div class="hg-label">Status Sistem</div>
                <div class="hg-row"><div class="hg-dot"></div> Sistem Aktif & Berjalan</div>
                <div class="hg-row" style="color:rgba(255,255,255,0.45);font-size:.68rem;font-weight:400">
                    <i class="bi bi-lock-fill" style="color:var(--primary-light)"></i> Koneksi Aman (HTTPS)
                </div>
            </div>
        </div>

        <!-- ── FORM RIGHT ── -->
        <div class="form-side">
            <div class="form-box">

                {{-- Mobile header --}}
                <div class="mobile-hero">
                    <img src="{{ asset('img/logo.png') }}" class="mh-logo" alt="Logo">
                    <div>
                        <div class="mh-name">Paperless Mail</div>
                        <div class="mh-sub">YPI Al Azhar — Persuratan Digital</div>
                    </div>
                </div>

                <div class="brand-wrap">
                    <img src="{{ asset('img/logo.png') }}" class="brand-logo" alt="Logo YPI Al Azhar">
                    <div>
                        <div class="b-name">Paperless Mail</div>
                        <div class="b-sub">YPI Al Azhar</div>
                    </div>
                </div>

                <h2>Selamat Datang 👋</h2>
                <p class="welcome-sub">Masuk ke akun Anda untuk melanjutkan.</p>

                @if(session('error'))
                <div class="err-alert">
                    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:1px"></i>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="f-group">
                        <label class="f-label" for="email">Alamat Email</label>
                        <div class="f-wrap">
                            <i class="bi bi-envelope f-icon"></i>
                            <input type="email" id="email" name="email"
                                value="{{ old('email') }}"
                                placeholder="nama@al-azhar.or.id"
                                required autofocus
                                style="{{ $errors->has('email') ? 'border-color:var(--error);' : '' }}">
                        </div>
                        @error('email')<div class="f-err">{{ $message }}</div>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label" for="password">Kata Sandi</label>
                        <div class="f-wrap">
                            <i class="bi bi-lock f-icon"></i>
                            <input type="password" id="password" name="password"
                                placeholder="••••••••" required
                                style="{{ $errors->has('password') ? 'border-color:var(--error);' : '' }}">
                            <button type="button" class="pw-toggle" id="pwToggle" tabindex="-1">
                                <i class="bi bi-eye" id="pwIcon"></i>
                            </button>
                        </div>
                        @error('password')<div class="f-err">{{ $message }}</div>@enderror
                    </div>

                    <div class="remember">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Ingat sesi saya di perangkat ini</label>
                    </div>

                    <button type="submit" class="btn-submit" id="btnSubmit">
                        Masuk ke Sistem &nbsp;<i class="bi bi-arrow-right-short" style="font-size:1.15rem"></i>
                    </button>
                </form>

                <div class="sep"><hr><span>ATAU</span><hr></div>

                <a href="{{ route('google.login') }}" class="btn-google">
                    <svg viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Lanjutkan dengan Google
                </a>

                <div class="footer-copy">&copy; {{ date('Y') }} Yayasan Pesantren Islam Al Azhar</div>
            </div>
        </div>

    </div>
</div>

<script>
// Password toggle
const btn=document.getElementById('pwToggle'),inp=document.getElementById('password'),ico=document.getElementById('pwIcon');
if(btn){btn.addEventListener('click',()=>{const h=inp.type==='password';inp.type=h?'text':'password';ico.className=h?'bi bi-eye-slash':'bi bi-eye'})}

// Particles
(function(){
    const c=document.getElementById('particles');
    for(let i=0;i<25;i++){
        const p=document.createElement('div');p.className='particle';
        const s=Math.random()*4+2;
        p.style.cssText=`width:${s}px;height:${s}px;left:${Math.random()*100}%;animation-duration:${Math.random()*15+10}s;animation-delay:${Math.random()*10}s;`;
        c.appendChild(p);
    }
})();

// Submit loading state
document.querySelector('form')?.addEventListener('submit',function(){
    const b=document.getElementById('btnSubmit');
    b.innerHTML='<span style="display:inline-flex;align-items:center;gap:6px"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="animation:spin 1s linear infinite"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31" stroke-linecap="round"/></svg>Memproses...</span>';
    b.disabled=true;b.style.opacity='.7';
});
</script>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
</body>
</html>