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
            $table->dropForeign(['ujian_id']);
            $table->dropColumn('ujian_id');
            $table->string('tahun_ajaran')->default('2024/2025')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->foreignId('ujian_id')->nullable()->constrained()->onDelete('cascade');
            $table->dropColumn('tahun_ajaran');
        });
    }
};
