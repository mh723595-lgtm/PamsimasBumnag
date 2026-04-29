<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan:
     * 1. kolom denda & total_bayar ke tagihan_air
     * 2. kolom foto_meter ke meteran_air
     * 3. kolom approval ke pelanggan & petugas (untuk fitur registrasi)
     * 4. Tabel registrasi_pending untuk antrian persetujuan admin
     */
    public function up(): void
    {
        // ── tagihan_air: tambah denda ──────────────────────────────
        Schema::table('tagihan_air', function (Blueprint $table) {
            $table->decimal('denda', 15, 2)->default(0)
                  ->after('total_tagihan')
                  ->comment('Denda keterlambatan otomatis (% × total_tagihan)');
            $table->decimal('total_bayar', 15, 2)->default(0)
                  ->after('denda')
                  ->comment('total_tagihan + denda, dihitung otomatis');
            $table->date('tanggal_denda')->nullable()
                  ->after('total_bayar')
                  ->comment('Tanggal denda pertama kali dikenakan');
        });

        // ── meteran_air: tambah foto meter ────────────────────────
        Schema::table('meteran_air', function (Blueprint $table) {
            $table->string('foto_meter')->nullable()
                  ->after('keterangan')
                  ->comment('Path foto bukti pembacaan meter (storage/public/meteran)');
        });

        // ── pelanggan: tambah status_registrasi ───────────────────
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->enum('status_registrasi', ['pending', 'approved', 'rejected'])
                  ->default('approved')
                  ->after('status')
                  ->comment('Untuk akun via self-registrasi: pending → approved/rejected');
            $table->text('catatan_registrasi')->nullable()
                  ->after('status_registrasi')
                  ->comment('Catatan admin saat approve/reject');
            $table->timestamp('approved_at')->nullable()
                  ->after('catatan_registrasi');
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->after('approved_at');
        });

        // ── petugas: tambah status_registrasi ────────────────────
        Schema::table('petugas', function (Blueprint $table) {
            $table->enum('status_registrasi', ['pending', 'approved', 'rejected'])
                  ->default('approved')
                  ->after('status')
                  ->comment('Untuk akun via self-registrasi');
            $table->text('catatan_registrasi')->nullable()
                  ->after('status_registrasi');
            $table->timestamp('approved_at')->nullable()
                  ->after('catatan_registrasi');
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->after('approved_at');
        });

        // ── setting_aplikasi: tambah konfigurasi denda ────────────
        // Diisi via PengaturanController::defaults, tidak perlu di sini
    }

    public function down(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['status_registrasi', 'catatan_registrasi', 'approved_at']);
        });

        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['status_registrasi', 'catatan_registrasi', 'approved_at']);
        });

        Schema::table('meteran_air', function (Blueprint $table) {
            $table->dropColumn('foto_meter');
        });

        Schema::table('tagihan_air', function (Blueprint $table) {
            $table->dropColumn(['denda', 'total_bayar', 'tanggal_denda']);
        });
    }
};
