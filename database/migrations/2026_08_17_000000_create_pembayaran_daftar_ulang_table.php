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
        Schema::create('pembayaran_daftar_ulang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
            $table->string('order_id')->nullable()->unique();
            $table->decimal('jumlah', 12, 2)->default(0);
            $table->enum('status', ['belum_bayar', 'pending', 'lunas', 'gagal', 'kedaluwarsa'])->default('belum_bayar');
            $table->string('metode_pembayaran')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_daftar_ulang');
    }
};
