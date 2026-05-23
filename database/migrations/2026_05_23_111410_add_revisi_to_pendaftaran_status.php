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
        // MySQL specific statement to add 'revisi' to the enum
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE pendaftarans MODIFY COLUMN status ENUM('draft', 'menunggu_verifikasi', 'revisi', 'lolos_admin', 'ditolak_admin', 'sudah_ujian', 'diterima', 'tidak_diterima') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE pendaftarans MODIFY COLUMN status ENUM('draft', 'menunggu_verifikasi', 'lolos_admin', 'ditolak_admin', 'sudah_ujian', 'diterima', 'tidak_diterima') DEFAULT 'draft'");
    }
};
