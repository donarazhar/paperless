<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login – Paperless Mail | YPI Al Azhar</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue: #2563eb;
            --blue-dark: #1d4ed8;
            --blue-light: #dbeafe;
            --navy: #0f172a;
            --slate: #1e293b;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --bg: #f8faff;
        }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
        }

        /* ═══════════════════════════════
           LAYOUT
        ═══════════════════════════════ */
        .page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* ═══════════════════════════════
           LEFT — HERO
        ═══════════════════════════════ */
        .hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #1d4ed8 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem 5rem;
            position: relative;
            overflow: hidden;
        }

        /* Decorative orbs */
        .hero::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,0.18) 0%, transparent 70%);
            top: -160px; right: -160px;
            pointer-events: none;
        }
        .hero::after {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 70%);
            bottom: -100px; left: -100px;
            pointer-events: none;
        }

        .hero-inner { position: relative; z-index: 1; }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.14);
            color: rgba(255,255,255,0.9);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.35rem 0.9rem;
            border-radius: 100px;
            margin-bottom: 2.25rem;
            width: fit-content;
        }

        .hero h1 {
            font-size: clamp(2rem, 3.5vw, 3rem);
            font-weight: 900;
            color: #fff;
            line-height: 1.15;
            letter-spacing: -0.04em;
            margin-bottom: 1.25rem;
        }

        .hero h1 em {
            font-style: normal;
            background: linear-gradient(135deg, #93c5fd, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            color: rgba(255,255,255,0.6);
            font-size: 1rem;
            line-height: 1.8;
            max-width: 360px;
            margin-bottom: 2.5rem;
            font-weight: 400;
        }

        .hero-features {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .hero-feat {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            color: rgba(255,255,255,0.8);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .feat-icon {
            width: 32px; height: 32px;
            border-radius: 9px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.12);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem;
            color: #93c5fd;
            flex-shrink: 0;
        }

        /* Glassmorphism card on hero */
        .hero-glass {
            position: absolute;
            bottom: 2.5rem;
            right: 2.5rem;
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.13);
            border-radius: 1.1rem;
            padding: 1.1rem 1.35rem;
            z-index: 2;
            min-width: 210px;
            animation: floatUp 3.5s ease-in-out infinite;
        }

        @keyframes floatUp {
            0%, 100% { transform: translateY(0); }
            50%  { transform: translateY(-8px); }
        }

        .hero-glass .hg-label {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.45);
            margin-bottom: 0.5rem;
        }

        .hero-glass .hg-row {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            color: #fff;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
        }

        .hg-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #4ade80;
            flex-shrink: 0;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.7); }
        }

        /* ═══════════════════════════════
           RIGHT — FORM
        ═══════════════════════════════ */
        .form-side {
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            overflow-y: auto;
        }

        .form-box {
            width: 100%;
            max-width: 400px;
        }

        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            margin-bottom: 2.5rem;
        }

        .brand-logo {
            width: 44px; height: 44px;
            object-fit: contain;
            border-radius: 10px;
            border: 1px solid var(--border);
            padding: 5px;
            background: #fff;
        }

        .brand-text .b-name {
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--text);
            line-height: 1.2;
        }

        .brand-text .b-sub {
            font-size: 0.72rem;
            color: var(--muted);
            font-weight: 500;
        }

        .form-box h2 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.04em;
            margin-bottom: 0.4rem;
        }

        .form-box .welcome-sub {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        /* Alert */
        .err-alert {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 0.65rem;
            padding: 0.85rem 1rem;
            color: #b91c1c;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }

        /* Field */
        .f-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .f-wrap {
            position: relative;
            margin-bottom: 1.1rem;
        }

        .f-wrap .f-icon {
            position: absolute;
            left: 0.95rem;
            top: 50%; transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.95rem;
            pointer-events: none;
            transition: color .2s;
            z-index: 1;
        }

        .f-wrap input {
            width: 100%;
            height: 48px;
            border: 1.5px solid var(--border);
            border-radius: 0.75rem;
            padding: 0 1rem 0 2.75rem;
            font-size: 0.9rem;
            font-family: inherit;
            color: var(--text);
            background: #fafbfc;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }

        .f-wrap input::placeholder { color: #c0cad8; }

        .f-wrap input:focus {
            border-color: var(--blue);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .f-wrap:focus-within .f-icon { color: var(--blue); }

        .f-err { color: #dc2626; font-size: 0.78rem; margin-top: -0.65rem; margin-bottom: 0.85rem; }

        .pw-toggle {
            position: absolute;
            right: 0.9rem; top: 50%; transform: translateY(-50%);
            background: none; border: none;
            color: #94a3b8; cursor: pointer;
            font-size: 1rem;
            padding: 0; line-height: 1;
            transition: color .2s;
            z-index: 2;
        }
        .pw-toggle:hover { color: var(--blue); }

        /* Remember */
        .remember {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin: 1rem 0 1.5rem;
        }
        .remember input {
            width: 16px; height: 16px;
            accent-color: var(--blue);
            cursor: pointer;
        }
        .remember label { font-size: 0.85rem; color: var(--muted); cursor: pointer; }

        /* Button */
        .btn-submit {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, var(--blue) 0%, #7c3aed 100%);
            color: #fff;
            border: none;
            border-radius: 0.75rem;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            transition: transform .15s, box-shadow .2s, opacity .2s;
            letter-spacing: 0.01em;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0.08);
            opacity: 0;
            transition: opacity .2s;
        }

        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(37,99,235,0.35); }
        .btn-submit:hover::before { opacity: 1; }
        .btn-submit:active { transform: translateY(0); }

        /* Separator */
        .sep {
            display: flex; align-items: center; gap: 1rem;
            margin: 1.75rem 0 1.5rem;
        }
        .sep hr { flex:1; border:none; border-top: 1px solid var(--border); }
        .sep span { font-size: 0.75rem; color: var(--muted); white-space: nowrap; }

        /* Info pills */
        .info-pills { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .info-pill {
            display: inline-flex; align-items: center; gap: 5px;
            background: var(--blue-light);
            color: var(--blue-dark);
            font-size: 0.73rem; font-weight: 600;
            padding: 0.3rem 0.75rem;
            border-radius: 100px;
        }

        .footer-copy {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.75rem;
            color: #b0bad0;
        }

        /* ═══════════════════════════════
           RESPONSIVE
        ═══════════════════════════════ */

        /* Tablet landscape (≤1024px) */
        @media (max-width: 1024px) {
            .page { grid-template-columns: 1fr 1.2fr; }
            .hero { padding: 3rem 3rem; }
            .hero h1 { font-size: 2.1rem; }
        }

        /* Tablet portrait (≤768px) — stack vertically */
        @media (max-width: 768px) {
            .page { grid-template-columns: 1fr; }

            .hero {
                padding: 2.5rem 2rem;
                min-height: auto;
                justify-content: flex-start;
            }

            .hero h1 { font-size: 1.8rem; }
            .hero p { font-size: 0.875rem; max-width: 100%; }
            .hero-features { display: none; }
            .hero-glass { display: none; }

            .form-side { padding: 2.5rem 1.5rem; }
        }

        /* Mobile (≤480px) */
        @media (max-width: 480px) {
            .hero { padding: 2rem 1.25rem; }
            .hero-badge { font-size: 0.65rem; }
            .hero h1 { font-size: 1.55rem; }
            .hero p { display: none; }

            .form-side { padding: 2rem 1.25rem; }
            .form-box h2 { font-size: 1.45rem; }
            .btn-submit { height: 46px; font-size: 0.9rem; }
        }

        /* Very small (≤360px) */
        @media (max-width: 360px) {
            .hero { padding: 1.5rem 1rem; }
            .form-side { padding: 1.5rem 1rem; }
        }
    </style>
</head>

<body>
<div class="page">

    <!-- ── HERO LEFT ── -->
    <div class="hero">
        <div class="hero-inner">
            <div class="hero-badge">
                <i class="bi bi-envelope-paper-fill"></i>
                Paperless Mail System
            </div>

            <h1>Persuratan<br>Digital yang<br><em>Efisien</em> &amp; Rapi.</h1>

            <p>Platform manajemen surat digital untuk seluruh unit di lingkungan YPI Al Azhar. Cepat, aman, dan terlacak.</p>

            <div class="hero-features">
                <div class="hero-feat">
                    <div class="feat-icon"><i class="bi bi-shield-check-fill"></i></div>
                    <span>Keamanan data berlapis &amp; terenkripsi</span>
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
            <div class="hg-row"><div class="hg-dot"></div> Sistem Aktif &amp; Berjalan</div>
            <div class="hg-row" style="color:rgba(255,255,255,0.55); font-size:0.75rem; font-weight:400;">
                <i class="bi bi-lock-fill" style="color:#93c5fd;"></i> Koneksi Aman (HTTPS)
            </div>
        </div>
    </div>

    <!-- ── FORM RIGHT ── -->
    <div class="form-side">
        <div class="form-box">

            <div class="brand-wrap">
                <img src="{{ asset('img/logo.png') }}" class="brand-logo" alt="Logo YPI Al Azhar">
                <div class="brand-text">
                    <div class="b-name">Paperless Mail</div>
                    <div class="b-sub">YPI Al Azhar</div>
                </div>
            </div>

            <h2>Selamat Datang 👋</h2>
            <p class="welcome-sub">Masuk ke akun Anda untuk melanjutkan.</p>

            @if(session('error'))
            <div class="err-alert">
                <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0; margin-top:1px;"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <label class="f-label" for="email">Alamat Email</label>
                <div class="f-wrap">
                    <i class="bi bi-envelope f-icon"></i>
                    <input type="email" id="email" name="email"
                        value="{{ old('email') }}"
                        placeholder="nama@al-azhar.or.id"
                        required autofocus
                        style="{{ $errors->has('email') ? 'border-color:#dc2626;' : '' }}">
                </div>
                @error('email')<div class="f-err">{{ $message }}</div>@enderror

                <!-- Password -->
                <label class="f-label" for="password">Kata Sandi</label>
                <div class="f-wrap">
                    <i class="bi bi-lock f-icon"></i>
                    <input type="password" id="password" name="password"
                        placeholder="••••••••"
                        required
                        style="{{ $errors->has('password') ? 'border-color:#dc2626;' : '' }}">
                    <button type="button" class="pw-toggle" id="pwToggle" tabindex="-1">
                        <i class="bi bi-eye" id="pwIcon"></i>
                    </button>
                </div>
                @error('password')<div class="f-err">{{ $message }}</div>@enderror

                <div class="remember">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Ingat sesi saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn-submit">
                    Masuk ke Sistem &nbsp;<i class="bi bi-arrow-right-short" style="font-size:1.2rem;"></i>
                </button>
            </form>

            <div class="sep">
                <hr><span>Sistem Informasi Persuratan Terpusat</span><hr>
            </div>

            <div class="info-pills">
                <div class="info-pill"><i class="bi bi-building-fill"></i> YPI Al Azhar</div>
                <div class="info-pill"><i class="bi bi-file-earmark-text-fill"></i> Surat Internal</div>
                <div class="info-pill"><i class="bi bi-send-check-fill"></i> Disposisi Digital</div>
                <div class="info-pill"><i class="bi bi-graph-up-arrow"></i> Laporan Real-time</div>
            </div>

            <div class="footer-copy">&copy; {{ date('Y') }} Yayasan Pesantren Islam Al Azhar</div>
        </div>
    </div>
</div>

<script>
    const btn = document.getElementById('pwToggle');
    const inp = document.getElementById('password');
    const ico = document.getElementById('pwIcon');
    if (btn) {
        btn.addEventListener('click', () => {
            const hide = inp.type === 'password';
            inp.type = hide ? 'text' : 'password';
            ico.className = hide ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }
</script>
</body>
</html>