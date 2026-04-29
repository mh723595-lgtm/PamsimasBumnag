<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pengaduan — laporan masalah/keluhan dari pelanggan
     * Upload foto tersimpan di storage/public/pengaduan/
     */
    public function up(): void
    {
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')
                  ->constrained('pelanggan')
                  ->onDelete('cascade');
            $table->string('nomor_pengaduan', 30)->unique()
                  ->comment('Format: PGD-YYYYMMDD-XXXX');
            $table->string('judul', 200);
            $table->text('deskripsi');
            $table->string('foto')->nullable()
                  ->comment('Path relatif: pengaduan/nama-file.jpg');
            $table->enum('jenis', ['kerusakan', 'tagihan', 'pelayanan', 'lainnya'])
                  ->default('lainnya');
            $table->enum('status', ['baru', 'diproses', 'selesai', 'ditolak'])
                  ->default('baru');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi'])
                  ->default('sedang');
            $table->text('tanggapan')->nullable()
                  ->comment('Respons dari admin/petugas');
            $table->foreignId('ditangani_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->timestamp('tanggal_selesai')->nullable();
            $table->timestamps();

            // Index untuk filter dashboard
            $table->index('status');
            $table->index('prioritas');
            $table->index('jenis');
            $table->index(['pelanggan_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
