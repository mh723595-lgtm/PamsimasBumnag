<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pembayaran — rekaman pembayaran tagihan
     * Status: pending → konfirmasi/ditolak
     */
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')
                  ->constrained('tagihan_air')
                  ->onDelete('cascade');
            $table->foreignId('pelanggan_id')
                  ->constrained('pelanggan')
                  ->onDelete('cascade');
            $table->string('nomor_pembayaran', 30)->unique()
                  ->comment('Format: PAY-YYYYMMDD-XXXXX');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->date('tanggal_bayar');
            $table->enum('metode_bayar', ['tunai', 'transfer', 'lainnya'])
                  ->default('tunai');
            $table->string('bukti_bayar')->nullable()
                  ->comment('Path file bukti transfer (storage/public/bukti)');
            $table->enum('status', ['pending', 'konfirmasi', 'ditolak'])
                  ->default('pending');
            $table->foreignId('dikonfirmasi_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Index untuk laporan keuangan
            $table->index('tanggal_bayar');
            $table->index('status');
            $table->index('metode_bayar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
