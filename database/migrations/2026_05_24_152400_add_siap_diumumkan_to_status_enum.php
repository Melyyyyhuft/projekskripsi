<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menambahkan 'siap_diumumkan' dan memastikan semua status tersedia di enum
        DB::statement("ALTER TABLE pendaftarans MODIFY COLUMN status ENUM(
            'draft', 
            'menunggu_verifikasi', 
            'revisi', 
            'lolos_admin', 
            'ditolak_admin', 
            'sudah_ujian', 
            'siap_finalisasi', 
            'siap_diumumkan',
            'diterima', 
            'tidak_diterima', 
            'tidak_mengikuti_ujian', 
            'gugur'
        ) DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembali ke versi sebelumnya (tanpa siap_diumumkan)
        DB::statement("ALTER TABLE pendaftarans MODIFY COLUMN status ENUM(
            'draft', 
            'menunggu_verifikasi', 
            'revisi', 
            'lolos_admin', 
            'ditolak_admin', 
            'sudah_ujian', 
            'siap_finalisasi', 
            'diterima', 
            'tidak_diterima', 
            'tidak_mengikuti_ujian', 
            'gugur'
        ) DEFAULT 'draft'");
    }
};
