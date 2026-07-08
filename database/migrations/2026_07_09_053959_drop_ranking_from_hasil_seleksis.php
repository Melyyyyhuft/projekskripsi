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
            if (Schema::hasColumn('hasil_seleksis', 'ranking')) {
                $table->dropColumn('ranking');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_seleksis', function (Blueprint $table) {
            $table->integer('ranking')->default(0)->after('skor_akhir');
        });
    }
};
