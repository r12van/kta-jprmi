# KTA JPRMI

Sistem informasi keanggotaan JPRMI berbasis Laravel untuk pengelolaan data anggota, kepengurusan struktural, dan pencetakan Kartu Tanda Anggota (KTA).

Repository resmi: `https://github.com/r12van/kta-jprmi`

## Stack

- Laravel 12
- PHP 8.2
- MariaDB 10.11
- Nginx
- FrankenPHP
- Vite

## Deploy Dengan Docker

Panduan ini memakai Docker Compose yang sudah ada di repository.

### 1. Clone repository

```bash
git clone https://github.com/r12van/kta-jprmi.git
cd kta-jprmi
```

### 2. Siapkan environment

Salin file environment lalu sesuaikan nilainya:

```bash
cp .env.example .env
```

Minimal ubah bagian berikut di `.env`:

```env
APP_NAME="Kartu Tanda Anggota JPRMI"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=db_jprmi
DB_USERNAME=jprmi_user
DB_PASSWORD=password_rahasia
```

Jika deployment memakai domain sendiri, ganti:

- `APP_URL` ke URL produksi Anda
- `server_name` di `nginx/default.conf`

### 3. Install dependency tanpa PHP/Node di host

Project ini dapat disiapkan penuh lewat container sementara.

Install dependency PHP:

```bash
docker run --rm -v "${PWD}:/app" -w /app composer:2 composer install --no-dev --optimize-autoloader
```

Build asset frontend:

```bash
docker run --rm -v "${PWD}:/app" -w /app node:22-alpine npm install
docker run --rm -v "${PWD}:/app" -w /app node:22-alpine npm run build
```

Jika Anda memakai PowerShell dan `${PWD}` bermasalah, ganti dengan path absolut project.

### 4. Jalankan container

```bash
docker compose up -d --build
```

Service yang dijalankan:

- `app`: Laravel di atas FrankenPHP
- `nginx`: reverse proxy publik
- `db`: MariaDB

Aplikasi akan tersedia di `http://localhost:8080`.

### 5. Inisialisasi Laravel

Jalankan perintah berikut setelah container aktif:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan storage:link
docker compose exec app php artisan migrate --seed --force
```

Opsional untuk optimasi konfigurasi:

```bash
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

### 6. Siapkan file watermark KTP

Simpan file PNG transparan berikut di path:

```text
public/images/watermark-ktp.png
```

File ini dipakai saat upload foto KTP.

### 7. Verifikasi deployment

Cek status container:

```bash
docker compose ps
```

Lihat log jika ada masalah:

```bash
docker compose logs -f
```

## Default akses awal

Jika seeder default masih digunakan, cek akun admin awal di seeder project Anda lalu segera ganti password setelah login pertama.

## Operasional singkat

Restart service:

```bash
docker compose restart
```

Matikan service:

```bash
docker compose down
```

Matikan service beserta volume database:

```bash
docker compose down -v
```

## Catatan

- Port publik default adalah `8080` untuk HTTP dan `8443` untuk HTTPS.
- Database MariaDB di-publish ke host pada port `3306`.
- Folder project di-mount ke container, jadi perubahan source code langsung terlihat oleh service.
