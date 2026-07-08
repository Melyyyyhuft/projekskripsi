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
            if (Schema::hasColumn('hasil_seleksis', 'penempatan_kelas')) {
                $table->dropColumn('penempatan_kelas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_seleksis', function (Blueprint $table) {
            $table->string('penempatan_kelas')->nullable()->after('skor_akhir');
        });
    }
};
