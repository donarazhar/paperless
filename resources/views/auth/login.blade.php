<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login – Paperless Mail | Yayasan Pesantren Islam Al Azhar</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">

    <!-- Prevent caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <!-- CSRF token for JS -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0f4c81; /* Biru Dongker Premium */
            --primary-hover: #0a3d62;
            --bg-color: #f8fafc;
            --text-main: #334155;
            --text-muted: #64748b;
        }

        html, body {
            font-family: 'Inter', sans-serif !important;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        /* Decorative background shapes */
        .bg-shape-1 {
            position: absolute;
            top: -100px;
            left: -100px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(15, 76, 129, 0.05) 0%, rgba(15, 76, 129, 0) 100%);
            z-index: -1;
        }
        .bg-shape-2 {
            position: absolute;
            bottom: -150px;
            right: -100px;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(15, 76, 129, 0.05) 0%, rgba(15, 76, 129, 0) 100%);
            z-index: -1;
        }

        .login-card {
            background: #ffffff;
            border: none;
            border-radius: 1.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            padding: 3rem;
            position: relative;
        }

        /* Accent line on top of card */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--primary-color);
        }

        .brand-logo {
            width: 80px;
            height: auto;
            object-fit: contain;
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-main);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .input-group-text {
            background-color: transparent;
            border-right: none;
            color: var(--text-muted);
            padding-left: 1rem;
            border-color: #e2e8f0;
            border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem;
        }

        .form-control {
            border-left: none;
            border-color: #e2e8f0;
            padding: 0.8rem 1rem 0.8rem 0;
            color: var(--text-main);
            border-top-right-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            box-shadow: none !important;
        }

        .form-control::placeholder {
            color: #cbd5e1;
        }

        /* Hover & Focus state for input group */
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: var(--primary-color);
        }
        .input-group:focus-within .input-group-text {
            color: var(--primary-color);
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: none !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 600;
            padding: 0.8rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15, 76, 129, 0.2);
        }

        .form-check-label {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .text-muted-custom {
            color: var(--text-muted);
        }
    </style>
</head>

<body>
    <div class="bg-shape-1"></div>
    <div class="bg-shape-2"></div>

    <div class="container d-flex justify-content-center">
        <div class="card login-card">
            <div class="text-center mb-4">
                <img src="{{ asset('img/logo.png') }}" class="brand-logo" alt="Logo Al Azhar">
                <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.5px;">Paperless Mail</h4>
                <div class="fw-semibold" style="color: var(--primary-color); font-size: 0.95rem;">Yayasan Pesantren Islam Al Azhar</div>
                <div class="text-muted-custom mt-2" style="font-size: 0.85rem;">Sistem Informasi Persuratan Terpusat</div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center mb-4 border-0 shadow-sm" role="alert" style="border-radius: 0.5rem; font-size: 0.9rem;">
                    <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" id="email" name="email"
                            class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                            placeholder="nama@al-azhar.or.id" required autofocus>
                        @error('email')
                            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
                        @error('password')
                            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">Ingat sesi saya</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Masuk ke Sistem <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </form>

            <div class="text-center mt-5">
                <small class="text-muted-custom">
                    &copy; {{ date('Y') }} YPI Al Azhar. All rights reserved.
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>