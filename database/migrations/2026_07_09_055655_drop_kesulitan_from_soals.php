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
            if (Schema::hasColumn('soals', 'kesulitan')) {
                $table->dropColumn('kesulitan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->string('kesulitan')->default('Sedang')->after('kategori');
        });
    }
};
