<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login – Al Azhar Paperless System</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }

        .login-wrapper {
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeInDown 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .login-logo {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            margin-bottom: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .login-subtitle {
            font-size: 0.9rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .login-card {
            background: #ffffff;
            border-radius: 1.25rem;
            padding: 2.5rem 2rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.02), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both;
        }

        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #475569;
            margin-bottom: 0.2rem;
        }

        .form-control {
            width: 100%;
            height: 2.8rem;
            border: none;
            border-bottom: 1.5px solid #e2e8f0;
            border-radius: 0;
            padding: 0.25rem 0 0.25rem 2.2rem;
            font-size: 0.95rem;
            font-family: inherit;
            color: #0f172a;
            background: transparent;
            outline: none;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #6366f1;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .input-icon {
            position: absolute;
            bottom: 0.75rem;
            left: 0.2rem;
            color: #64748b;
            font-size: 1.1rem;
        }

        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            background: #6366f1;
            color: #ffffff;
            border: none;
            border-radius: 100px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            box-shadow: 0 2px 10px rgba(99, 102, 241, 0.25);
        }

        .btn-submit:hover {
            background: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .login-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .login-divider span {
            padding: 0 0.75rem;
        }

        .btn-google {
            width: 100%;
            padding: 0.75rem;
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
            border-radius: 100px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-google:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }

        .error-message {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-header">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="login-logo">
            <h1 class="login-title">Al Azhar Paperless System</h1>
            <p class="login-subtitle">Masuk untuk mengelola persuratan Anda</p>
        </div>

        <div class="login-card">
            @if(session('error') || $errors->any())
                <div class="error-message">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ session('error') ?? 'Email atau kata sandi tidak valid.' }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="nama@al-azhar.or.id" value="{{ old('email') }}" required autofocus autocomplete="username">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                </div>

                <button type="submit" class="btn-submit">
                    Masuk <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="login-divider">
                <span>Atau</span>
            </div>

            <a href="{{ route('google.login') }}" class="btn-google">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" style="width: 18px; height: 18px;">
                Masuk dengan Google
            </a>
        </div>
    </div>

</body>
</html>