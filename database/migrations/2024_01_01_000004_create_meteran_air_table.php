<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel meteran_air — pencatatan angka meteran per bulan
     * Kolom pemakaian dihitung di application layer (MeteranAir::booted)
     * bukan generated column agar kompatibel semua DB engine.
     */
    public function up(): void
    {
        Schema::create('meteran_air', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')
                  ->constrained('pelanggan')
                  ->onDelete('cascade');
            $table->foreignId('petugas_id')
                  ->nullable()
                  ->constrained('petugas')
                  ->onDelete('set null');
            $table->unsignedTinyInteger('bulan')
                  ->comment('1-12');
            $table->unsignedSmallInteger('tahun');
            $table->decimal('angka_awal', 10, 2)->default(0)
                  ->comment('Angka meteran awal periode');
            $table->decimal('angka_akhir', 10, 2)->default(0)
                  ->comment('Angka meteran akhir periode (hasil baca lapangan)');
            $table->decimal('pemakaian', 10, 2)->default(0)
                  ->comment('angka_akhir - angka_awal, dihitung di Model::booted()');
            $table->date('tanggal_baca')
                  ->comment('Tanggal petugas membaca meteran');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Satu pelanggan hanya boleh punya 1 data per bulan/tahun
            $table->unique(['pelanggan_id', 'bulan', 'tahun'], 'uq_meteran_periode');

            // Index untuk filter dan laporan
            $table->index(['bulan', 'tahun']);
            $table->index('tanggal_baca');
            $table->index('petugas_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meteran_air');
    }
};
