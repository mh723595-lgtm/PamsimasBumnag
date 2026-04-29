<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_aplikasi', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label')->nullable();
            $table->enum('tipe', ['text', 'number', 'boolean', 'textarea', 'email', 'url'])
                  ->default('text');
            $table->string('grup')->default('umum');  // umum | tarif | tagihan | notifikasi
            $table->timestamps();

            $table->index('grup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_aplikasi');
    }
};
