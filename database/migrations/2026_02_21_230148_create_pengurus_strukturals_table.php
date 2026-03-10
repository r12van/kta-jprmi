<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pengurus_strukturals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');
            $table->string('tingkat'); // PP, PW, PD, PC, PR
            $table->string('jabatan'); // Ketua Umum, Sekretaris, dll
            $table->string('periode_awal', 4); // Tahun mulai (ex: 2024)
            $table->string('periode_akhir', 4); // Tahun selesai (ex: 2026)
            $table->boolean('is_active')->default(true); // Status aktif menjabat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengurus_strukturals');
    }
};
