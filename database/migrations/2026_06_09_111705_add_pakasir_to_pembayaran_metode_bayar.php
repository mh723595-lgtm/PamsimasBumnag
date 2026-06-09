<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite tidak mendukung ALTER COLUMN untuk ENUM — skip jika SQLite
        if (DB::getDriverName() === 'sqlite') {
            // SQLite tidak punya tipe ENUM native, kolom dibuat sebagai VARCHAR.
            // Validasi enum dilakukan di level aplikasi (Model / FormRequest),
            // sehingga nilai 'pakasir' sudah bisa disimpan tanpa perlu ALTER.
            return;
        }

        // MySQL / MariaDB: tambah 'pakasir' ke ENUM metode_bayar
        DB::statement("
            ALTER TABLE pembayaran
            MODIFY COLUMN metode_bayar
            ENUM('tunai','transfer','lainnya','pakasir')
            NOT NULL DEFAULT 'tunai'
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Kembalikan ke enum semula (tanpa 'pakasir')
        // PERHATIAN: jika sudah ada data dengan metode_bayar='pakasir'
        // ini akan error. Pastikan sudah tidak ada sebelum rollback.
        DB::statement("
            ALTER TABLE pembayaran
            MODIFY COLUMN metode_bayar
            ENUM('tunai','transfer','lainnya')
            NOT NULL DEFAULT 'tunai'
        ");
    }
};