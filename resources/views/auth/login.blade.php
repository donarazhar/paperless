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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            padding: 1rem;
            overflow: hidden; /* Mencegah scroll */
        }

        /* ── Card ── */
        .card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 1.25rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07),
                        0 20px 40px -8px rgba(0,0,0,0.1);
            max-height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* ── Header ── */
        .card-header {
            text-align: center;
            margin-bottom: 1.25rem;
        }

        .logo-wrap {
            width: 56px;
            height: 56px;
            margin: 0 auto 0.75rem;
            border-radius: 14px;
            overflow: hidden;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
        }

        .logo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .card-header h1 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .card-header p {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-top: 0.25rem;
        }

        /* ── Alert Error ── */
        .alert-error {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #be123c;
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .alert-error i {
            font-size: 0.9rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* ── Form ── */
        .form-group {
            margin-bottom: 0.75rem;
        }

        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: 2.6rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.65rem;
            padding: 0 0.9rem 0 2.25rem;
            font-size: 0.85rem;
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
            height: 2.6rem;
            margin-top: 1rem;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: #fff;
            border: none;
            border-radius: 0.65rem;
            font-size: 0.9rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 14px rgba(29,78,216,0.35);
            transition: transform 0.18s, box-shadow 0.18s, opacity 0.18s;
        }

        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(29,78,216,0.42); }
        .btn-submit:active { transform: translateY(0); box-shadow: 0 2px 8px rgba(29,78,216,0.3); }
        .btn-submit.is-loading { opacity: 0.8; pointer-events: none; }

        .spinner {
            display: none; width: 14px; height: 14px;
            border: 2px solid rgba(255,255,255,0.35); border-top-color: #fff; border-radius: 50%;
            animation: spin 0.65s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-submit.is-loading .btn-label { display: none; }
        .btn-submit.is-loading .spinner { display: block; }

        /* ── Divider ── */
        .divider { display: flex; align-items: center; gap: 0.5rem; margin: 1rem 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
        .divider span { font-size: 0.7rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; }

        /* ── Google ── */
        .btn-google {
            width: 100%;
            height: 2.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: #fff;
            color: #334155;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.65rem;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.18s;
        }

        .btn-google:hover { border-color: #93c5fd; background: #eff6ff; color: #1d4ed8; }
        .btn-google img { width: 16px; height: 16px; }

        /* ── Footer note ── */
        .card-footer-note {
            margin-top: 1rem;
            text-align: center;
            font-size: 0.7rem;
            color: #94a3b8;
            line-height: 1.4;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 10px;
        }

        .footer-links a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.8rem;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        /* ── Modal CSS ── */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            padding: 1rem;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-content {
            background: #fff;
            width: 100%;
            max-width: 600px;
            border-radius: 1.25rem;
            padding: 1.25rem;
            transform: translateY(20px);
            transition: transform 0.3s ease;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-height: 90vh;
            overflow-y: auto;
        }

        @media (min-width: 768px) {
            .modal-content {
                padding: 2rem;
                max-width: 700px;
            }
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .modal-header h2 {
            font-size: 1.15rem;
            color: #0f172a;
            font-weight: 700;
        }

        @media (min-width: 768px) {
            .modal-header h2 { font-size: 1.25rem; }
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #64748b;
            cursor: pointer;
            transition: color 0.2s;
            line-height: 1;
        }

        .btn-close:hover {
            color: #ef4444;
        }

        .modal-body {
            font-size: 0.85rem;
            color: #334155;
            line-height: 1.5;
        }
        
        @media (min-width: 768px) {
            .modal-body { font-size: 0.9rem; line-height: 1.6; }
        }

        .modal-body h4 {
            color: #0f172a;
            margin: 0 0 0.25rem;
            font-size: 0.95rem;
        }

        .modal-body ul, .modal-body ol {
            padding-left: 1.25rem;
            margin-bottom: 1rem;
        }

        .modal-body p {
            margin-bottom: 1rem;
        }

        .help-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            margin-top: 1rem;
        }

        @media (min-width: 768px) {
            .help-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .contact-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
        }

        .contact-card:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .contact-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        /* ── Responsive ── */

        /* Tablet (600px – 899px) */
        @media (min-width: 600px) {
            body { background: #e8edf5; }

            .card {
                padding: 2rem 2.25rem;
                border: 1px solid #e2e8f0;
                max-width: 380px;
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
                max-width: 400px;
                padding: 2.25rem 2.5rem;
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

    <div style="margin-top: 1.5rem; text-align: center;">
        <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 1.25rem;">
            Silakan masuk menggunakan akun PresensiGPS Anda.
        </p>

        <!-- Presensi SSO Login -->
        <a href="{{ route('presensi.login') }}" class="btn-submit" style="text-decoration: none;">
            <i class="bi bi-shield-lock" style="font-size: 1.1rem;"></i>
            Masuk via SSO PresensiGPS
        </a>
    </div>

    <div class="footer-links">
        <a href="#" onclick="openModal('modal-tentang'); return false;">Tentang</a>
        <a href="#" onclick="openModal('modal-bantuan'); return false;">Bantuan</a>
        <a href="#" onclick="openModal('modal-kontak'); return false;">Kontak</a>
    </div>

    <p class="card-footer-note">
        &copy; {{ date('Y') }} Al Azhar Paperless System <br>
        Dibuat oleh DAL Army (2026)
    </p>
</div>

<!-- Modal Tentang -->
<div class="modal-overlay" id="modal-tentang" onclick="closeModal(event, 'modal-tentang')">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2>Tentang Aplikasi</h2>
            <button class="btn-close" onclick="closeModal(null, 'modal-tentang')"><i class="bi bi-x"></i></button>
        </div>
        <div class="modal-body">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width: 70px; margin-bottom: 10px;">
                <h3 style="color: #0f172a; font-weight: 700; margin-bottom: 0.25rem;">Al Azhar Paperless System (APS)</h3>
                <p style="color: #64748b; font-size: 0.85rem;">Sistem Informasi Persuratan Digital</p>
            </div>
            <p><strong>Al Azhar Paperless System (APS)</strong> adalah aplikasi persuratan berbasis <em>Paperless</em> dengan arsitektur <strong>Terdesentralisasi (Peer-to-Peer)</strong> antar unit di lingkungan Yayasan Pesantren Islam (YPI) Al Azhar.</p>
            <p>Mengambil inspirasi dari tata letak Gmail, sistem ini menawarkan antarmuka bergaya <em>mailbox</em> yang intuitif dan rapi. Aplikasi ini menghilangkan kebutuhan dokumen fisik dengan menjadikan setiap surat elektronik, yang dilengkapi dengan pelacakan riwayat (<em>audit trail</em>) dan lampiran dokumen.</p>
            <ul>
                <li><strong>Otonomi Unit:</strong> Surat internal dapat dikirimkan secara langsung (Peer-to-Peer) antar unit tanpa harus melewati birokrasi satu pintu.</li>
                <li><strong>Multi-Cabang & Multi-Peran:</strong> Mendukung struktur organisasi kompleks (Cabang &rarr; Unit &rarr; Organ &rarr; Pengguna).</li>
            </ul>
            <p>Aplikasi ini dibuat dan dikembangkan pada tahun <strong>2026</strong> oleh tim pengembang <strong>DAL Army</strong>, untuk mempercepat birokrasi dan mempermudah pelacakan dokumen surat menyurat.</p>
        </div>
    </div>
</div>

<!-- Modal Bantuan -->
<div class="modal-overlay" id="modal-bantuan" onclick="closeModal(event, 'modal-bantuan')">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2>Bantuan & Alur Kerja</h2>
            <button class="btn-close" onclick="closeModal(null, 'modal-bantuan')"><i class="bi bi-x"></i></button>
        </div>
        <div class="modal-body">
            <p>Aplikasi ini memiliki alur kerja yang sangat ringkas dan terpusat pada 6 menu utama:</p>
            
            <div class="help-grid">
                <div>
                    <h4>1. Kotak Masuk (Inbox)</h4>
                    <p style="margin-bottom:0">Semua surat yang dikirimkan ke unit Anda akan bermuara di sini. Admin/Pimpinan dapat membaca dan langsung memproses surat (Arsip/Disposisi).</p>
                </div>
                <div>
                    <h4>2. Terkirim (Outbox)</h4>
                    <p style="margin-bottom:0">Berisi seluruh riwayat surat yang telah berhasil dikirimkan oleh unit Anda, lengkap dengan status baca dan pelacakan (<em>Audit Trail</em>).</p>
                </div>
                <div>
                    <h4>3. Draft</h4>
                    <p style="margin-bottom:0">Tempat pembuatan konsep surat oleh Admin yang wajib di-<strong>ACC (Disetujui)</strong> oleh Kepala Unit sebelum dapat dikirimkan.</p>
                </div>
                <div>
                    <h4>4. Disposisi</h4>
                    <p style="margin-bottom:0">Alur delegasi tugas. Pimpinan dapat meneruskan surat masuk beserta instruksi ke jabatan/staf di bawahnya untuk ditindaklanjuti hingga selesai.</p>
                </div>
                <div>
                    <h4>5. Arsip</h4>
                    <p style="margin-bottom:0">Pusat penyimpanan permanen untuk seluruh surat (dari Inbox maupun Disposisi) yang prosesnya telah ditandai "Selesai".</p>
                </div>
                <div>
                    <h4>6. Master Data</h4>
                    <p style="margin-bottom:0">Menu administratif (khusus Admin) untuk mengelola data inti organisasi seperti Cabang, Unit Kerja, Organ (Jabatan), dan Pengguna.</p>
                </div>
            </div>

            <hr style="margin: 1.5rem 0; border: 0; border-top: 1px solid #e2e8f0;">
            <p style="font-size: 0.85rem; color: #64748b;">Jika Anda mengalami kendala login, silakan gunakan menu <strong>Kontak</strong>.</p>
        </div>
    </div>
</div>

<!-- Modal Kontak -->
<div class="modal-overlay" id="modal-kontak" onclick="closeModal(event, 'modal-kontak')">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2>Hubungi Kami</h2>
            <button class="btn-close" onclick="closeModal(null, 'modal-kontak')"><i class="bi bi-x"></i></button>
        </div>
        <div class="modal-body">
            <p>Jika Anda mengalami kesulitan akses, lupa kata sandi, atau membutuhkan bantuan teknis terkait aplikasi ini, silakan hubungi tim dukungan kami:</p>
            
            <a href="https://wa.me/6288214740182" target="_blank" class="contact-card">
                <div class="contact-icon" style="background-color: #25D366;">
                    <i class="bi bi-whatsapp"></i>
                </div>
                <div>
                    <strong style="display: block; color: #0f172a;">WhatsApp Support</strong>
                    <span style="color: #64748b; font-size: 0.85rem;">0882-1474-0182</span>
                </div>
            </a>

            <a href="mailto:donarazhar@gmail.com" class="contact-card">
                <div class="contact-icon" style="background-color: #ef4444;">
                    <i class="bi bi-envelope"></i>
                </div>
                <div>
                    <strong style="display: block; color: #0f172a;">Email Support</strong>
                    <span style="color: #64748b; font-size: 0.85rem;">donarazhar@gmail.com</span>
                </div>
            </a>
        </div>
    </div>
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

    // Modal Logic
    function openModal(id) {
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden'; // Mencegah scroll di background
    }

    function closeModal(event, id) {
        if (event && event.target !== event.currentTarget) return; // Hanya tutup jika klik overlay atau tombol X
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = 'auto'; // Mengembalikan scroll
    }
</script>

</body>
</html>