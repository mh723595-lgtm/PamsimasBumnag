<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            $table->string('nik', 16)->nullable()->unique()->after('nip');
            $table->string('foto')->nullable()->after('alamat');
            $table->date('tanggal_lahir')->nullable()->after('foto');
            $table->date('tmt')->nullable()->after('tanggal_lahir'); // Terhitung Mulai Tanggal bergabung
        });
    }

    public function down(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            $table->dropColumn(['nik', 'foto', 'tanggal_lahir', 'tmt']);
        });
    }
};