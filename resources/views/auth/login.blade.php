<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login – MailApp | Bank Jateng Syariah Pekalongan</title>

    <!-- Prevent caching (hindari CSRF token kadaluarsa pada reload) -->
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        html,
       body {
    font-family: 'Inter', sans-serif !important;
    background: url('{{ asset('img/gedung-bank-jateng.jpg') }}') no-repeat center center fixed;
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    min-height: 100vh;
    position: relative;
    z-index: 1;
}

body::before {
    content: '';
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.4); /* gelapkan background */
    z-index: -1;
}


        .login-card {
            border: none;
            border-radius: 1.5rem;
            box-shadow: 0 2px 24px rgba(0, 0, 0, 0.12);
        }

        .login-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    color: white;
}
.login-card .form-label,
.login-card .form-check-label,
.login-card .text-muted,
.login-card .text-primary-emphasis {
    color: white !important;
}
.login-card .form-control {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255,255,255,0.3);
}
.login-card .form-control::placeholder {
    color: #e0e0e0;
}


        .brand-logo {
            width: 85px;
            height: 58px;
            object-fit: contain;
        }

        .btn-primary {
            background-color: #005f9e;
            border-color: #005f9e;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #004a7c;
            border-color: #004a7c;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-md-8 col-lg-5">
                <div class="card login-card p-4">
                    <div class="text-center mb-4">
                        <img src="https://bucket-api.baznas.go.id/bucket-api/file?bucket=bzn-fdr-smb-p5739641&file=attachments/rekening/172292650008666680_497-v2-Bank-Jateng-Syariah.png"
                            class="brand-logo mb-2" alt="Logo Bank Jateng">
                        <h4 class="fw-bold mb-1">MailApp</h4>
                        <div class="text-primary-emphasis mb-2">Bank Jateng Syariah Pekalongan</div>
                        <div class="text-muted small">Sistem Surat Menyurat Internal</div>
                    </div>

                    {{-- Tampilkan flash error (login gagal / token expired) --}}
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-at-fill"></i></span>
                                <input type="email" id="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                    required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                <input type="password" id="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">Ingat Saya</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </button>
                    </form>

                    <hr class="my-4">
                    <div class="text-center text-muted small">
                        &copy; {{ date('Y') }} MailApp | RPL SMK Muhammadiyah Kesesi
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>