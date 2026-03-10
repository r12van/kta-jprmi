<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->string('nia', 15)->unique()->nullable(); // Format: PP.KK.YY.UUUUUU

            // 3 Jenis Anggota
            $table->enum('jenis_anggota', ['Remaja Mesjid', 'Pengurus', 'Alumni']);

            // Data Diri
            $table->string('nama_lengkap');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('no_hp')->nullable();

            // Relasi Wilayah
            $table->foreignId('provinsi_id')->constrained('indonesia_provinces');
            $table->foreignId('kota_id')->constrained('indonesia_cities');
            $table->text('alamat_lengkap');

            // Tahun Masuk untuk generate YY
            $table->year('tahun_masuk');

            // File Pendukung
            $table->string('foto_diri');
            $table->string('foto_ktp')->nullable(); // KTP bersifat opsional

            $table->string('nama_masjid')->nullable();
            $table->string('alamat_masjid')->nullable();

            // Status & Tracking
            $table->enum('status_verifikasi', ['Pending', 'Verified', 'Pending Update'])->default('Pending');
            $table->foreignId('created_by')->nullable()->constrained('users'); // Siapa yang mendaftarkan
            $table->foreignId('verified_by')->nullable()->constrained('users'); // Admin yang memverifikasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
