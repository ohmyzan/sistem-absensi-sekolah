# 📷 HadirAI

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament_v3-E5A50A?style=for-the-badge&logo=filament&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![face-api.js](https://img.shields.io/badge/Face_API.js-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

## Deskripsi

HadirAI adalah prototipe sistem absensi sekolah berbasis pengenalan wajah yang dikembangkan untuk SMK Jakarta 1. Sistem ini menggunakan `face-api.js` di browser untuk mendeteksi dan mencocokkan wajah siswa secara real-time, sehingga mempercepat proses absensi dan mengurangi risiko titip absen.

## Fitur Utama

- Absensi otomatis dengan kamera webcam.
- Pencocokan wajah di sisi klien tanpa beban berat pada server.
- Panel admin modern menggunakan Filament v3.
- Export laporan ke format CSV/Excel.
- Role-based access control untuk Admin dan Siswa.

## Role & Hak Akses

- **Admin**: Mengelola data siswa, merekam wajah, memantau statistik, dan mengunduh laporan.
- **Siswa**: Melakukan absensi mandiri melalui webcam.

## Teknologi

- Laravel
- Filament v3
- Tailwind CSS
- face-api.js
- MySQL
- Vite

## Instalasi

1. Clone repository:

   ```bash
   git clone https://github.com/<username>/absensi-sekolah.git
   cd absensi-sekolah
   ```

2. Install dependensi PHP:

   ```bash
   composer install
   ```

3. Salin file environment dan buat key aplikasi:

   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

4. Sesuaikan konfigurasi database di file `.env`.

5. Jalankan migrasi dan seed database:

   ```bash
   php artisan migrate --seed
   ```

6. Jika ingin mengompilasi aset frontend:

   ```bash
   npm install
   npm run dev
   ```

## Menjalankan Aplikasi

Jalankan server Laravel:

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000` di browser.

## Struktur Proyek

- `app/Models` — model Laravel (`User`, `Student`, `Attendance`)
- `app/Http/Controllers` — logika backend
- `app/Filament` — konfigurasi panel admin
- `public/js` — skrip frontend untuk face-api.js
- `database/migrations` — struktur tabel database

## Catatan

- Model wajah dan aset `face-api.js` disimpan di `public/models`.
- Pastikan webcam dapat diakses oleh browser dan server berjalan di `https` jika diperlukan pada produksi.

## Lisensi

Proyek ini dilisensikan di bawah MIT License.
