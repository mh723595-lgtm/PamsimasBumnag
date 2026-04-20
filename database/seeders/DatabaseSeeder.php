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
        // ---- SETTING APLIKASI ----
        // $settings = [
        //     ['key' => 'nama_sistem',    'value' => 'PAMSIMAS',                        'label' => 'Nama Sistem',     'tipe' => 'text',     'grup' => 'umum'],
        //     ['key' => 'nama_desa',      'value' => 'Desa Sukamaju',                   'label' => 'Nama Desa',       'tipe' => 'text',     'grup' => 'umum'],
        //     ['key' => 'alamat_kantor',  'value' => 'Jl. Raya Sukamaju No. 1',         'label' => 'Alamat Kantor',   'tipe' => 'textarea', 'grup' => 'umum'],
        //     ['key' => 'telepon',        'value' => '081234567890',                    'label' => 'Telepon',         'tipe' => 'text',     'grup' => 'umum'],
        //     ['key' => 'email_sistem',   'value' => 'admin@pamsimas.id',               'label' => 'Email Sistem',    'tipe' => 'text',     'grup' => 'umum'],
        //     ['key' => 'tarif_blok1',    'value' => '20000',                           'label' => 'Tarif Blok 1',    'tipe' => 'number',   'grup' => 'tarif'],
        //     ['key' => 'tarif_blok2',    'value' => '1500',                            'label' => 'Tarif Blok 2',    'tipe' => 'number',   'grup' => 'tarif'],
        //     ['key' => 'tarif_blok3',    'value' => '2000',                            'label' => 'Tarif Blok 3',    'tipe' => 'number',   'grup' => 'tarif'],
        //     ['key' => 'biaya_admin',    'value' => '0',                            'label' => 'Biaya Admin',     'tipe' => 'number',   'grup' => 'tarif'],
        //     ['key' => 'jatuh_tempo',    'value' => '20',                              'label' => 'Hari Jatuh Tempo','tipe' => 'number',   'grup' => 'tagihan'],
        // ];
        // foreach ($settings as $s) {
        //     SettingAplikasi::updateOrCreate(['key' => $s['key']], $s);
        // }

        // ---- USERS ----
        $admin = User::updateOrCreate(['email' => 'admin@pamsimas.id'], [
            'name'     => 'Administrator',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'is_active'=> true,
        ]);

        // $petugasUser = User::updateOrCreate(['email' => 'petugas@pamsimas.id'], [
        //     'name'     => 'Ahmad Fauzi',
        //     'password' => Hash::make('password'),
        //     'role'     => 'petugas',
        //     'is_active'=> true,
        // ]);

        // $pelangganUser = User::updateOrCreate(['email' => 'pelanggan@pamsimas.id'], [
        //     'name'     => 'Budi Santoso',
        //     'password' => Hash::make('password'),
        //     'role'     => 'pelanggan',
        //     'is_active'=> true,
        // ]);

        // // ---- PETUGAS ----
        // $petugas = Petugas::updateOrCreate(['user_id' => $petugasUser->id], [
        //     'nip'         => 'PTG-001',
        //     'nama_petugas'=> 'Ahmad Fauzi',
        //     'jabatan'     => 'Teknisi Lapangan',
        //     'no_hp'       => '081234567891',
        //     'alamat'      => 'Jl. Petugas No. 5, RT 02',
        //     'status'      => 'aktif',
        // ]);

        // // ---- PELANGGAN (10 data) ----
        // $dataPelanggan = [
        //     ['name' => 'Budi Santoso',    'email' => 'pelanggan@pamsimas.id',   'nomor' => 'PLG-0001', 'alamat' => 'Jl. Mawar No. 1 RT 01/01'],
        //     ['name' => 'Siti Rahayu',     'email' => 'siti@pamsimas.id',        'nomor' => 'PLG-0002', 'alamat' => 'Jl. Melati No. 3 RT 01/02'],
        //     ['name' => 'Hendra Wijaya',   'email' => 'hendra@pamsimas.id',      'nomor' => 'PLG-0003', 'alamat' => 'Jl. Kenanga No. 7 RT 02/01'],
        //     ['name' => 'Dewi Kusuma',     'email' => 'dewi@pamsimas.id',        'nomor' => 'PLG-0004', 'alamat' => 'Jl. Anggrek No. 12 RT 02/02'],
        //     ['name' => 'Rudi Hartono',    'email' => 'rudi@pamsimas.id',        'nomor' => 'PLG-0005', 'alamat' => 'Jl. Dahlia No. 4 RT 03/01'],
        //     ['name' => 'Ani Wijayanti',   'email' => 'ani@pamsimas.id',         'nomor' => 'PLG-0006', 'alamat' => 'Jl. Kamboja No. 8 RT 03/02'],
        //     ['name' => 'Bambang Purnomo', 'email' => 'bambang@pamsimas.id',     'nomor' => 'PLG-0007', 'alamat' => 'Jl. Tulip No. 2 RT 04/01'],
        //     ['name' => 'Rina Marlina',    'email' => 'rina@pamsimas.id',        'nomor' => 'PLG-0008', 'alamat' => 'Jl. Bougenville No. 9 RT 04/02'],
        //     ['name' => 'Doni Prasetyo',   'email' => 'doni@pamsimas.id',        'nomor' => 'PLG-0009', 'alamat' => 'Jl. Flamboyan No. 5 RT 05/01'],
        // ];

        // $pelangganList = [];
        // foreach ($dataPelanggan as $dp) {
        //     $u = User::updateOrCreate(['email' => $dp['email']], [
        //         'name'     => $dp['name'],
        //         'password' => Hash::make('password'),
        //         'role'     => 'pelanggan',
        //         'is_active'=> true,
        //     ]);
        //     $pl = Pelanggan::updateOrCreate(['nomor_pelanggan' => $dp['nomor']], [
        //         'user_id'        => $u->id,
        //         'nama_pelanggan' => $dp['name'],
        //         'alamat'         => $dp['alamat'],
        //         'rt_rw'          => '01/01',
        //         'desa'           => 'Sukamaju',
        //         'kecamatan'      => 'Sukajaya',
        //         'no_hp'          => '0812' . rand(10000000, 99999999),
        //         'meteran_awal'   => rand(100, 500),
        //         'status'         => 'aktif',
        //         'tanggal_daftar' => Carbon::now()->subMonths(rand(6, 24))->toDateString(),
        //     ]);
        //     $pelangganList[] = $pl;
        // }

        // // ---- METERAN & TAGIHAN (6 bulan terakhir) ----
        // $tagihanService = new TagihanService();
        // $nomPembayaran  = 1;

        // foreach ($pelangganList as $pl) {
        //     $angkaAwal = $pl->meteran_awal;
        //     for ($i = 5; $i >= 0; $i--) {
        //         $dt = Carbon::now()->subMonths($i);
        //         $pemakaian = rand(8, 35);
        //         $angkaAkhir = $angkaAwal + $pemakaian;

        //         $meteran = MeteranAir::updateOrCreate(
        //             ['pelanggan_id' => $pl->id, 'bulan' => $dt->month, 'tahun' => $dt->year],
        //             [
        //                 'petugas_id'   => $petugas->id,
        //                 'angka_awal'   => $angkaAwal,
        //                 'angka_akhir'  => $angkaAkhir,
        //                 'pemakaian'    => $pemakaian,
        //                 'tanggal_baca' => $dt->copy()->setDay(rand(1, 10))->toDateString(),
        //             ]
        //         );

        //         $hasil = $tagihanService->hitungTagihan($pemakaian);

        //         $nomorTagihan = 'TGH-' . str_pad($dt->month, 2, '0', STR_PAD_LEFT) . $dt->year . '-' . str_pad($pl->id * 10 + (5-$i), 4, '0', STR_PAD_LEFT);

        //         $tagihan = TagihanAir::updateOrCreate(
        //             ['pelanggan_id' => $pl->id, 'bulan' => $dt->month, 'tahun' => $dt->year],
        //             [
        //                 'meteran_id'          => $meteran->id,
        //                 'nomor_tagihan'       => $nomorTagihan,
        //                 'pemakaian'           => $pemakaian,
        //                 'total_tagihan'       => $hasil['total'],
        //                 'tanggal_tagihan'     => $dt->copy()->setDay(11)->toDateString(),
        //                 'tanggal_jatuh_tempo' => $dt->copy()->endOfMonth()->toDateString(),
        //                 'status'              => $i > 0 ? 'lunas' : (rand(0,1) ? 'lunas' : 'belum_bayar'),
        //             ]
        //         );

        //         if ($tagihan->status === 'lunas' && !$tagihan->pembayaran) {
        //             Pembayaran::create([
        //                 'tagihan_id'       => $tagihan->id,
        //                 'pelanggan_id'     => $pl->id,
        //                 'nomor_pembayaran' => 'PAY-' . str_pad($nomPembayaran++, 6, '0', STR_PAD_LEFT),
        //                 'jumlah_bayar'     => $hasil['total'],
        //                 'tanggal_bayar'    => $dt->copy()->setDay(rand(12, 25))->toDateString(),
        //                 'metode_bayar'     => ['tunai', 'transfer'][rand(0, 1)],
        //                 'status'           => 'konfirmasi',
        //                 'dikonfirmasi_oleh'=> $admin->id,
        //             ]);
        //         }

        //         $angkaAwal = $angkaAkhir;
        //     }
        // }

        // // ---- NOTIFIKASI ADMIN ----
        // Notifikasi::create([
        //     'user_id'     => $admin->id,
        //     'judul'       => 'Sistem berhasil dikonfigurasi',
        //     'pesan'       => 'Database telah diisi dengan data awal. Sistem siap digunakan.',
        //     'tipe'        => 'success',
        //     'sudah_dibaca'=> false,
        // ]);

        $this->command->info('✅ Seeder berhasil! Login: admin@pamsimas.id / password');
        // $this->command->info('✅ Petugas: petugas@pamsimas.id / password');
        // $this->command->info('✅ Pelanggan: pelanggan@pamsimas.id / password');
    }
}