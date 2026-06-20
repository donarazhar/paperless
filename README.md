# 📩 Paperless Mail - Yayasan Pesantren Islam Al Azhar

Aplikasi **Sistem Informasi Persuratan berbasis Paperless** yang dibangun dengan framework Laravel. Aplikasi ini dirancang khusus untuk memfasilitasi kebutuhan **Sistem Satu Pintu (Sentralisasi)** dan hierarki multi-cabang di lingkungan kerja Yayasan Pesantren Islam (YPI) Al Azhar.

## ✨ Arsitektur & Alur Kerja (Workflow)

Aplikasi ini menggunakan alur kerja sentralisasi ke Sekretariat YPI Al Azhar:
1. **Pembuatan Surat**: Seluruh unit (di berbagai cabang) membuat surat internal. Surat otomatis diarahkan ke pintu Sekretariat YPI Al Azhar (tidak perlu memilih penerima).
2. **Pemberian Agenda**: Surat masuk ke antrean **Staf TU Sekretariat** untuk dibaca dan diberikan Nomor Agenda YPIA.
3. **Disposisi**: Setelah mendapat nomor agenda, surat didistribusikan/didisposisikan oleh **Kasubag TU Sekretariat** ke cabang/unit terkait.
4. **Pertimbangan (Feedback)**: Staf Unit yang menerima disposisi memberikan catatan pertimbangan/respons balik kepada Sekretariat.
5. **Penyelesaian**: Staf TU meninjau kembali dan menandai perjalanan surat sebagai **Selesai**. Histori lengkap perjalanan disposisi dapat dilacak.

## 🧑‍💼 Hak Akses (Roles)

| Role                 | Akses & Tanggung Jawab                                                               |
|----------------------|--------------------------------------------------------------------------------------|
| **Staf TU**          | **Super Admin & Agenda:** Mengelola master data (Cabang, Unit, Pengguna) dan menginput Nomor Agenda surat. |
| **Kasubag TU**       | **Admin Surat:** Pemegang kuasa penuh untuk membaca dan mendisposisikan surat.     |
| **Kepala Sekretariat** | **Read-only / Pemantau:** Membaca surat dan melihat laju disposisi.                  |
| **Staf Unit**        | Membuat surat keluar dan memberikan catatan/pertimbangan terhadap disposisi masuk.   |

## 🗂️ Struktur Hierarki Data

- **Cabang (Branches)**: Kelompok wilayah atau pusat administratif.
- **Unit (Units)**: Divisi atau sekolah di bawah naungan Cabang. (Termasuk Sekretariat).
- **Pengguna (Users)**: Staf yang bertugas pada Unit tertentu.

## ⚙️ Instalasi Cepat

1. **Clone repositori**
   ```bash
   git clone https://github.com/donarazhar/paperless.git
   cd paperless
   ```

2. **Install dependency**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Pengaturan Lingkungan (.env)**
   Copy file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi *database* Anda.
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

4. **Migrasi Database & Seeder**
   *(PERHATIAN: `migrate:fresh` akan me-reset seluruh data database)*
   ```bash
   php artisan migrate:fresh --seed
   ```
   **Akun Default untuk Pengujian:**
   - Super Admin (Staf TU): `staftu@example.com` (Pass: `secret123`)

5. **Symlink Storage & Jalankan Server Lokal**
   ```bash
   php artisan storage:link
   php artisan serve
   ```

## 📌 Catatan Teknis

- Aplikasi ini menyertakan visualisasi alur persuratan (Timeline Disposisi) di dalam tampilan detail surat.
- Lampiran file dan PDF otomatis tersimpan secara aman di dalam folder `storage`.
- Semua data pengguna wajib terikat (berelasi) dengan **Unit**, dan semua Unit wajib terikat dengan **Cabang**.
