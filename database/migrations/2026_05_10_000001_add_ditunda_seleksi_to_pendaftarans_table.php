<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom ditunda_seleksi untuk fitur penundaan seleksi per-siswa.
     * Admin dapat menandai siswa tertentu agar tidak diproses saat seleksi dijalankan.
     */
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->boolean('ditunda_seleksi')->default(false)->after('status')
                ->comment('Tandai siswa ini untuk ditunda dari proses seleksi oleh admin');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropColumn('ditunda_seleksi');
        });
    }
};
