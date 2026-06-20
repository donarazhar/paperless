<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login – Paperless Mail | Yayasan Pesantren Islam Al Azhar</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #1a56db;
            --primary-dark: #1344b4;
            --primary-soft: #eff6ff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #ffffff;
            color: var(--text-main);
            min-height: 100vh;
            margin: 0;
            display: flex;
            overflow: hidden;
        }

        /* ── LEFT PANEL ────────────────────────── */
        .left-panel {
            width: 52%;
            background: linear-gradient(145deg, #0f172a 0%, #1e3a5f 55%, #1a56db 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 4rem 5rem;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,0.06);
            top: -140px;
            right: -140px;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,0.06);
            bottom: -80px;
            left: -80px;
        }

        .left-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            color: #fff;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 0.4rem 1rem;
            border-radius: 100px;
            margin-bottom: 2rem;
        }

        .left-panel h1 {
            color: #fff;
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.03em;
            margin-bottom: 1.25rem;
        }

        .left-panel h1 span {
            color: #93c5fd;
        }

        .left-panel p {
            color: rgba(255,255,255,0.65);
            font-size: 1rem;
            line-height: 1.75;
            max-width: 380px;
            margin-bottom: 2.5rem;
        }

        .stat-pills {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .stat-pill {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.9);
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.45rem 1rem;
            border-radius: 100px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stat-pill i { color: #93c5fd; font-size: 0.85rem; }

        .floating-card {
            position: absolute;
            bottom: 2.5rem;
            right: -24px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            color: #fff;
            width: 210px;
            font-size: 0.82rem;
            z-index: 2;
        }

        .floating-card .icon-wrap {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(147,197,253,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
        }

        /* ── RIGHT PANEL ────────────────────────── */
        .right-panel {
            width: 48%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 4rem;
            background: #fff;
            overflow-y: auto;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
        }

        .login-box .logo-img {
            width: 52px;
            height: 52px;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 6px;
            margin-bottom: 1.75rem;
        }

        .login-box h2 {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.04em;
            margin-bottom: 0.35rem;
        }

        .login-box .subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        /* Input */
        .field-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: 0.02em;
            text-transform: uppercase;
            margin-bottom: 0.45rem;
            display: block;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .input-wrap .prefix-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .input-wrap input {
            width: 100%;
            height: 50px;
            border: 1.5px solid var(--border);
            border-radius: 0.65rem;
            padding: 0 1rem 0 2.85rem;
            font-size: 0.95rem;
            font-family: inherit;
            color: var(--text-main);
            background: #fafafa;
            outline: none;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }

        .input-wrap input::placeholder { color: #b0bec5; }

        .input-wrap input:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3.5px rgba(26,86,219,0.1);
        }

        .input-wrap input:focus ~ .prefix-icon,
        .input-wrap:focus-within .prefix-icon {
            color: var(--primary);
        }

        .toggle-pw {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0;
            font-size: 1rem;
            transition: color 0.2s;
        }

        .toggle-pw:hover { color: var(--primary); }

        /* Remember */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.75rem;
        }
        .remember-row input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: var(--primary);
            cursor: pointer;
        }
        .remember-row label {
            font-size: 0.87rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        /* CTA Button */
        .btn-login {
            width: 100%;
            height: 52px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 0.65rem;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            letter-spacing: 0.01em;
        }

        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(26,86,219,0.3);
        }

        .btn-login:active { transform: translateY(0); }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.75rem 0;
            color: var(--border);
        }
        .divider hr { flex: 1; border-color: var(--border); }
        .divider span { color: var(--text-muted); font-size: 0.8rem; white-space: nowrap; }

        /* Info chips */
        .info-chips { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1.75rem; }
        .info-chip {
            display: flex;
            align-items: center;
            gap: 5px;
            background: var(--primary-soft);
            color: var(--primary);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.3rem 0.75rem;
            border-radius: 100px;
        }

        /* Alert */
        .alert-err {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 0.65rem;
            color: #b91c1c;
            font-size: 0.875rem;
            padding: 0.85rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.5rem;
        }

        /* Footer */
        .login-footer {
            margin-top: 2.5rem;
            color: var(--text-muted);
            font-size: 0.78rem;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 900px) {
            html, body { overflow: auto; }
            .left-panel { display: none; }
            .right-panel { width: 100%; padding: 2.5rem 1.5rem; }
        }
    </style>
</head>

<body>

    <!-- ── LEFT PANEL ── -->
    <div class="left-panel">
        <div class="left-badge">
            <i class="bi bi-envelope-paper-fill"></i>
            Paperless Mail System
        </div>

        <h1>Kelola Surat,<br>Lebih <span>Efisien</span><br>& Terstruktur.</h1>

        <p>Platform persuratan digital terpusat untuk seluruh unit di lingkungan Yayasan Pesantren Islam Al Azhar.</p>

        <div class="stat-pills">
            <div class="stat-pill"><i class="bi bi-shield-check-fill"></i> Aman & Terenkripsi</div>
            <div class="stat-pill"><i class="bi bi-diagram-3-fill"></i> Multi-Unit</div>
            <div class="stat-pill"><i class="bi bi-lightning-charge-fill"></i> Real-time</div>
        </div>

        <div class="floating-card">
            <div class="icon-wrap"><i class="bi bi-send-check-fill text-primary" style="font-size:1.1rem;"></i></div>
            <div style="font-weight:700; margin-bottom:2px;">Disposisi Instan</div>
            <div style="opacity:0.65; font-size:0.78rem;">Teruskan surat ke unit tujuan dalam hitungan detik.</div>
        </div>
    </div>

    <!-- ── RIGHT PANEL ── -->
    <div class="right-panel">
        <div class="login-box">
            <img src="{{ asset('img/logo.png') }}" class="logo-img" alt="Logo Al Azhar">

            <h2>Selamat Datang 👋</h2>
            <p class="subtitle">Masuk ke akun Anda untuk melanjutkan.</p>

            @if(session('error'))
                <div class="alert-err">
                    <i class="bi bi-exclamation-circle-fill" style="font-size:1.1rem; flex-shrink:0;"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div>
                    <label class="field-label" for="email">Email</label>
                    <div class="input-wrap">
                        <input type="email" id="email" name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@al-azhar.or.id"
                            required autofocus
                            class="{{ $errors->has('email') ? 'border-danger' : '' }}">
                        <i class="bi bi-envelope prefix-icon"></i>
                    </div>
                    @error('email')
                        <div style="color:#dc2626; font-size:0.8rem; margin-top:-0.75rem; margin-bottom:1rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="field-label" for="password">Kata Sandi</label>
                    <div class="input-wrap">
                        <input type="password" id="password" name="password"
                            placeholder="••••••••"
                            required
                            class="{{ $errors->has('password') ? 'border-danger' : '' }}">
                        <i class="bi bi-lock prefix-icon"></i>
                        <button type="button" class="toggle-pw" id="togglePw" tabindex="-1">
                            <i class="bi bi-eye" id="togglePwIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div style="color:#dc2626; font-size:0.8rem; margin-top:-0.75rem; margin-bottom:1rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember -->
                <div class="remember-row">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Ingat sesi saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn-login">
                    Masuk ke Sistem <i class="bi bi-arrow-right-short" style="font-size:1.2rem;"></i>
                </button>
            </form>

            <div class="divider">
                <hr><span>Sistem Informasi Persuratan</span><hr>
            </div>

            <div class="info-chips">
                <div class="info-chip"><i class="bi bi-building"></i> YPI Al Azhar</div>
                <div class="info-chip"><i class="bi bi-file-earmark-text"></i> Surat Internal</div>
                <div class="info-chip"><i class="bi bi-clock-history"></i> Lacak Perjalanan</div>
            </div>

            <div class="login-footer">
                &copy; {{ date('Y') }} Yayasan Pesantren Islam Al Azhar. All rights reserved.
            </div>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePw');
        const pwInput   = document.getElementById('password');
        const pwIcon    = document.getElementById('togglePwIcon');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const isHidden = pwInput.type === 'password';
                pwInput.type = isHidden ? 'text' : 'password';
                pwIcon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        }
    </script>

</body>
</html>