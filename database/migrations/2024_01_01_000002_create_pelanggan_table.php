<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pelanggan — data pelanggan PAMSIMAS
     * Relasi: users (1:1), meteran_air (1:N), tagihan_air (1:N),
     *         pembayaran (1:N), pengaduan (1:N)
     */
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('nomor_pelanggan', 20)->unique();
            $table->string('nama_pelanggan', 150);
            $table->text('alamat');
            $table->string('rt_rw', 20)->nullable();
            $table->string('desa', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kabupaten', 100)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('no_ktp', 20)->nullable();
            $table->unsignedInteger('meteran_awal')->default(0)
                  ->comment('Angka meteran saat pertama kali daftar');
            $table->enum('status', ['aktif', 'nonaktif', 'tutup'])->default('aktif');
            $table->date('tanggal_daftar');
            $table->timestamps();

            // Index untuk pencarian
            $table->index('nama_pelanggan');
            $table->index('status');
            $table->index('desa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
