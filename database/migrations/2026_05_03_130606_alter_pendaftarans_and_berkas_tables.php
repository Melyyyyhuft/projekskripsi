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
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->string('no_hp')->nullable()->after('asal_sekolah');
        });

        Schema::table('berkas', function (Blueprint $table) {
            $table->string('jenis_berkas')->after('pendaftaran_id');
            $table->string('nama_file')->after('file_path');
            $table->enum('status_verifikasi', ['pending', 'valid', 'tidak_valid'])->default('pending')->after('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropColumn('no_hp');
        });

        Schema::table('berkas', function (Blueprint $table) {
            $table->dropColumn(['jenis_berkas', 'nama_file', 'status_verifikasi']);
        });
    }
};
