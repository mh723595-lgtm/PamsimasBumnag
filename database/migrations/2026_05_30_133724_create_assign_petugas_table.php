<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assign_petugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petugas_id')->constrained('petugas')->onDelete('cascade');
            $table->foreignId('jorong_id')->constrained('jorong')->onDelete('cascade');
            $table->string('periode')->default('permanen');
            $table->boolean('aktif')->default(true);
            $table->text('catatan')->nullable();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->unique(['petugas_id', 'jorong_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assign_petugas');
    }
};