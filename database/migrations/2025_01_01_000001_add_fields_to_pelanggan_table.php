<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            // Nomor meteran fisik di lapangan
            $table->string('nomor_meteran', 50)->nullable()->unique()->after('meteran_awal')
                  ->comment('Nomor seri meteran air fisik');

            // Nomor pelanggan eksternal (dari sistem lama / PDAM / dll)
            $table->string('nomor_pelanggan_external', 50)->nullable()->after('nomor_meteran')
                  ->comment('Nomor pelanggan dari sistem eksternal / lama');

            // Koordinat lokasi rumah pelanggan
            $table->decimal('latitude', 10, 7)->nullable()->after('nomor_pelanggan_external')
                  ->comment('Koordinat latitude lokasi pelanggan');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude')
                  ->comment('Koordinat longitude lokasi pelanggan');
        });
    }

    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropColumn(['nomor_meteran', 'nomor_pelanggan_external', 'latitude', 'longitude']);
        });
    }
};