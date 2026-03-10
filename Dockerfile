# Menggunakan base image resmi FrankenPHP dengan PHP 8.2
FROM dunglas/frankenphp:php8.2-alpine

# Set direktori kerja di dalam container
WORKDIR /app

# Install ekstensi PHP spesifik yang dibutuhkan Laravel, DataTables, Excel, & Intervention Image
RUN install-php-extensions \
    pdo_mysql \
    gd \
    intl \
    zip \
    bcmath \
    opcache \
    pcntl \
    exif

# Copy seluruh file aplikasi Anda ke dalam container
COPY . /app

# Ubah kepemilikan folder agar Nginx/FrankenPHP bisa membaca & menulis file (seperti upload KTP)
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache
