<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jorong', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jorong');
            $table->string('kode_jorong')->unique()->nullable();
            // $table->text('keterangan')->nullable();
            // $table->enum('jenis_wilayah', ['desa', 'kelurahan'])->default('desa');
            $table->boolean('aktif')->default(true);
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jorong');
    }
};