# 📩 Al Azhar Paperless System — Sistem Informasi Persuratan Digital

**Yayasan Pesantren Islam (YPI) Al Azhar**

> Aplikasi persuratan berbasis **Paperless** dengan arsitektur **Terdesentralisasi (Peer-to-Peer)** antar unit di lingkungan YPI Al Azhar. Mengambil konsep paperless yang ada di Gmail, aplikasi ini menawarkan antarmuka bergaya *mailbox* (Kotak Masuk, Kotak Keluar, Draf) yang intuitif, rapi, dan responsif. Setiap pergerakan surat diperlakukan layaknya pesan elektronik lengkap dengan pelacakan riwayat (*audit trail*) dan lampiran digital, menggantikan sepenuhnya kebutuhan dokumen fisik. Dibangun dengan **Laravel 12**, sistem ini menangani surat internal, surat masuk eksternal, surat keluar eksternal, disposisi berjenjang secara mandiri per-unit, dan arsip digital.

---

## 📑 Daftar Isi

- [Arsitektur & Filosofi Sistem](#-arsitektur--filosofi-sistem)
- [Alur Kerja (Workflow)](#-alur-kerja-workflow)
- [Hak Akses & Peran (Roles)](#-hak-akses--peran-roles)
- [Fitur Lengkap](#-fitur-lengkap)
- [Skema Database (ERD) & Status](#-skema-database-erd--status)
- [Instalasi & Pengaturan](#-instalasi--pengaturan)
- [Akun Default Pengujian](#-akun-default-pengujian)
- [Catatan Teknis & Keamanan](#-catatan-teknis--keamanan)

---

## 🏛 Arsitektur & Filosofi Sistem

Aplikasi ini menerapkan prinsip **Terdesentralisasi (Peer-to-Peer)**: unit-unit kerja memiliki otonomi penuh untuk saling mengirim dan merespons surat secara langsung, tanpa harus melewati birokrasi satu pintu (Sekretariat Pusat). 

1. **Otonomi Unit (Peer-to-Peer)** — Surat internal dapat dikirimkan langsung dari Unit A ke Unit B. Admin dari masing-masing unit penerima memiliki wewenang penuh untuk memberikan Nomor Agenda Masuk secara lokal dan mengatur alur disposisinya sendiri.
2. **Hierarki Cabang & Unit** — Data terstruktur rapi: **Cabang → Unit → Organ (Jabatan) → Pengguna**.
3. **Paperless** — Dokumen fisik didigitalisasi sebagai lampiran (PDF, DOCX, gambar) di dalam sistem.
4. **Audit Trail** — Setiap tindakan (kirim, ACC, agenda, disposisi, tanggapan, selesai) tercatat permanen di riwayat surat.

### Konsep Multi-Cabang & Multi-Peran

Aplikasi ini didesain agar sangat fleksibel dan *scalable* untuk menampung struktur organisasi berskala besar. Sistem mendukung konsep **Multi-Cabang** dan **Multi-Peran (Multi-Role)** dengan struktur dasar sebagai berikut:

```text
Cabang (Branch)
├── Unit Pusat (Pusat Kendali / Sekretariat Utama)
│   ├── Admin Pusat (Penerima & Agenda)
│   ├── Sub-Bagian Review
│   ├── Bagian Distribusi (Manajer Disposisi Utama)
│   └── Pimpinan Pusat (Pemantau)
├── Unit Cabang / Unit Kerja Biasa 1
│   ├── Admin Unit (Pengelola Surat Unit)
│   ├── Kepala Unit (Pemberi ACC & Disposisi)
│   └── Sub Unit / Staf (Pelaksana Disposisi)
└── Unit Cabang / Unit Kerja Biasa 2
    └── ... (Struktur peran mengikuti standar)
```

**Keterangan:**
- **Multi-Cabang**: Sistem mampu menaungi banyak wilayah operasional sekaligus secara terpusat.
- **Multi-Unit**: Setiap cabang memiliki unit-unit spesifik, dengan satu "Unit Pusat" yang difungsikan sebagai poros lalu lintas agenda (Sentralisasi Satu Pintu).
- **Multi-Peran (Role)**: Di dalam setiap unit, terdapat jabatan dan kewenangan hierarkis (*Admin*, *Kepala*, *Sub Unit*) sehingga kerahasiaan draf surat terjaga dan tugas didistribusikan kepada pihak yang tepat.

---

## 🔄 Alur Kerja (Workflow) & Menu Utama

Aplikasi ini menggunakan alur kerja yang sangat ringkas dan terpusat pada 6 menu utama, menyerupai antarmuka kotak surat (mailbox) yang familiar.

### 1. Kotak Masuk (Inbox)
Semua surat yang dikirimkan ke unit Anda akan bermuara di sini.
- Surat yang masuk akan berstatus **Baru / Belum Dibaca**.
- Admin atau Pimpinan Unit dapat membuka surat, membaca isi, melihat lampiran, dan langsung memprosesnya.
- Dari Kotak Masuk, surat dapat **Diarsipkan** atau **Didisposisikan** ke bawahan.

### 2. Terkirim (Outbox)
Menu ini berisi seluruh riwayat surat yang telah berhasil dikirimkan oleh unit Anda ke unit lain atau pihak eksternal.
- Anda dapat melacak apakah surat tersebut sudah dibaca oleh unit tujuan atau belum.
- Berisi *Audit Trail* kapan surat dikirim dan siapa pengirimnya.

### 3. Draft
Tempat penyimpanan sementara untuk surat yang sedang dibuat sebelum dikirimkan.
- **Admin** membuat konsep/draf surat di sini.
- Surat yang ada di menu Draft harus melalui proses **ACC (Persetujuan)** dari Kepala Unit sebelum sistem memvalidasinya untuk dikirim.
- Surat yang belum di-ACC dapat diedit kembali atau dihapus.

### 4. Disposisi
Alur delegasi tugas secara hierarkis.
- Saat surat masuk ke Inbox, Pimpinan dapat meneruskan (disposisi) surat tersebut beserta instruksi ke jabatan/staf di bawahnya.
- Staf penerima disposisi akan melihat daftar tugas di menu ini (atau **Disposisi Saya**).
- Staf menindaklanjuti arahan dan dapat menyelesaikan (*Complete*) disposisi tersebut.

### 5. Arsip
Pusat penyimpanan permanen untuk seluruh surat yang prosesnya telah selesai.
- Surat dari *Inbox* maupun *Disposisi* yang sudah ditandai "Selesai" akan dipindahkan ke menu Arsip.
- Tujuannya agar Kotak Masuk tetap bersih dari tumpukan surat lama, namun data tetap tersimpan rapi dan dapat dicari sewaktu-waktu.

### 6. Master Data
Menu administratif (khusus Role Admin) untuk mengelola data inti organisasi, meliputi:
- **Cabang**: Mengelola wilayah operasional atau lokasi cabang.
- **Unit Kerja**: Mengelola berbagai unit/departemen di dalam setiap cabang.
- **Organ**: Mengelola struktur jabatan (Pimpinan, Staf, dsb).
- **Pengguna**: Mengelola akun pengguna (menambah/menghapus user, *reset password*, menetapkan *role*).

---

## 🧑‍💼 Hak Akses & Peran (Roles)

| Role | Akses Menu Utama |
|------|------------------|
| **`admin_sekretariat` / `admin_unit`** | Pembuat Draft, Pengelola Inbox & Outbox, Arsip, (serta Master Data untuk Admin). |
| **`kepala_unit` / `subag_persuratan`** | Pemberi ACC Draft, Pengelola Inbox, Pemberi Disposisi. |
| **`sub_unit` / `bagian_tu`** | Penerima & Pelaksana Disposisi (Disposisi Saya). |
| **`kepala_sekretariat`** | Pemantau (*Read-only*) & Penerima Disposisi tingkat tinggi. |

---

## 🗄 Skema Lifecycle Surat

Proses perjalanan surat di dalam sistem:
`Draft` &rarr; `Menunggu ACC` &rarr; `Terkirim (Masuk ke Inbox Tujuan)` &rarr; `Didisposisikan` (Opsional) &rarr; `Selesai / Diarsipkan`

---

## 🚀 Instalasi & Pengaturan

```bash
git clone https://github.com/donarazhar/paperless.git
cd paperless
composer install
npm install && npm run build
copy .env.example .env
php artisan key:generate

# Konfigurasi Database di .env
# DB_DATABASE=paperless
# DB_USERNAME=root

# Migrasi & Seed Database (Memuat semua Cabang, Unit, & User default)
php artisan migrate:fresh --seed

# Storage Link (Wajib untuk lampiran)
php artisan storage:link

php artisan serve
```

---

## 🔑 Akun Default Pengujian

Gunakan akun-akun berikut untuk menguji *workflow* (Password untuk semua akun: `123456`):

**Sekretariat YPI Al Azhar (Pusat):**
- `admin@example.com` (Admin Sekretariat)
- `subagsurat@example.com` (Subag Persuratan)
- `kabagiantu@example.com` (Bagian TU)

**Direktorat Keuangan:**
- `adminkeuangan@example.com` (Admin Unit)
- `kepalakeuangan@example.com` (Kepala Unit)

**Bagian ITTD:**
- `adminittd@example.com` (Admin Unit)
- `kepalaittd@example.com` (Kepala Unit)

*(Buka `DatabaseSeeder.php` untuk melihat senarai lengkap pengguna).*

---

## 🔒 Catatan Teknis & Keamanan

1. **Security URL**: Semua ID pada URL disamarkan menggunakan `Hashids` (misal: `/letters/Xy7K9`).
2. **Strict Inbound Filtering**: Fungsi `LetterController@inbound` didesain untuk memastikan kerahasiaan draf surat.
3. **Role Validation**: Akses tombol (Kirim, ACC, Agenda, Arsip) dilindungi ketat oleh kondisi `Auth::user()->role` baik di level antarmuka (Blade) maupun level Controller (`abort(403)`). 

---

*Dikembangkan untuk Yayasan Pesantren Islam Al Azhar.*
