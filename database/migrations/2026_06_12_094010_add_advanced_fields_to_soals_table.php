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
        Schema::table('soals', function (Blueprint $table) {
            $table->string('kategori')->default('Pilihan Ganda')->after('mapel');
            $table->string('kesulitan')->default('Sedang')->after('kategori');
            $table->integer('bobot')->default(5)->after('kesulitan');
            $table->string('gambar')->nullable()->after('bobot');
            $table->text('penjelasan')->nullable()->after('teks_soal');
            $table->string('status')->default('Aktif')->after('jawaban_benar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'kesulitan', 'bobot', 'gambar', 'penjelasan', 'status']);
        });
    }
};
