# 🕌 SIM-JPRMI (Sistem Informasi Manajemen JPRMI)

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap_5-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

**SIM-JPRMI** adalah Aplikasi Sistem Informasi Manajemen berbasis Web yang dirancang khusus untuk mengelola pusat data keanggotaan dan kepengurusan Jaringan Pemuda Remaja Masjid Indonesia (JPRMI) secara nasional. 

Aplikasi ini mendukung hierarki multi-level administrator (Pusat, Wilayah/Provinsi, Daerah/Kota) dengan fitur isolasi data cerdas dan otomatisasi rekam jejak struktural anggota.

---

## ✨ Fitur Unggulan (Key Features)

- 🔐 **Multi-Level RBAC:** Pembatasan akses super ketat. Admin Provinsi (PW) hanya bisa melihat dan mengelola data provinsinya sendiri, begitu juga dengan Admin Daerah (PD).
- 📈 **Executive Dashboard:** Visualisasi data *real-time* menggunakan Chart.js (Pertumbuhan anggota, komposisi gender, sebaran usia, dan Top 10 Provinsi).
- ⚡ **Server-Side DataTables:** Pemrosesan jutaan baris data anggota tanpa *lag* menggunakan Yajra DataTables, lengkap dengan fitur pencarian dan filter wilayah kustom.
- 🪪 **Smart Member Lifecycle:** Otomatisasi perpindahan status anggota (Remaja Masjid ➔ Pengurus ➔ Alumni) berdasarkan riwayat jabatan struktural yang aktif/demisioner.
- 🛡️ **Watermark KTP Otomatis:** Keamanan data tingkat tinggi. Setiap unggahan foto KTP pendaftar akan otomatis distempel *watermark* transparan (menggunakan Intervention Image) sebelum masuk ke *database*.
- 🖨️ **Export Data Percetakan:** Satu kali klik untuk mengekspor data anggota terverifikasi (berdasarkan filter wilayah) ke Excel untuk kebutuhan cetak Kartu Tanda Anggota (KTA).

---

## 🛠️ Stack Teknologi

- **Framework:** Laravel 10.x / 11.x (PHP 8.2+)
- **Database:** MySQL / MariaDB
- **Frontend:** Bootstrap 5, FontAwesome 5/6
- **Packages Utama:**
  - `yajra/laravel-datatables-oracle` (Server-Side Tables)
  - `laravolt/indonesia` (Data Master Provinsi, Kota, Kecamatan)
  - `maatwebsite/excel` (Export Data)
  - `intervention/image` (Image Manipulation & Watermarking)

---

## 🚀 Panduan Instalasi (Local Development)

Ikuti langkah-langkah berikut untuk menjalankan project ini di komputer lokal Anda:

### 1. Kebutuhan Sistem (Prerequisites)
Pastikan Anda sudah menginstal:
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL Server (XAMPP / Laragon)

### 2. Clone & Setup Library
```bash
git clone [https://github.com/username-anda/sim-jprmi.git](https://github.com/username-anda/sim-jprmi.git)
cd sim-jprmi
composer install
npm install && npm run build
```

### 3. Konfigurasi Environment
Duplikasi file `.env.example` menjadi `.env`:
``` bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan kredensial database Anda:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_jprmi
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Key & Storage Link
_Langkah ini sangat penting agar foto profil dan KTP bisa diakses!_
```bash
php artisan key:generate
php artisan storage:link
```

### 5. Setup Database & Wilayah
Jalankan migrasi database beserta seeder bawaan untuk mengisi data admin utama dan data wilayah Indonesia.
```bash
php artisan migrate --seed
```
_(Catatan: Pastikan seeder Laravolt\Indonesia\Seeder sudah berjalan agar tabel provinsi dan kota terisi)._

### 6. ⚠️ Setup Watermark KTP (Wajib)
Aplikasi ini membutuhkan file stempel untuk KTP.
1. Siapkan logo/teks format **PNG Transparan** (misal: "Hanya untuk JPRMI").
2. Simpan file tersebut dengan nama persis `watermark-ktp.png`.
3. Letakkan file tersebut di dalam folder `public/images/watermark-ktp.png`.

### 7. Jalankan Aplikasi
```bash
php artisan serve
```
Aplikasi kini bisa diakses melalui `http://localhost:8000`.

### 🔑 Default Login Akses
Jika Anda menggunakan Seeder bawaan, gunakan kredensial berikut untuk masuk sebagai Admin Pusat (PP):
- **Email**: `admin.pp@jprmi.id`
- **Password**: `password123` _(atau sesuai yang Anda set di Seeder)_
Segera ubah password ini melalui menu *Profil Saya* di pojok kanan atas setelah Anda berhasil login!

### 👨‍💻 Kontributor
Sistem ini dikembangkan dan didesain secara khusus oleh `(https://rizvan.my.id)` untuk memenuhi standarisasi manajemen Jaringan Pemuda Remaja Masjid Indonesia.
