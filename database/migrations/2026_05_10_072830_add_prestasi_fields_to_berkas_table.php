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
        Schema::table('berkas', function (Blueprint $table) {
            $table->string('jenis_prestasi')->nullable()->after('status_verifikasi');
            $table->string('tingkat_prestasi')->nullable()->after('jenis_prestasi');
            $table->text('catatan_admin')->nullable()->after('tingkat_prestasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            $table->dropColumn(['jenis_prestasi', 'tingkat_prestasi', 'catatan_admin']);
        });
    }
};
