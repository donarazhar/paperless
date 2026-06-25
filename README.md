# 📩 Al Azhar Paperless System — Sistem Informasi Persuratan Digital

**Yayasan Pesantren Islam (YPI) Al Azhar**

> Aplikasi persuratan berbasis **Paperless** dengan arsitektur **Satu Pintu (Sentralisasi)** melalui Sekretariat YPI Al Azhar. Mengambil konsep paperless yang ada di Gmail, aplikasi ini menawarkan antarmuka bergaya *mailbox* (Kotak Masuk, Kotak Keluar, Draf) yang intuitif, rapi, dan responsif. Setiap pergerakan surat diperlakukan layaknya pesan elektronik lengkap dengan pelacakan riwayat (*audit trail*) dan lampiran digital, menggantikan sepenuhnya kebutuhan dokumen fisik. Dibangun dengan **Laravel 12**, sistem ini menangani surat internal, surat masuk eksternal, surat keluar eksternal, disposisi berjenjang, dan arsip digital terpusat.

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

Aplikasi ini menerapkan prinsip **Sentralisasi Satu Pintu**: seluruh pergerakan surat antar-unit/cabang terpantau dan difasilitasi melalui Sekretariat YPI Al Azhar sebagai pusat kendali.

1. **Kontrol Terpusat** — Setiap surat internal maupun eksternal mendapatkan nomor agenda resmi dan terlacak riwayatnya secara penuh.
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

## 🔄 Alur Kerja (Workflow)

Berikut adalah diagram visual alur kerja pada aplikasi Al Azhar Paperless System berdasarkan jenis suratnya.

### 1. Surat Internal (Antar Unit)

Surat yang dibuat oleh sebuah unit dan ditujukan ke unit lain dalam YPI Al Azhar. Proses ini wajib melewati pusat (Sekretariat) untuk pendataan nomor agenda.

```mermaid
flowchart TD
    A([Admin Unit Pengirim]) -->|1. Buat Draft| B{Butuh ACC<br>Kepala Unit?}
    B -- Ya --> C([Kepala Unit Pengirim])
    C -->|2. ACC Surat| D([Admin Unit Pengirim])
    B -- Tidak --> D
    D -->|3. Klik Kirim| E([Admin Sekretariat Pusat])
    E -->|4. Beri Nomor Agenda| F([Subag Persuratan Pusat])
    F -->|5. Review & Distribusi| G([Bagian TU Pusat])
    G -->|6. Disposisi ke Unit Tujuan| H([Admin Unit Penerima])
    H --> I{Perlu Disposisi<br>Lanjutan?}
    I -- Ya --> J([Kepala Unit Penerima])
    J -->|7. Disposisi Internal| K([Sub Unit Penerima])
    K -->|8. Tindak Lanjut| L([Selesai / Arsip])
    I -- Tidak --> L
    H -->|Langsung Arsip| L
```

**Aturan Penting Alur Internal:**
- **Privasi Kotak Masuk**: Surat **tidak akan muncul** di kotak masuk (inbox) unit penerima selama Admin Unit pengirim belum mengeklik tombol "Kirim" (masih berstatus *Draft*, *Menunggu ACC*, atau *Menunggu Dikirim*).
- **Penomoran Agenda**: Semua surat antar-unit harus melalui Admin Sekretariat untuk diberikan Nomor Agenda sebelum didisposisikan oleh Bagian TU ke unit tujuan.
- **Otonomi Penerima**: Setelah surat sampai ke unit tujuan, **Admin Unit penerima** dapat:
  1. Melakukan disposisi internal di dalam unitnya (meneruskan ke Kepala Unit / Sub Unit).
  2. Langsung mengarsipkan surat (Tandai Selesai) jika dirasa cukup di level Admin Unit saja.

### 2. Surat Masuk Eksternal

Surat dari pihak luar (eksternal) yang diterima oleh organisasi. Surat ini masuk ke satu pintu Sekretariat untuk kemudian didistribusikan.

```mermaid
flowchart TD
    A(Pihak Luar / Eksternal) -->|Kirim Fisik / Email| B([Admin Sekretariat])
    B -->|1. Input Surat & Agenda| C([Subag Persuratan])
    C -->|2. Review| D([Bagian TU])
    D -->|3. Disposisi ke Unit| E([Admin Unit Tujuan])
    E -->|4. Teruskan Disposisi| F([Kepala Unit / Sub Unit])
    F -->|5. Tindak Lanjut| G([Selesai / Arsip])
```

### 3. Surat Keluar Eksternal

Surat yang ditujukan ke luar organisasi (eksternal). Tidak perlu melalui sentralisasi Sekretariat (nomor surat dan agenda dikelola sendiri oleh unit jika ada kebijakan masing-masing).

```mermaid
flowchart TD
    A([Admin Unit]) -->|1. Buat Draft Eksternal| B([Kepala Unit])
    B -->|2. ACC Surat| C([Admin Unit])
    C -->|3. Kirim Fisik / Email ke Eksternal| D([Status Terkirim])
    D -.->|Update Keterangan Resi| D
```
*Status langsung berubah menjadi **Terkirim**. Unit dapat memperbarui kolom "Keterangan" (misal: "Resi JNE: 12345").*

---

## 🧑‍💼 Hak Akses & Peran (Roles)

| Role | Tanggung Jawab Utama |
|------|----------------------|
| **`admin_sekretariat`** | Mengelola agenda surat, membuat surat masuk eksternal pusat, memberikan nomor agenda ke surat internal. |
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
- **Badge Status Dinamis**: Menampilkan label "Terkirim" untuk surat keluar dan "Selesai" untuk surat masuk eksternal.
- **Notifikasi "Tugas"**: Menu sidebar akan memunculkan *badge* notifikasi merah berisikan angka jika ada tugas ACC atau Disposisi yang menunggu tindakan.

### 📋 Disposisi Lanjutan
- **Disposisi Lintas Unit & Personal**: Disposisi dapat ditujukan ke Unit (organisasi) maupun langsung ke Personal (jabatan/orang).
- **Cetak Lembar Disposisi**: Halaman *print-friendly* untuk mencetak riwayat dan arahan disposisi secara fisik.


## 🗄 Skema Database (ERD) & Status

**Status Lifecycle (Surat Internal):**
`draft` → `pending_approval` (Menunggu ACC) → `pending_sending` (Menunggu Dikirim Admin) → `pending_agenda` (Menunggu Agenda Pusat) → `in_review_subag` (Review Subag) → `in_review_bagian_tu` (Review TU) → `in_consideration` (Dipertimbangkan Unit Tujuan) → `completed` (Selesai/Arsip)

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
