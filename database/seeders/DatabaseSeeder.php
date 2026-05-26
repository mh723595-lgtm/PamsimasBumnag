<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Petugas;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- ADMIN USER ----
        $admin = User::updateOrCreate(
            ['email' => 'admin@pamsimas.id'],
            [
                'name'      => 'Administrator',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // ---- PETUGAS USER ----
        $petugasUser = User::updateOrCreate(
            ['email' => 'petugas@pamsimas.id'],
            [
                'name'      => 'Ahmad Fauzi',
                'password'  => Hash::make('password'),
                'role'      => 'petugas',
                'is_active' => true,
            ]
        );

        // ---- PETUGAS RELATION ----
        Petugas::updateOrCreate(
            ['user_id' => $petugasUser->id],
            [
                'nama_petugas'      => 'Ahmad Fauzi',
                'jabatan'           => 'Petugas Meteran',
                'status'            => 'aktif',
                'status_registrasi' => 'approved',
                'approved_at'       => now(),
                'approved_by'       => $admin->id,
            ]
        );

        // ---- PELANGGAN USER ----
        $pelangganUser = User::updateOrCreate(
            ['email' => 'pelanggan@pamsimas.id'],
            [
                'name'      => 'Budi Santoso',
                'password'  => Hash::make('password'),
                'role'      => 'pelanggan',
                'is_active' => true,
            ]
        );

        // ---- PELANGGAN RELATION ----
        Pelanggan::updateOrCreate(
            ['user_id' => $pelangganUser->id],
            [
                'nomor_pelanggan'   => 'PLG-0001',
                'nama_pelanggan'    => 'Budi Santoso',
                'alamat'            => 'Jl. Merdeka No. 1',
                'meteran_awal'      => 0,
                'status'            => 'aktif',
                'status_registrasi' => 'approved',
                'tanggal_daftar'    => now()->toDateString(),
                'approved_at'       => now(),
                'approved_by'       => $admin->id,
            ]
        );

        // ---- OUTPUT ----
        $this->command->info('✅ Seeder berhasil!');
        $this->command->info('Admin     : admin@pamsimas.id / password');
        $this->command->info('Petugas   : petugas@pamsimas.id / password');
        $this->command->info('Pelanggan : pelanggan@pamsimas.id / password');
    }
}