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
            $table->string('kategori_kelulusan')->nullable()->after('status_kelulusan');
            $table->boolean('is_finalisasi')->default(false)->after('kategori_kelulusan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_seleksis', function (Blueprint $table) {
            $table->dropColumn(['kategori_kelulusan', 'is_finalisasi']);
        });
    }
};
