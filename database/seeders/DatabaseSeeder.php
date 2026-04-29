<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Petugas;
use App\Models\MeteranAir;
use App\Models\TagihanAir;
use App\Models\Pembayaran;
use App\Models\SettingAplikasi;
use App\Models\Notifikasi;
use App\Services\TagihanService;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        // ---- USERS ----
        $admin = User::updateOrCreate(['email' => 'admin@pamsimas.id'], [
            'name'     => 'Administrator',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'is_active'=> true,
        ]);

        $petugasUser = User::updateOrCreate(['email' => 'petugas@pamsimas.id'], [
            'name'     => 'Ahmad Fauzi',
            'password' => Hash::make('password'),
            'role'     => 'petugas',
            'is_active'=> true,
        ]);

        $pelangganUser = User::updateOrCreate(['email' => 'pelanggan@pamsimas.id'], [
            'name'     => 'Budi Santoso',
            'password' => Hash::make('password'),
            'role'     => 'pelanggan',
            'is_active'=> true,
        ]);


        $this->command->info('✅ Seeder berhasil! Login: admin@pamsimas.id / password');
        $this->command->info('✅ Petugas: petugas@pamsimas.id / password');
        $this->command->info('✅ Pelanggan: pelanggan@pamsimas.id / password');
    }
}
