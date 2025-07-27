# 📩 Sistem Surat Menyurat Internal - Bank Jateng Syariah

Aplikasi Laravel untuk mencatat dan mengelola surat masuk, surat keluar, dan disposisi antar unit kerja di lingkungan internal Bank Jateng Syariah.

## ✨ Fitur Utama

- **Login & Role**
  - Admin: Kelola semua data dan user
  - Staff: Kirim dan terima surat
  - Pimpinan: Lihat semua surat unit, beri disposisi

- **Manajemen Surat**
  - Surat Masuk: Baca, download, dan tandai status
  - Surat Keluar: Isi data, kirim ke unit, atau simpan draft
  - Disposisi: Teruskan surat dengan catatan (opsional)

- **Pencarian & Filter**
  - Berdasarkan nomor surat, judul, pengirim/penerima, tanggal, status

## ⚙️ Instalasi Cepat

```bash
git clone <repo-url>
cd nama-folder

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate

php artisan migrate
php artisan serve
```

## 🧑‍💼 Role Pengguna

| Role     | Akses & Fitur                                              |
|----------|------------------------------------------------------------|
| Admin    | Kelola user, unit, dan seluruh surat                       |
| Staff    | Kirim surat keluar, baca surat masuk                       |
| Pimpinan | Lihat semua surat unit dan disposisi ke user bawahan      |

## 🗂️ Struktur Tabel Inti

- `users` → data pengguna
- `units` → unit kerja
- `letters` → data surat
- `attachments` → file lampiran
- `dispositions` → riwayat disposisi

## 📌 Catatan

- Maksimum lampiran 5MB (PDF/DOCX)
- Surat yang dikirim tidak bisa dihapus oleh selain admin
- File disimpan aman dan bisa diunduh oleh penerima
