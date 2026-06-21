# 📩 Paperless Mail — Sistem Informasi Persuratan Digital

**Yayasan Pesantren Islam (YPI) Al Azhar**

> Aplikasi persuratan berbasis **Paperless** dengan arsitektur **Satu Pintu (Sentralisasi)** melalui Sekretariat YPI Al Azhar. Dibangun dengan **Laravel 12**, menangani surat internal, surat masuk eksternal, surat keluar eksternal, disposisi berjenjang, dan arsip digital terpusat.

---

## 📑 Daftar Isi

- [Arsitektur & Filosofi Sistem](#-arsitektur--filosofi-sistem)
- [Alur Kerja (Workflow)](#-alur-kerja-workflow)
  - [Surat Internal](#1-surat-internal)
  - [Surat Masuk Eksternal](#2-surat-masuk-eksternal)
  - [Surat Keluar Eksternal](#3-surat-keluar-eksternal)
- [Hak Akses & Peran (Roles)](#-hak-akses--peran-roles)
- [Fitur Lengkap](#-fitur-lengkap)
- [Skema Database (ERD)](#-skema-database-erd)
- [Struktur Routing & API](#-struktur-routing--api)
- [Struktur Direktori Proyek](#-struktur-direktori-proyek)
- [Tech Stack & Dependensi](#-tech-stack--dependensi)
- [Instalasi & Pengaturan](#-instalasi--pengaturan)
- [Akun Default Pengujian](#-akun-default-pengujian)
- [Catatan Teknis & Keamanan](#-catatan-teknis--keamanan)

---

## 🏛 Arsitektur & Filosofi Sistem

Aplikasi ini menerapkan prinsip **Sentralisasi Satu Pintu**: seluruh surat dari berbagai unit/cabang **wajib** melewati Sekretariat YPI Al Azhar sebagai pusat kendali. Tujuannya:

1. **Kontrol Terpusat** — Setiap surat tercatat, mendapat nomor agenda resmi, dan dapat dilacak riwayatnya.
2. **Hierarki Multi-Cabang** — Data terstruktur dalam tiga level: **Cabang → Unit → Pengguna**.
3. **Paperless** — Seluruh dokumen fisik didigitalisasi sebagai lampiran (PDF, DOCX, gambar) tersimpan aman di `storage`.
4. **Audit Trail** — Setiap tindakan (kirim, agenda, disposisi, tanggapan, selesai) dicatat di tabel `letter_histories`.

### Hierarki Organisasi

```
Cabang (Branch)
├── Unit A (is_sekretariat: true)  ← Pusat kendali surat
│   ├── Staf TU (Super Admin)
│   ├── Kasubag TU (Admin Disposisi)
│   └── Kepala Sekretariat (Pemantau)
├── Unit B (Direktorat Keuangan)
│   └── Staf Unit
└── ...

Cabang Daerah
├── Unit SD Islam Al Azhar 1
│   └── Staf Unit
└── ...
```

---

## 🔄 Alur Kerja (Workflow)

### 1. Surat Internal

Surat antar-unit di dalam organisasi YPI Al Azhar. Alur utama:

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  Staf Unit   │────▶│   Staf TU    │────▶│  Kasubag TU  │────▶│  Staf Unit   │────▶│   Staf TU    │
│  Buat Surat  │     │ Beri Agenda  │     │  Disposisi   │     │ Pertimbangan │     │   Selesai    │
│  (sent)      │     │ (pending_    │     │ (in_         │     │ (in_         │     │ (completed)  │
│              │     │  agenda)     │     │  review_     │     │  consider-   │     │              │
│              │     │              │     │  kasubag)    │     │  ation)      │     │              │
└──────────────┘     └──────────────┘     └──────────────┘     └──────────────┘     └──────────────┘
```

**Detail per tahap:**

| # | Tahap | Aktor | Status Surat | Aksi |
|---|-------|-------|--------------|------|
| 1 | **Pembuatan** | Staf Unit / Staf TU | `draft` atau `pending_agenda` | Membuat surat + lampiran, memilih unit tujuan |
| 2 | **Pemberian Agenda** | Staf TU | `in_review_kasubag` | Input nomor agenda YPIA, auto-disposisi ke Kasubag TU |
| 3 | **Disposisi** | Kasubag TU | `in_consideration` | Mendisposisikan surat ke unit/staf tujuan beserta catatan |
| 4 | **Pertimbangan** | Staf Unit (penerima) | `in_consideration` | Memberi respons: `pertimbangan`, `accepted`, `rejected`, `followup` |
| 5 | **Penyelesaian** | Staf TU | `completed` | Menandai surat selesai, masuk arsip |

**Status Lifecycle Surat Internal:**
```
draft → pending_agenda → in_review_kasubag → in_consideration → completed
```

### 2. Surat Masuk Eksternal

Surat dari pihak luar yang diterima oleh unit.

| # | Tahap | Aksi |
|---|-------|------|
| 1 | **Input Surat** | Staf TU/Unit mencatat surat dari pengirim eksternal |
| 2 | **Pilih Aksi** | `archive` (langsung arsip) atau `forward` (teruskan ke Sekretariat) |
| 3 | **Jika Forward** | Mengikuti alur agenda → disposisi → pertimbangan → selesai |

### 3. Surat Keluar Eksternal

Surat yang dikirim ke pihak luar organisasi.

| # | Tahap | Aksi |
|---|-------|------|
| 1 | **Pembuatan** | Staf Unit mencatat nama penerima eksternal, perihal, lampiran |
| 2 | **Status** | Langsung `completed` (arsip keluar) |
| 3 | **Update Keterangan** | Staf dapat memperbarui catatan hasil tindak lanjut |

---

## 🧑‍💼 Hak Akses & Peran (Roles)

| Role | Kode | Akses & Tanggung Jawab |
|------|------|----------------------|
| **Staf TU** | `staf_tu` | **Super Admin.** Kelola master data (Cabang, Unit, User). Input nomor agenda. Tandai surat selesai. Lihat semua surat. |
| **Kasubag TU** | `kasubag_tu` | **Admin Disposisi.** Membaca surat beragenda. Mendisposisikan ke unit/staf tujuan. Lihat semua surat. |
| **Kepala Sekretariat** | `kepala_sekretariat` | **Pemantau (Read-only).** Membaca surat dan memantau laju disposisi. Lihat semua surat. |
| **Staf Unit** | `staf_unit` | Membuat surat keluar internal & eksternal. Menerima disposisi. Memberi catatan pertimbangan. Hanya lihat surat milik sendiri/unitnya. |

### Matriks Akses Fitur

| Fitur | Staf TU | Kasubag TU | Kepala Sekretariat | Staf Unit |
|-------|:-------:|:----------:|:-----------------:|:---------:|
| Dashboard (semua statistik) | ✅ | ✅ | ✅ | ✅ (terbatas) |
| Buat Surat Internal | ✅ | ❌ | ❌ | ✅ |
| Buat Surat Masuk Eksternal | ✅ | ❌ | ❌ | ✅ |
| Buat Surat Keluar Eksternal | ✅ | ❌ | ❌ | ✅ |
| Input Nomor Agenda | ✅ | ❌ | ❌ | ❌ |
| Disposisi Surat | ✅ | ✅ | ❌ | ✅ |
| Tanggapi Disposisi | — | — | — | ✅ |
| Tandai Selesai | ✅ | ❌ | ❌ | ❌ |
| CRUD Cabang/Unit/User | ✅ | ❌ | ❌ | ❌ |
| Lihat Semua Surat | ✅ | ✅ | ✅ | ❌ |
| Cetak Lembar Disposisi | ✅ | ✅ | ✅ | ✅ |
| Edit Profil & Password | ✅ | ✅ | ✅ | ✅ |

---

## ✨ Fitur Lengkap

### 📬 Manajemen Surat
- **Surat Internal** — Pembuatan, pengiriman, draft, dan tracking status end-to-end
- **Surat Masuk Eksternal** — Pencatatan surat dari pihak luar dengan opsi arsip langsung atau teruskan
- **Surat Keluar Eksternal** — Pencatatan surat keluar ke pihak eksternal dengan keterangan tindak lanjut
- **Inbox** — Surat masuk internal dengan filter (pencarian, status, rentang tanggal) dan paginasi
- **Inbox Eksternal** — Surat masuk dari luar organisasi
- **Outbox** — Surat keluar internal milik pengguna
- **Outbox Eksternal** — Surat keluar ke pihak eksternal
- **Arsip** — Seluruh surat berstatus `completed` (internal & eksternal)
- **Lampiran Multi-File** — Upload PDF, DOC, DOCX, JPG, JPEG, PNG (maks 5MB/file)

### 📋 Disposisi & Agenda
- **Nomor Agenda YPIA** — Penomoran resmi oleh Staf TU
- **Disposisi Berjenjang** — Kasubag TU mendisposisikan ke unit/staf spesifik
- **Smart Routing** — Disposisi ke Sekretariat auto-redirect ke Staf TU / Kasubag TU sesuai hierarki
- **Tanggapan Disposisi** — Penerima memberikan respons: pertimbangan, diterima, ditolak, atau tindak lanjut
- **Timeline Disposisi** — Visualisasi riwayat perjalanan surat
- **Cetak Lembar Disposisi** — Halaman print-friendly untuk dokumentasi fisik

### 📊 Dashboard
- **Statistik Harian** — Surat masuk, keluar, belum dibaca, disposisi menunggu
- **Grafik 7 Hari** — Line chart (Chart.js) laju surat masuk, keluar, dan disposisi (khusus manager)
- **Notifikasi Real-time** — Panel "Perlu Tindakan" dengan 5 item terbaru
- **Toast Alerts** — SweetAlert2 toast untuk surat belum dibaca dan disposisi pending
- **Kontekstual per Role** — Statistik berbeda sesuai peran pengguna

### 🔐 Autentikasi & Keamanan
- **Login Email/Password** — Autentikasi standar Laravel
- **Login dengan Google (OAuth 2.0)** — Via Laravel Socialite, hanya untuk email terdaftar (restricted)
- **Role-based Middleware** — `App\Http\Middleware\Role` mengunci akses berdasarkan peran
- **Policy-based Authorization** — `LetterPolicy` mengontrol akses detail surat per user
- **Hashids** — ID surat di-encode di URL untuk mencegah enumerasi (`vinkla/hashids`)
- **Session Management** — Regenerasi session saat login, invalidasi saat logout

### 🏢 Master Data (Admin)
- **Manajemen Cabang** — CRUD dengan validasi relasi (tidak bisa hapus jika masih punya unit)
- **Manajemen Unit** — CRUD dengan flag `is_sekretariat` dan relasi ke cabang
- **Manajemen Pengguna** — CRUD dengan assignment role dan unit, password terenkripsi

### 👤 Profil Pengguna
- **Edit Profil** — Ubah nama dan email
- **Ganti Password** — Dengan verifikasi password lama

---

## 🗄 Skema Database (ERD)

### Tabel & Relasi

```
branches (Cabang)
├── id, name, timestamps

units (Unit)
├── id, branch_id (FK→branches), name, is_sekretariat, timestamps
│   └── Relasi: belongsTo Branch, hasMany Users, hasMany Letters (inbound)

users
├── id, name, email, password, role, unit_id (FK→units), google_id, timestamps
│   └── Relasi: belongsTo Unit, hasMany Letters (sent/received), hasMany Dispositions

letters (Surat)
├── id, type, letter_number, agenda_number, subject, body
├── from_user_id (FK→users), external_sender_name, created_by_user_id (FK→users)
├── external_recipient_name, external_notes
├── to_user_id (FK→users), to_unit_id (FK→units), status, timestamps
│   └── Relasi: belongsTo User (sender/recipient/creator), belongsTo Unit, hasMany Attachments/Dispositions/Histories

attachments (Lampiran)
├── id, letter_id (FK→letters), file_path, timestamps

dispositions (Disposisi)
├── id, letter_id (FK→letters), from_user_id (FK→users)
├── to_user_id (FK→users), to_unit_id (FK→units)
├── note, status, response_note, timestamps

letter_histories (Riwayat)
├── id, letter_id (FK→letters), user_id (FK→users), action, note, timestamps
```

### Nilai Status

**Letter Status:**
| Status | Deskripsi |
|--------|-----------|
| `draft` | Surat tersimpan sebagai draf |
| `sent` | Surat dikirim ke unit non-sekretariat |
| `pending_agenda` | Menunggu nomor agenda dari Staf TU |
| `in_review_kasubag` | Sudah diagendakan, menunggu disposisi Kasubag |
| `in_consideration` | Sudah didisposisikan, menunggu pertimbangan |
| `completed` | Proses surat selesai (arsip) |

**Disposition Status:**
| Status | Deskripsi |
|--------|-----------|
| `pending` | Menunggu tanggapan penerima |
| `pertimbangan` | Diberikan catatan pertimbangan |
| `accepted` | Disposisi diterima |
| `rejected` | Disposisi ditolak |
| `followup` | Perlu tindak lanjut lebih lanjut |

**Letter Type:**
| Type | Deskripsi |
|------|-----------|
| `internal` | Surat antar-unit dalam organisasi |
| `external` | Surat masuk dari pihak luar |
| `outbound_external` | Surat keluar ke pihak luar |

---

## 🛣 Struktur Routing & API

### Autentikasi
| Method | URI | Controller | Keterangan |
|--------|-----|------------|------------|
| GET | `/login` | `LoginController@showLoginForm` | Halaman login |
| POST | `/login` | `LoginController@login` | Proses login |
| POST | `/logout` | `LoginController@logout` | Logout |
| GET | `/auth/google` | `GoogleAuthController@redirect` | Redirect ke Google OAuth |
| GET | `/auth/google/callback` | `GoogleAuthController@callback` | Callback Google OAuth |

### Dashboard & Profil (auth required)
| Method | URI | Controller | Keterangan |
|--------|-----|------------|------------|
| GET | `/` | `DashboardController@index` | Dashboard utama |
| GET | `/profile` | `ProfileController@edit` | Edit profil |
| PUT | `/profile` | `ProfileController@update` | Simpan profil |
| GET | `/profile/password` | `ProfileController@showPasswordForm` | Form ganti password |
| PUT | `/profile/password` | `ProfileController@updatePassword` | Simpan password baru |

### Surat (auth required)
| Method | URI | Middleware | Keterangan |
|--------|-----|-----------|------------|
| GET | `/letters` | auth | Daftar semua surat (role-filtered) |
| GET | `/letters/create` | role:staf_unit,staf_tu | Form buat surat internal |
| POST | `/letters` | role:staf_unit,staf_tu | Simpan surat internal |
| GET | `/letters/create-external` | auth | Form input surat masuk eksternal |
| POST | `/letters/external` | auth | Simpan surat masuk eksternal |
| GET | `/letters/create-outbound-external` | role:staf_unit,staf_tu | Form surat keluar eksternal |
| POST | `/letters/outbound-external` | role:staf_unit,staf_tu | Simpan surat keluar eksternal |
| GET | `/letters/inbox` | auth | Inbox surat internal |
| GET | `/letters/inbox-external` | auth | Inbox surat eksternal |
| GET | `/letters/outbox` | auth | Outbox surat internal |
| GET | `/letters/outbox-external` | auth | Outbox surat keluar eksternal |
| GET | `/letters/arsip` | auth | Arsip surat selesai |
| GET | `/letters/{letter}` | auth + Policy | Detail surat (Hashids) |
| GET | `/letters/{letter}/print-disposition` | auth + Policy | Cetak lembar disposisi |
| POST | `/letters/{letter}/mark-read` | auth | Tandai surat dibaca |
| POST | `/letters/{letter}/update-notes` | role:staf_unit,staf_tu | Update keterangan surat eksternal |

### Disposisi (auth required)
| Method | URI | Middleware | Keterangan |
|--------|-----|-----------|------------|
| POST | `/letters/{letter}/agenda` | role:staf_tu | Beri nomor agenda |
| POST | `/letters/{letter}/complete` | role:staf_tu | Tandai selesai |
| POST | `/letters/{letter}/dispositions` | auth (role-checked) | Buat disposisi baru |
| POST | `/dispositions/{disposition}/respond` | auth (owner-checked) | Tanggapi disposisi |

### Master Data (role:staf_tu)
| Method | URI | Keterangan |
|--------|-----|------------|
| GET/POST | `/users`, `/users/create` | CRUD Pengguna |
| GET/PUT/DELETE | `/users/{user}/edit` | Edit/Hapus Pengguna |
| GET/POST | `/units` | CRUD Unit |
| PUT/DELETE | `/units/{unit}` | Edit/Hapus Unit |
| GET/POST | `/branches` | CRUD Cabang |
| PUT/DELETE | `/branches/{branch}` | Edit/Hapus Cabang |

---

## 📂 Struktur Direktori Proyek

```
persuratan/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── GoogleAuthController.php   # OAuth Google
│   │   │   │   ├── LoginController.php        # Login/Logout
│   │   │   │   └── RegisterController.php     # (tersedia)
│   │   │   ├── BranchController.php           # CRUD Cabang
│   │   │   ├── Controller.php                 # Base + authorizeRole()
│   │   │   ├── DashboardController.php        # Statistik & notifikasi
│   │   │   ├── DispositionController.php      # Agenda, disposisi, respond, selesai
│   │   │   ├── LetterController.php           # Semua operasi surat (487 baris)
│   │   │   ├── ProfileController.php          # Edit profil & password
│   │   │   ├── UnitController.php             # CRUD Unit
│   │   │   └── UserController.php             # CRUD User
│   │   └── Middleware/
│   │       └── Role.php                       # Middleware cek role
│   ├── Models/
│   │   ├── Attachment.php                     # Lampiran surat
│   │   ├── Branch.php                         # Cabang organisasi
│   │   ├── Disposition.php                    # Disposisi surat
│   │   ├── Letter.php                         # Model utama surat
│   │   ├── LetterHistory.php                  # Audit trail
│   │   ├── Unit.php                           # Unit kerja
│   │   └── User.php                           # Pengguna
│   ├── Policies/
│   │   └── LetterPolicy.php                   # Otorisasi akses surat
│   └── Providers/
├── database/
│   ├── migrations/                            # 14 file migrasi
│   └── seeders/
│       ├── DatabaseSeeder.php                 # Orchestrator utama
│       ├── UnitSeeder.php                     # Seed cabang & unit
│       ├── LetterSeeder.php                   # Seed surat contoh
│       ├── OutboundLetterSeeder.php           # Seed surat keluar
│       └── MassiveDataSeeder.php              # Seed data massal
├── resources/views/
│   ├── auth/          (login, register)
│   ├── branches/      (index)
│   ├── layouts/       (app.blade.php — master layout)
│   ├── letters/       (12 view: inbox, outbox, create, show, arsip, print, dll)
│   ├── profile/       (edit, password)
│   ├── units/         (index, create)
│   ├── users/         (index, create, edit)
│   └── dashboard.blade.php
├── routes/
│   └── web.php                                # 68 baris routing
├── public/
│   ├── css/           # Asset CSS
│   └── img/           # Asset gambar
└── config/
    └── services.php                           # Konfigurasi Google OAuth
```

---

## ⚙ Tech Stack & Dependensi

| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| **Framework** | Laravel | ^12.0 |
| **PHP** | PHP | ^8.2 |
| **Frontend Build** | Vite | ^6.2.4 |
| **CSS Framework** | TailwindCSS | ^4.0.0 |
| **OAuth** | Laravel Socialite | ^5.28 |
| **URL Encoding** | vinkla/hashids | ^13.0 |
| **Charts** | Chart.js | CDN |
| **Alerts** | SweetAlert2 | CDN |
| **Icons** | Bootstrap Icons | CDN |
| **Testing** | Pest | ^3.8 |

---

## 🚀 Instalasi & Pengaturan

### Prasyarat
- PHP ≥ 8.2 dengan ekstensi: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath
- Composer
- Node.js ≥ 18 & npm
- MySQL / MariaDB / SQLite

### Langkah Instalasi

```bash
# 1. Clone repositori
git clone https://github.com/donarazhar/paperless.git
cd paperless

# 2. Install dependency PHP
composer install

# 3. Install dependency Node.js & build assets
npm install && npm run build

# 4. Konfigurasi environment
copy .env.example .env
php artisan key:generate

# 5. Sesuaikan .env dengan database Anda
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=paperless
# DB_USERNAME=root
# DB_PASSWORD=

# 6. (Opsional) Konfigurasi Google OAuth
# GOOGLE_CLIENT_ID=your-client-id
# GOOGLE_CLIENT_SECRET=your-client-secret
# GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback

# 7. Migrasi & Seed database
# ⚠️ PERINGATAN: migrate:fresh akan MENGHAPUS seluruh data!
php artisan migrate:fresh --seed

# 8. Buat symlink storage
php artisan storage:link

# 9. Jalankan server
php artisan serve
```

### Mode Development (Concurrent)

```bash
composer dev
# Menjalankan secara bersamaan:
# - php artisan serve (server)
# - php artisan queue:listen (queue)
# - npm run dev (vite hot-reload)
```

---

## 🔑 Akun Default Pengujian

Setelah menjalankan `php artisan migrate:fresh --seed`:

| Role | Email | Password | Unit |
|------|-------|----------|------|
| **Staf TU** (Super Admin) | `staftu@example.com` | `123456` | Sekretariat YPI Al Azhar |
| **Kasubag TU** | `kasubagtu@example.com` | `123456` | Sekretariat YPI Al Azhar |
| **Kepala Sekretariat** | `kepalasekretariat@example.com` | `123456` | Sekretariat YPI Al Azhar |
| **Staf Unit** (Keuangan) | `stafunit@example.com` | `123456` | Direktorat Keuangan |
| **Staf Unit** (SD 1) | `stafsd@example.com` | `123456` | Unit SD Islam Al Azhar 1 |

---

## 🔒 Catatan Teknis & Keamanan

### Keamanan
- **URL Obfuscation** — ID surat di-encode menggunakan Hashids, mencegah sequential ID enumeration
- **Restricted Google Login** — Hanya email yang sudah terdaftar di database yang dapat login via Google
- **Role Middleware** — Middleware `role` memvalidasi akses berdasarkan peran sebelum masuk controller
- **Policy Authorization** — `LetterPolicy@view` memastikan user hanya bisa melihat surat yang berhak diakses
- **CSRF Protection** — Seluruh form POST dilindungi token CSRF Laravel
- **Password Hashing** — Menggunakan `bcrypt` via `Hash::make()`

### Teknis
- **Smart Disposition Routing** — Disposisi ke unit Sekretariat secara otomatis diarahkan ke Staf TU atau Kasubag TU berdasarkan hierarki pengirim
- **Visibility Rules** — Kasubag TU & Kepala Sekretariat hanya melihat surat di inbox setelah Staf TU memberikan agenda (bukan surat mentah)
- **File Storage** — Lampiran tersimpan di `storage/app/public/attachments`, diakses via symlink `public/storage`
- **Pagination** — Daftar surat menggunakan paginasi 15 item per halaman dengan query string preservation
- **Pencarian & Filter** — Mendukung pencarian (nomor surat, agenda, perihal, unit pengirim) dan filter (status, rentang tanggal, cabang, unit)
- **Timeline Disposisi** — Visualisasi kronologis perjalanan surat di halaman detail
- **Cetak Disposisi** — Halaman print-friendly untuk lembar disposisi fisik

### Data Seeder
- `UnitSeeder` — Membuat 2 cabang dan 3 unit dasar
- `MassiveDataSeeder` — Menghasilkan data dalam jumlah besar untuk pengujian
- `LetterSeeder` — Membuat surat contoh dengan berbagai status
- `OutboundLetterSeeder` — Membuat surat keluar contoh

---

## 📄 Lisensi

MIT License — Lihat file [LICENSE](LICENSE) untuk detail.
