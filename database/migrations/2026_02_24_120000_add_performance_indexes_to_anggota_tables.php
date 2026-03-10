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
        Schema::table('anggota', function (Blueprint $table) {
            $table->index('status_verifikasi', 'anggota_status_verifikasi_idx');
            $table->index(['status_verifikasi', 'jenis_anggota'], 'anggota_status_jenis_idx');
            $table->index(['status_verifikasi', 'provinsi_id'], 'anggota_status_provinsi_idx');
            $table->index(['status_verifikasi', 'kota_id'], 'anggota_status_kota_idx');
            $table->index(['status_verifikasi', 'tahun_masuk'], 'anggota_status_tahun_idx');
        });

        Schema::table('anggota_drafts', function (Blueprint $table) {
            $table->index('status_approval', 'anggota_drafts_status_approval_idx');
            $table->index(['status_approval', 'created_at'], 'anggota_drafts_status_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropIndex('anggota_status_verifikasi_idx');
            $table->dropIndex('anggota_status_jenis_idx');
            $table->dropIndex('anggota_status_provinsi_idx');
            $table->dropIndex('anggota_status_kota_idx');
            $table->dropIndex('anggota_status_tahun_idx');
        });

        Schema::table('anggota_drafts', function (Blueprint $table) {
            $table->dropIndex('anggota_drafts_status_approval_idx');
            $table->dropIndex('anggota_drafts_status_created_idx');
        });
    }
};
