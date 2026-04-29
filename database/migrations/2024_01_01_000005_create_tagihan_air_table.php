<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel tagihan_air — tagihan air per pelanggan per periode
     * Total tagihan dihitung oleh TagihanService::hitungTagihan()
     */
    public function up(): void
    {
        Schema::create('tagihan_air', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')
                  ->constrained('pelanggan')
                  ->onDelete('cascade');
            $table->foreignId('meteran_id')
                  ->nullable()
                  ->constrained('meteran_air')
                  ->onDelete('set null')
                  ->comment('Meteran yang menjadi dasar tagihan ini');
            $table->string('nomor_tagihan', 30)->unique()
                  ->comment('Format: TGH-MMYYYY-XXXX');
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->decimal('pemakaian', 10, 2)->default(0)
                  ->comment('Volume air terpakai (m³)');
            $table->decimal('total_tagihan', 15, 2)->default(0)
                  ->comment('Hasil kalkulasi TagihanService');
            $table->date('tanggal_tagihan')
                  ->comment('Tanggal tagihan diterbitkan');
            $table->date('tanggal_jatuh_tempo')
                  ->comment('Batas akhir pembayaran (biasanya akhir bulan)');
            $table->enum('status', ['belum_bayar', 'lunas', 'terlambat'])
                  ->default('belum_bayar');
            $table->timestamps();

            // Satu pelanggan hanya satu tagihan per periode
            $table->unique(['pelanggan_id', 'bulan', 'tahun'], 'uq_tagihan_periode');

            // Index untuk filter dashboard & laporan
            $table->index(['bulan', 'tahun']);
            $table->index('status');
            $table->index('tanggal_jatuh_tempo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_air');
    }
};
