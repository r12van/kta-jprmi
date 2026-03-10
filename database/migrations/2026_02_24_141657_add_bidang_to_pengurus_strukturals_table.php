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
        Schema::table('pengurus_strukturals', function (Blueprint $table) {
            $table->string('nama_bidang')->nullable()->after('jabatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengurus_strukturals', function (Blueprint $table) {
            $table->dropColumn('nama_bidang');
        });
    }
};
