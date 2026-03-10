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
            $table->text('alamat_domisili')->nullable()->after('alamat_lengkap');
            $table->unsignedBigInteger('kecamatan_id')->nullable()->after('kota_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn(['alamat_domisili', 'kecamatan_id']);
        });
    }
};
