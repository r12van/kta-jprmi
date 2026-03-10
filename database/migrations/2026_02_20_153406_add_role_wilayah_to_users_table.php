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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Admin PD', 'Admin PW', 'Admin PP'])->default('Admin PD')->after('password');
            // Relasi ke tabel bawaan Laravolt
            $table->foreignId('provinsi_id')->nullable()->constrained('indonesia_provinces')->after('role');
            $table->foreignId('kota_id')->nullable()->constrained('indonesia_cities')->after('provinsi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['kota_id']);
            $table->dropColumn(['role', 'provinsi_id', 'kota_id']);
        });
    }
};
