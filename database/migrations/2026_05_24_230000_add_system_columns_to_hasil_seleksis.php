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
        Schema::table('hasil_seleksis', function (Blueprint $table) {
            // Kolom untuk menyimpan nilai asli dari sistem (Mode A)
            $table->decimal('skor_sistem', 5, 2)->nullable()->after('pendaftaran_id');
            $table->decimal('bonus_sistem', 5, 2)->nullable()->after('skor_sistem');
            $table->string('penempatan_sistem')->nullable()->after('bonus_sistem');
            $table->string('kategori_sistem')->nullable()->after('penempatan_sistem');
            
            // Kolom status_proses sudah ada dari turn sebelumnya, pastikan tipenya mendukung
            // is_manual_override, overridden_by, overridden_at juga sudah ada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_seleksis', function (Blueprint $table) {
            $table->dropColumn(['skor_sistem', 'bonus_sistem', 'penempatan_sistem', 'kategori_sistem']);
        });
    }
};
