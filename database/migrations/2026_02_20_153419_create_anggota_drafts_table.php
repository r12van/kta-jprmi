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
        Schema::create('anggota_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnDelete();

            // Kolom yang diajukan untuk diubah (bisa ditambah sesuai kebutuhan form edit)
            $table->enum('jenis_anggota_baru', ['Remaja Mesjid', 'Pengurus', 'Alumni'])->nullable();
            $table->string('nama_lengkap_baru')->nullable();
            $table->text('alamat_lengkap_baru')->nullable();
            $table->string('foto_diri_baru')->nullable();

            // Tracking Approval
            $table->foreignId('diajukan_oleh')->constrained('users'); // Admin PD yang mengedit
            $table->enum('status_approval', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu');
            $table->foreignId('reviewed_by')->nullable()->constrained('users'); // Admin PW/PP yang memproses
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_drafts');
    }
};
