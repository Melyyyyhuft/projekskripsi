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
        // Menambah status baru ke enum pendaftarans untuk mendukung flow seleksi modern:
        // - 'siap_finalisasi': Status antara setelah dihitung tapi belum dipublish.
        // - 'tidak_mengikuti_ujian': Pengganti 'gugur' untuk siswa yang tidak ikut CBT.
        // - 'gugur': Status cadangan jika diperlukan.
        
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum sebelumnya jika diperlukan
        DB::statement("ALTER TABLE pendaftarans MODIFY COLUMN status ENUM(
            'draft', 
            'menunggu_verifikasi', 
            'revisi', 
            'lolos_admin', 
            'ditolak_admin', 
            'sudah_ujian', 
            'diterima', 
            'tidak_diterima'
        ) DEFAULT 'draft'");
    }
};
