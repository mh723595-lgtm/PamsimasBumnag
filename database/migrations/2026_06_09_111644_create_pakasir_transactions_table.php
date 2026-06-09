<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pakasir_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tagihan_id')
                  ->constrained('tagihan_air')
                  ->onDelete('cascade');

            $table->foreignId('pelanggan_id')
                  ->constrained('pelanggan')
                  ->onDelete('cascade');

            $table->foreignId('pembayaran_id')
                  ->nullable()
                  ->constrained('pembayaran')
                  ->onDelete('set null');

            // ID transaksi yang diberikan oleh Pakasir setelah transaksi dibuat
            $table->string('pakasir_transaction_id')->nullable()->unique();

            // Referensi merchant — pakai nomor_tagihan agar unik dan traceable
            $table->string('merchant_ref')->index();

            // Nominal transaksi
            $table->decimal('amount', 15, 2);

            // Status transaksi: pending | paid | failed | expired
            $table->enum('status', ['pending', 'paid', 'failed', 'expired'])
                  ->default('pending');

            // URL halaman pembayaran yang diberikan Pakasir
            $table->text('payment_url')->nullable();

            // URL QRIS image (jika ada dari Pakasir)
            $table->text('qris_url')->nullable();

            // Waktu pembayaran dikonfirmasi Pakasir
            $table->timestamp('paid_at')->nullable();

            // Waktu kedaluwarsa transaksi
            $table->timestamp('expired_at')->nullable();

            // Raw JSON dari response createTransaction Pakasir
            $table->json('raw_response')->nullable();

            // Raw JSON dari payload webhook Pakasir
            $table->json('raw_webhook')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pakasir_transactions');
    }
};