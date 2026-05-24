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
            $table->decimal('bonus_sertifikat', 5, 2)->default(0)->after('skor_akhir');
            $table->string('penempatan_kelas')->nullable()->after('bonus_sertifikat');
            $table->text('alasan_penolakan')->nullable()->after('kategori_kelulusan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_seleksis', function (Blueprint $table) {
            $table->dropColumn(['bonus_sertifikat', 'penempatan_kelas', 'alasan_penolakan']);
        });
    }
};
