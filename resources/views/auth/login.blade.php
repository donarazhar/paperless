<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Al Azhar Paperless System</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            padding: 1.25rem;
        }

        /* ── Card ── */
        .card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07),
                        0 20px 40px -8px rgba(0,0,0,0.1);
        }

        /* ── Header ── */
        .card-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-wrap {
            width: 68px;
            height: 68px;
            margin: 0 auto 1rem;
            border-radius: 18px;
            overflow: hidden;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
        }

        .logo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .card-header h1 {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .card-header p {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-top: 0.25rem;
        }

        /* ── Alert Error ── */
        .alert-error {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #be123c;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.84rem;
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            margin-bottom: 1.5rem;
        }

        .alert-error i {
            font-size: 1rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ── Form ── */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.45rem;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.95rem;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: 2.85rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0 0.9rem 0 2.5rem;
            font-size: 0.9rem;
            font-family: inherit;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
        }

        .form-input:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        }

        .form-input::placeholder {
            color: #cbd5e1;
        }

        /* Password toggle */
        .btn-eye {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            font-size: 0.95rem;
            padding: 0.25rem;
            line-height: 1;
            transition: color 0.15s;
        }

        .btn-eye:hover { color: #3b82f6; }

        /* ── Submit ── */
        .btn-submit {
            width: 100%;
            height: 2.85rem;
            margin-top: 1.5rem;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: #fff;
            border: none;
            border-radius: 0.75rem;
            font-size: 0.93rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 14px rgba(29,78,216,0.35);
            transition: transform 0.18s, box-shadow 0.18s, opacity 0.18s;
            letter-spacing: 0.01em;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(29,78,216,0.42);
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(29,78,216,0.3);
        }

        .btn-submit.is-loading {
            opacity: 0.8;
            pointer-events: none;
        }

        .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.65s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .btn-submit.is-loading .btn-label { display: none; }
        .btn-submit.is-loading .spinner   { display: block; }

        /* ── Divider ── */
        .divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            font-size: 0.75rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            white-space: nowrap;
        }

        /* ── Google ── */
        .btn-google {
            width: 100%;
            height: 2.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.625rem;
            background: #fff;
            color: #334155;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: inherit;
            text-decoration: none;
            cursor: pointer;
            transition: border-color 0.18s, background 0.18s, box-shadow 0.18s;
        }

        .btn-google:hover {
            border-color: #93c5fd;
            background: #eff6ff;
            color: #1d4ed8;
            box-shadow: 0 2px 10px rgba(59,130,246,0.1);
        }

        .btn-google img {
            width: 18px;
            height: 18px;
        }

        /* ── Footer note ── */
        .card-footer-note {
            margin-top: 1.75rem;
            text-align: center;
            font-size: 0.77rem;
            color: #94a3b8;
            line-height: 1.5;
        }

        /* ── Responsive ── */

        /* Tablet (600px – 899px) */
        @media (min-width: 600px) {
            body { background: #e8edf5; }

            .card {
                padding: 2.75rem 2.5rem;
                border: 1px solid #e2e8f0;
            }
        }

        /* Desktop (≥ 900px) */
        @media (min-width: 900px) {
            body {
                background:
                    radial-gradient(ellipse 70% 60% at 20% 50%, rgba(59,130,246,0.08) 0%, transparent 70%),
                    radial-gradient(ellipse 60% 50% at 80% 50%, rgba(99,102,241,0.07) 0%, transparent 70%),
                    #f1f5f9;
            }

            .card {
                max-width: 440px;
                padding: 3rem 2.75rem;
            }

            .logo-wrap {
                width: 76px;
                height: 76px;
            }

            .card-header h1 {
                font-size: 1.35rem;
            }
        }
    </style>
</head>
<body>

<div class="card">

    <!-- Header -->
    <div class="card-header">
        <div class="logo-wrap">
            <img src="{{ asset('img/logo.png') }}" alt="Logo Al Azhar">
        </div>
        <h1>Al Azhar Paperless System</h1>
        <p>Masuk untuk mengelola persuratan Anda</p>
    </div>

    <!-- Error -->
    @if(session('error') || $errors->any())
        <div class="alert-error">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ session('error') ?? $errors->first() ?? 'Email atau kata sandi tidak valid.' }}</span>
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">Alamat Email</label>
            <div class="input-wrap">
                <i class="bi bi-envelope input-icon"></i>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    placeholder="nama@al-azhar.or.id"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                >
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Kata Sandi</label>
            <div class="input-wrap">
                <i class="bi bi-lock input-icon"></i>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    placeholder="Masukkan kata sandi"
                    required
                    autocomplete="current-password"
                    style="padding-right: 2.5rem;"
                >
                <button type="button" class="btn-eye" id="togglePwd" aria-label="Tampilkan sandi">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">
            <span class="btn-label">Masuk &nbsp;<i class="bi bi-arrow-right"></i></span>
            <span class="spinner"></span>
        </button>
    </form>

    <!-- Divider -->
    <div class="divider">
        <span>atau</span>
    </div>

    <!-- Google Login -->
    <a href="{{ route('google.login') }}" class="btn-google">
        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google">
        Masuk dengan Google
    </a>

    <p class="card-footer-note">
        &copy; {{ date('Y') }} Al Azhar Paperless System
    </p>
</div>

<script>
    // Toggle password
    const togglePwd = document.getElementById('togglePwd');
    const pwdInput  = document.getElementById('password');
    const eyeIcon   = document.getElementById('eyeIcon');

    togglePwd.addEventListener('click', () => {
        const show = pwdInput.type === 'password';
        pwdInput.type   = show ? 'text' : 'password';
        eyeIcon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });

    // Loading state
    document.getElementById('loginForm').addEventListener('submit', () => {
        document.getElementById('submitBtn').classList.add('is-loading');
    });
</script>

</body>
</html>