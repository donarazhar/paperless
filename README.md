# 📩 Paperless Mail — Sistem Informasi Persuratan Digital

**Yayasan Pesantren Islam (YPI) Al Azhar**

> Aplikasi persuratan berbasis **Paperless** dengan arsitektur **Satu Pintu (Sentralisasi)** melalui Sekretariat YPI Al Azhar. Dibangun dengan **Laravel 12**, menangani surat internal, surat masuk eksternal, surat keluar eksternal, disposisi berjenjang, dan arsip digital terpusat.

---

## 📑 Daftar Isi

- [Arsitektur & Filosofi Sistem](#-arsitektur--filosofi-sistem)
- [Alur Kerja (Workflow)](#-alur-kerja-workflow)
- [Hak Akses & Peran (Roles)](#-hak-akses--peran-roles)
- [Fitur Lengkap](#-fitur-lengkap)
- [Skema Database (ERD)](#-skema-database-erd)
- [Struktur Routing & API](#-struktur-routing--api)
- [Instalasi & Pengaturan](#-instalasi--pengaturan)
- [Akun Default Pengujian](#-akun-default-pengujian)
- [Catatan Teknis & Keamanan](#-catatan-teknis--keamanan)

---

## 🏛 Arsitektur & Filosofi Sistem

Aplikasi ini menerapkan prinsip **Sentralisasi Satu Pintu**: seluruh pergerakan surat antar-unit/cabang terpantau dan difasilitasi melalui Sekretariat YPI Al Azhar sebagai pusat kendali.

1. **Kontrol Terpusat** — Setiap surat internal maupun eksternal mendapatkan nomor agenda resmi dan terlacak riwayatnya secara penuh.
2. **Hierarki Cabang & Unit** — Data terstruktur rapi: **Cabang → Unit → Organ (Jabatan) → Pengguna**.
3. **Paperless** — Dokumen fisik didigitalisasi sebagai lampiran (PDF, DOCX, gambar) di dalam sistem.
4. **Audit Trail** — Setiap tindakan (kirim, ACC, agenda, disposisi, tanggapan, selesai) tercatat permanen di riwayat surat.

### Hierarki Organisasi Terkini

```text
Kampus Pusat
├── Sekretariat YPI Al Azhar (Pusat Kendali Surat)
│   ├── Admin Sekretariat (Penerima & Agenda)
│   ├── Subag Persuratan (Review & Distribusi)
│   ├── Bagian TU (Manajer Disposisi Pusat)
│   └── Kepala Sekretariat (Pemantau)
├── Direktorat Keuangan
├── Masjid Agung Al Azhar
├── Bagian ITTD
└── Direktorat Dakwah Sosial

Kampus Bandung
├── Unit SD Islam Al Azhar 1
│   ├── Admin Unit
│   ├── Kepala Unit
│   └── Sub Unit

Kampus Cikarang
└── (Tersedia untuk ekspansi)
```

*(Catatan: Setiap unit non-sekretariat rata-rata memiliki 3 peran: Admin Unit, Kepala Unit, dan Sub Unit).*

---

## 🔄 Alur Kerja (Workflow)

### 1. Surat Internal (Antar Unit)

Surat yang dibuat oleh sebuah unit dan ditujukan ke unit lain dalam YPI Al Azhar.

```text
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│ Admin Unit  │────▶│ Admin Sekre │────▶│ Subag / TU  │────▶│ Admin Unit  │────▶│ Selesai /   │
│ (Pengirim)  │     │ (Beri Agenda│     │ (Disposisi) │     │ (Penerima)  │     │ Arsip       │
│ Kirim Surat │     │  & Teruskan)│     │             │     │ (Disposisi) │     │ (Terkirim)  │
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
```

**Aturan Penting Alur Internal:**
- **Privasi Kotak Masuk**: Surat **tidak akan muncul** di kotak masuk (inbox) unit penerima selama Admin Unit pengirim belum mengeklik tombol "Kirim" (masih berstatus *Draft*, *Menunggu ACC*, atau *Menunggu Dikirim*).
- **Penomoran Agenda**: Semua surat antar-unit harus melalui Admin Sekretariat untuk diberikan Nomor Agenda sebelum didisposisikan oleh Bagian TU ke unit tujuan.
- **Otonomi Penerima**: Setelah surat sampai ke unit tujuan, **Admin Unit penerima** dapat:
  1. Melakukan disposisi internal di dalam unitnya (meneruskan ke Kepala Unit / Sub Unit).
  2. Langsung mengarsipkan surat (Tandai Selesai) jika surat tersebut dirasa cukup sampai di Admin Unit saja.

### 2. Surat Masuk Eksternal

Surat dari pihak luar (eksternal) yang diterima oleh organisasi.
1. Diinput oleh Admin Sekretariat atau Admin Unit.
2. Melewati proses agenda dan review yang sama.
3. Berujung pada status **Selesai**.

### 3. Surat Keluar Eksternal

Surat yang ditujukan ke luar organisasi (eksternal).
1. Dibuat oleh Admin Unit, melalui proses ACC Kepala Unit.
2. Dikirimkan.
3. Status langsung berubah menjadi **Terkirim**. Unit dapat memperbarui kolom "Keterangan" (misal: "Resi JNE: 12345").

---

## 🧑‍💼 Hak Akses & Peran (Roles)

| Role | Tanggung Jawab Utama |
|------|----------------------|
| **`admin_sekretariat`** | Mengelola agenda surat, membuat surat masuk eksternal pusat. |
| **`subag_persuratan`** | Merekap laporan/history surat, review surat dari admin sekretariat sebelum ke Bagian TU. |
| **`bagian_tu`** | Mendisposisikan surat beragenda ke unit-unit tujuan di seluruh cabang. |
| **`kepala_sekretariat`** | Memantau seluruh laju surat masuk dan keluar secara *read-only*. |
| **`admin_unit`** | Membuat surat internal/eksternal unitnya, membagikan disposisi internal unit, atau langsung mengarsipkan surat masuk. |
| **`kepala_unit`** | Memberikan persetujuan (ACC) surat keluar unitnya, menerima disposisi, memberi arahan ke Sub Unit. |
| **`sub_unit`** | Menerima instruksi disposisi dari Kepala Unit dan melaksanakannya. |

---

## ✨ Fitur Lengkap

### 📬 Manajemen & Tracking Surat
- **Laporan & History Terpusat**: Menu History untuk memantau semua surat yang sedang berproses disposisi (khusus role Sekretariat).
- **Inbox & Outbox Cerdas**: Filter otomatis memblokir surat draf/menunggu pengiriman agar tidak membingungkan penerima.
- **Badge Status Dinamis**: Menampilkan label "Terkirim" untuk surat keluar dan "Selesai" untuk surat masuk eksternal agar konteks tata bahasa lebih sesuai.
- **Notifikasi "Tugas"**: Menu sidebar akan memunculkan *badge* notifikasi merah berisikan angka jika ada tugas ACC atau Disposisi yang menunggu tindakan.

### 📋 Disposisi Lanjutan
- **Disposisi Lintas Unit & Personal**: Disposisi dapat ditujukan ke Unit (organisasi) maupun langsung ke Personal (jabatan/orang).
- **Cetak Lembar Disposisi**: Halaman *print-friendly* untuk mencetak riwayat dan arahan disposisi secara fisik.

### 📊 Dashboard Cerdas
- Menampilkan metrik surat masuk, keluar, dan tugas pending.
- Menampilkan grafik laju surat 7 hari terakhir (*Chart.js*).

---

## 🗄 Skema Database (ERD)

**Status Lifecycle (Surat Internal):**
`draft` → `pending_approval` → `pending_sending` → `pending_agenda` → `in_review_subag` → `in_review_bagian_tu` → `in_consideration` → `completed`

*(Catatan: Saat surat `completed`, label yang tampil di UI bisa berupa **Selesai** atau **Terkirim** tergantung jenis dan asal surat).*

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

**Unit - Masjid Agung Al Azhar:**
- `adminmasjid@example.com` (Admin Unit)
- `kepalamasjid@example.com` (Kepala Unit)
- `submasjid@example.com` (Sub Unit)

**Unit - Bagian ITTD:**
- `adminittd@example.com` (Admin Unit)
- `kepalaittd@example.com` (Kepala Unit)
- `subittd@example.com` (Sub Unit)

**Unit - SD Islam Al Azhar 1 (Kampus Bandung):**
- `adminsd@example.com` (Admin Unit)
- `kasd@example.com` (Kepala Unit)
- `wakasd@example.com` (Sub Unit)

*(Buka `DatabaseSeeder.php` untuk melihat senarai lengkap pengguna).*

---

## 🔒 Catatan Teknis & Keamanan

1. **Security URL**: Semua ID pada URL disamarkan menggunakan `Hashids` (misal: `/letters/Xy7K9`).
2. **Strict Inbound Filtering**: Fungsi `LetterController@inbound` didesain untuk memastikan kerahasiaan draf surat.
3. **Role Validation**: Akses tombol (Kirim, ACC, Agenda, Arsip) dilindungi ketat oleh kondisi `Auth::user()->role` baik di level antarmuka (Blade) maupun level Controller (`abort(403)`). 

---

*Dikembangkan untuk Yayasan Pesantren Islam Al Azhar.*
