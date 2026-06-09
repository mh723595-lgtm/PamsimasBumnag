<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Petugas;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================
        //  USERS — Admin, Petugas, Pelanggan
        // =========================================================

        // ---- ADMIN (hanya 1) ----
        $admin = User::updateOrCreate(
            ['email' => 'admin@pamsimas.id'],
            [
                'name'      => 'Administrator',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // ---- PETUGAS USERS ----
        $petugasUserData = [
            ['email' => 'febrinur@pamsimas.id',        'name' => 'Febrinur'],
            ['email' => 'yuli.indrawati@pamsimas.id',  'name' => 'Yuli Indrawati'],
            ['email' => 'novia.riani@pamsimas.id',     'name' => 'Novia Riani'],
            ['email' => 'hardi.pranata@pamsimas.id',   'name' => 'Hardi Pranata'],
            ['email' => 'hendri.awarman@pamsimas.id',  'name' => 'Hendri Awarman'],
            ['email' => 'rinaldi@pamsimas.id',         'name' => 'Rinaldi'],
            ['email' => 'dian.wahyuni@pamsimas.id',    'name' => 'Dian Wahyuni'],
        ];

        $petugasUserObjs = [];
        foreach ($petugasUserData as $pu) {
            $petugasUserObjs[] = User::updateOrCreate(
                ['email' => $pu['email']],
                [
                    'name'      => $pu['name'],
                    'password'  => Hash::make('password'),
                    'role'      => 'petugas',
                    'is_active' => true,
                ]
            );
        }

        // ---- PELANGGAN USERS ----
        $pelangganUserData = [
            ['email' => 'gusmayanti@pamsimas.id',    'name' => 'Gusmayanti'],
            ['email' => 'reno.sari@pamsimas.id',     'name' => 'Reno Sari'],
            ['email' => 'yarti@pamsimas.id',         'name' => 'Yarti'],
            ['email' => 'andra.wijaya@pamsimas.id',  'name' => 'Andra Wijaya'],
            ['email' => 'darnawati@pamsimas.id',     'name' => 'Darnawati'],
            ['email' => 'yusnimar@pamsimas.id',      'name' => 'Yusnimar'],
            ['email' => 'silvi@pamsimas.id',         'name' => 'Silvi'],
            ['email' => 'ayu.ardila@pamsimas.id',    'name' => 'Ayu Ardila'],
            ['email' => 'irfan@pamsimas.id',         'name' => 'Irfan'],
            ['email' => 'gusti.edrianto@pamsimas.id','name' => 'Gusti Edrianto'],
            ['email' => 'mansur@pamsimas.id',        'name' => 'Mansur'],
        ];

        $pelangganUserObjs = [];
        foreach ($pelangganUserData as $pu) {
            $pelangganUserObjs[] = User::updateOrCreate(
                ['email' => $pu['email']],
                [
                    'name'      => $pu['name'],
                    'password'  => Hash::make('password'),
                    'role'      => 'pelanggan',
                    'is_active' => true,
                ]
            );
        }

        // =========================================================
        //  JORONG
        // =========================================================

        $jorongData = [
            [
                'nama_jorong'   => 'Jorong Pincuran Tujuah',
                'kode_jorong'   => 'JRG-001',
                'keterangan'    => 'Wilayah jorong Pincuran Tujuah',
                'jenis_wilayah' => 'desa',
                'aktif'         => true,
                'provinsi'      => 'Sumatera Barat',
                'kabupaten'     => 'Agam',
                'kecamatan'     => 'Tanjung Raya',
                'desa'          => 'Bayua',
                'nagari'        => 'Nagari Bayua',
                'dibuat_oleh'   => $admin->id,
            ],
            [
                'nama_jorong'   => 'Jorong Kapalo Koto',
                'kode_jorong'   => 'JRG-002',
                'keterangan'    => 'Wilayah jorong Kapalo Koto',
                'jenis_wilayah' => 'desa',
                'aktif'         => true,
                'provinsi'      => 'Sumatera Barat',
                'kabupaten'     => 'Agam',
                'kecamatan'     => 'Tanjung Raya',
                'desa'          => 'Bayua',
                'nagari'        => 'Nagari Bayua',
                'dibuat_oleh'   => $admin->id,
            ],
            [
                'nama_jorong'   => 'Jorong Lubuak Anyia',
                'kode_jorong'   => 'JRG-003',
                'keterangan'    => 'Wilayah jorong Lubuak Anyia',
                'jenis_wilayah' => 'desa',
                'aktif'         => true,
                'provinsi'      => 'Sumatera Barat',
                'kabupaten'     => 'Agam',
                'kecamatan'     => 'Tanjung Raya',
                'desa'          => 'Bayua',
                'nagari'        => 'Nagari Bayua',
                'dibuat_oleh'   => $admin->id,
            ],
            [
                'nama_jorong'   => 'Jorong Banda Tangah',
                'kode_jorong'   => 'JRG-004',
                'keterangan'    => 'Wilayah jorong Banda Tangah',
                'jenis_wilayah' => 'desa',
                'aktif'         => true,
                'provinsi'      => 'Sumatera Barat',
                'kabupaten'     => 'Agam',
                'kecamatan'     => 'Tanjung Raya',
                'desa'          => 'Bayua',
                'nagari'        => 'Nagari Bayua',
                'dibuat_oleh'   => $admin->id,
            ],
            [
                'nama_jorong'   => 'Jorong Lubuak Kandang',
                'kode_jorong'   => 'JRG-005',
                'keterangan'    => 'Wilayah jorong Lubuak Kandang',
                'jenis_wilayah' => 'desa',
                'aktif'         => true,
                'provinsi'      => 'Sumatera Barat',
                'kabupaten'     => 'Agam',
                'kecamatan'     => 'Tanjung Raya',
                'desa'          => 'Bayua',
                'nagari'        => 'Nagari Bayua',
                'dibuat_oleh'   => $admin->id,
            ],
        ];

        $jorongIds = [];
        foreach ($jorongData as $jorong) {
            $existing = DB::table('jorong')->where('kode_jorong', $jorong['kode_jorong'])->first();
            if ($existing) {
                DB::table('jorong')->where('id', $existing->id)->update(array_merge($jorong, ['updated_at' => now()]));
                $jorongIds[$jorong['kode_jorong']] = $existing->id;
            } else {
                $jorongIds[$jorong['kode_jorong']] = DB::table('jorong')->insertGetId(array_merge($jorong, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        // Shorthand jorong id
        $jrgPincuranTujuah  = $jorongIds['JRG-001'];
        $jrgKapaloKoto      = $jorongIds['JRG-002'];
        $jrgLubuakAnyia     = $jorongIds['JRG-003'];
        $jrgBandaTangah     = $jorongIds['JRG-004'];
        $jrgLubuakKandang   = $jorongIds['JRG-005'];

        // =========================================================
        //  PETUGAS
        //  Index: 0=Febrinur, 1=Yuli, 2=Novia, 3=Hardi, 4=Hendri, 5=Rinaldi, 6=Dian
        // =========================================================

        $petugasDetails = [
            [
                'nip'           => 'NIP-001',
                'nik'           => '1306031303640001',
                'nama_petugas'  => 'Febrinur',
                'jabatan'       => 'Kepala Unit',
                'no_hp'         => '081200000001',
                'alamat'        => 'Pasar Jumat, Jorong Pincuran Tujuah, Desa Bayua',
                'tanggal_lahir' => '1964-03-13',
                'tmt'           => '2024-10-26',
            ],
            [
                'nip'           => 'NIP-002',
                'nik'           => '1306036707780002',
                'nama_petugas'  => 'Yuli Indrawati',
                'jabatan'       => 'Staf Administrasi',
                'no_hp'         => '081200000002',
                'alamat'        => 'Togok Sianok, Jorong Pincuran Tujuah, Desa Bayua',
                'tanggal_lahir' => '1978-07-27',
                'tmt'           => '2024-10-26',
            ],
            [
                'nip'           => 'NIP-003',
                'nik'           => '1306035611810001',
                'nama_petugas'  => 'Novia Riani',
                'jabatan'       => 'Staf Keuangan',
                'no_hp'         => '081200000003',
                'alamat'        => 'Koto Tingga, Jorong Pincuran Tujuah, Desa Bayua',
                'tanggal_lahir' => '1981-11-16',
                'tmt'           => '2024-10-26',
            ],
            [
                'nip'           => 'NIP-004',
                'nik'           => '1306030606760004',
                'nama_petugas'  => 'Hardi Pranata',
                'jabatan'       => 'Staf Teknis',
                'no_hp'         => '081200000004',
                'alamat'        => 'Koto Tingga, Jorong Pincuran Tujuah, Desa Bayua',
                'tanggal_lahir' => '1976-06-06',
                'tmt'           => '2024-10-26',
            ],
            [
                'nip'           => 'NIP-005',
                'nik'           => '1306031112710001',
                'nama_petugas'  => 'Hendri Awarman',
                'jabatan'       => 'Staf Perencanaan',
                'no_hp'         => '081200000005',
                'alamat'        => 'Durian Diaua, Jorong Pincuran Tujuah, Desa Bayua',
                'tanggal_lahir' => '1971-12-11',
                'tmt'           => '2024-10-26',
            ],
            [
                'nip'           => 'NIP-006',
                'nik'           => '1306030807590003',
                'nama_petugas'  => 'Rinaldi',
                'jabatan'       => 'Staf Pelaksanaan',
                'no_hp'         => '081200000006',
                'alamat'        => 'Koto Tingga, Jorong Lubuak Kandang, Desa Bayua',
                'tanggal_lahir' => '1959-07-08',
                'tmt'           => '2024-10-26',
            ],
            [
                'nip'           => 'NIP-007',
                'nik'           => '1306036811840002',
                'nama_petugas'  => 'Dian Wahyuni',
                'jabatan'       => 'Staf Pengawasan',
                'no_hp'         => '081200000007',
                'alamat'        => 'Koto Tingga, Jorong Pincuran Tujuah, Desa Bayua',
                'tanggal_lahir' => '1984-11-28',
                'tmt'           => '2024-10-26',
            ],
        ];

        $petugasObjs = [];
        foreach ($petugasDetails as $i => $pd) {
            $petugasObjs[] = Petugas::updateOrCreate(
                ['user_id' => $petugasUserObjs[$i]->id],
                array_merge($pd, [
                    'status'            => 'aktif',
                    'status_registrasi' => 'approved',
                    'approved_at'       => now(),
                    'approved_by'       => $admin->id,
                ])
            );
        }

        // =========================================================
        //  ASSIGN PETUGAS → JORONG
        //  Kepala Unit & staf yg berdomisili di Pincuran Tujuah → JRG-001
        //  Rinaldi → Lubuak Kandang (JRG-005)
        // =========================================================

        $assignData = [
            ['petugas_id' => $petugasObjs[0]->id, 'jorong_id' => $jrgPincuranTujuah],  // Febrinur
            ['petugas_id' => $petugasObjs[1]->id, 'jorong_id' => $jrgPincuranTujuah],  // Yuli
            ['petugas_id' => $petugasObjs[2]->id, 'jorong_id' => $jrgPincuranTujuah],  // Novia
            ['petugas_id' => $petugasObjs[3]->id, 'jorong_id' => $jrgPincuranTujuah],  // Hardi
            ['petugas_id' => $petugasObjs[4]->id, 'jorong_id' => $jrgPincuranTujuah],  // Hendri
            ['petugas_id' => $petugasObjs[5]->id, 'jorong_id' => $jrgLubuakKandang],   // Rinaldi
            ['petugas_id' => $petugasObjs[6]->id, 'jorong_id' => $jrgPincuranTujuah],  // Dian
        ];

        foreach ($assignData as $assign) {
            $exists = DB::table('assign_petugas')
                ->where('petugas_id', $assign['petugas_id'])
                ->where('jorong_id', $assign['jorong_id'])
                ->exists();
            if (!$exists) {
                DB::table('assign_petugas')->insert(array_merge($assign, [
                    'periode'     => 'permanen',
                    'aktif'       => true,
                    'catatan'     => null,
                    'dibuat_oleh' => $admin->id,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]));
            }
        }

        // =========================================================
        //  PELANGGAN
        //  Format: [user_idx, nomor, nama, alamat, rt_rw, desa, kecamatan, kabupaten, provinsi,
        //           no_hp, no_ktp, meteran_awal, nomor_meteran, lat, lng, jorong_id, petugas_id,
        //           tanggal_daftar]
        // =========================================================

        $pelangganDummy = [
            // 0 - Gusmayanti
            [0, 'PLG-0001', 'Gusmayanti',      'Koto Tinga',          'RT 01/RW 01', 'Bayua', 'Tanjung Raya', 'Agam', 'Sumatera Barat', '081267729617', '24414319', 0,   'MTR-001', -0.3500, 100.1800, $jrgPincuranTujuah, $petugasObjs[0]->id, '2023-08-17'],
            // 1 - Reno Sari
            [1, 'PLG-0002', 'Reno Sari',       'Durian Diaua',        'RT 01/RW 02', 'Bayua', 'Tanjung Raya', 'Agam', 'Sumatera Barat', '082283171731', '24413484', 0,   'MTR-002', -0.3510, 100.1810, $jrgPincuranTujuah, $petugasObjs[0]->id, '2023-08-21'],
            // 2 - Yarti
            [2, 'PLG-0003', 'Yarti',           'Kapalo Koto',         'RT 02/RW 01', 'Bayua', 'Tanjung Raya', 'Agam', 'Sumatera Barat', '085364286680', '23118723', 0,   'MTR-003', -0.3520, 100.1820, $jrgKapaloKoto,    $petugasObjs[3]->id, '2023-11-30'],
            // 3 - Andra Wijaya
            [3, 'PLG-0004', 'Andra Wijaya',    'Kapalo Koto',         'RT 02/RW 02', 'Bayua', 'Tanjung Raya', 'Agam', 'Sumatera Barat', '085263532926', '',         0,   'MTR-004', -0.3530, 100.1830, $jrgKapaloKoto,    $petugasObjs[3]->id, '2024-11-03'],
            // 4 - Darnawati
            [4, 'PLG-0005', 'Darnawati',       'Kampung Baru',        'RT 03/RW 01', 'Bayua', 'Tanjung Raya', 'Agam', 'Sumatera Barat', '081374508143', '23118725', 0,   'MTR-005', -0.3540, 100.1840, $jrgPincuranTujuah, $petugasObjs[0]->id, '2024-12-04'],
            // 5 - Yusnimar
            [5, 'PLG-0006', 'Yusnimar',        'Kaumpang',            'RT 03/RW 02', 'Bayua', 'Tanjung Raya', 'Agam', 'Sumatera Barat', '085274761209', '23400160', 0,   'MTR-006', -0.3550, 100.1850, $jrgLubuakAnyia,   $petugasObjs[2]->id, '2024-06-24'],
            // 6 - Silvi
            [6, 'PLG-0007', 'Silvi',           'Koto Tingga',         'RT 04/RW 01', 'Bayua', 'Tanjung Raya', 'Agam', 'Sumatera Barat', '081275053392', '22286943', 0,   'MTR-007', -0.3560, 100.1860, $jrgBandaTangah,   $petugasObjs[1]->id, '2004-10-04'],
            // 7 - Ayu Ardila
            [7, 'PLG-0008', 'Ayu Ardila',      'Pasa Pagi',           'RT 04/RW 02', 'Bayua', 'Tanjung Raya', 'Agam', 'Sumatera Barat', '088279313358', '23118726', 0,   'MTR-008', -0.3570, 100.1870, $jrgPincuranTujuah, $petugasObjs[0]->id, '2023-11-30'],
            // 8 - Irfan
            [8, 'PLG-0009', 'Irfan',           'Banda Tangah',        'RT 05/RW 01', 'Bayua', 'Tanjung Raya', 'Agam', 'Sumatera Barat', '081261011529', '25116488', 0,   'MTR-009', -0.3580, 100.1880, $jrgBandaTangah,   $petugasObjs[1]->id, '2026-01-07'],
            // 9 - Gusti Edrianto
            [9, 'PLG-0010', 'Gusti Edrianto',  'Labuah Tunggang',     'RT 05/RW 02', 'Bayua', 'Tanjung Raya', 'Agam', 'Sumatera Barat', '085263135505', '25116229', 0,   'MTR-010', -0.3590, 100.1890, $jrgKapaloKoto,    $petugasObjs[3]->id, '2026-01-22'],
            // 10 - Mansur
            [10,'PLG-0011', 'Mansur',           'Limau Abuang',        'RT 06/RW 01', 'Bayua', 'Tanjung Raya', 'Agam', 'Sumatera Barat', '08235833936',  '24495058', 0,   'MTR-011', -0.3600, 100.1900, $jrgPincuranTujuah, $petugasObjs[0]->id, '2026-02-12'],
        ];

        $pelangganObjs = [];
        foreach ($pelangganDummy as $d) {
            [$userIdx, $nomor, $nama, $alamat, $rtRw, $desa, $kecamatan, $kabupaten, $provinsi,
             $noHp, $noKtp, $meteranAwal, $nomorMeteran, $lat, $lng, $jorongId, $petugasId, $tglDaftar] = $d;

            $pelangganObjs[] = Pelanggan::updateOrCreate(
                ['user_id' => $pelangganUserObjs[$userIdx]->id],
                [
                    'nomor_pelanggan'           => $nomor,
                    'nama_pelanggan'            => $nama,
                    'alamat'                    => $alamat,
                    'provinsi'                  => $provinsi,
                    'rt_rw'                     => $rtRw,
                    'desa'                      => $desa,
                    'kecamatan'                 => $kecamatan,
                    'kabupaten'                 => $kabupaten,
                    'no_hp'                     => $noHp,
                    'no_ktp'                    => $noKtp ?: null,
                    'meteran_awal'              => $meteranAwal,
                    'nomor_meteran'             => $nomorMeteran,
                    'nomor_pelanggan_external'  => null,
                    'latitude'                  => $lat,
                    'longitude'                 => $lng,
                    'jorong_id'                 => $jorongId,
                    'petugas_id'                => $petugasId,
                    'status'                    => 'aktif',
                    'status_registrasi'         => 'approved',
                    'tanggal_daftar'            => $tglDaftar,
                    'approved_at'               => now(),
                    'approved_by'               => $admin->id,
                ]
            );
        }

        // =========================================================
        //  METERAN AIR (4 bulan terakhir untuk setiap pelanggan)
        // =========================================================

        $bulanList = [
            ['bulan' => 2, 'tahun' => 2026, 'tgl' => '2026-02-05'],
            ['bulan' => 3, 'tahun' => 2026, 'tgl' => '2026-03-05'],
            ['bulan' => 4, 'tahun' => 2026, 'tgl' => '2026-04-05'],
            ['bulan' => 5, 'tahun' => 2026, 'tgl' => '2026-05-05'],
        ];

        // Petugas per pelanggan (index sesuai $pelangganDummy)
        $petugasMeteranArr = [
            $petugasObjs[0]->id, // Gusmayanti     → Febrinur
            $petugasObjs[0]->id, // Reno Sari       → Febrinur
            $petugasObjs[3]->id, // Yarti           → Hardi
            $petugasObjs[3]->id, // Andra Wijaya    → Hardi
            $petugasObjs[0]->id, // Darnawati       → Febrinur
            $petugasObjs[2]->id, // Yusnimar        → Novia
            $petugasObjs[1]->id, // Silvi           → Yuli
            $petugasObjs[0]->id, // Ayu Ardila      → Febrinur
            $petugasObjs[1]->id, // Irfan           → Yuli
            $petugasObjs[3]->id, // Gusti Edrianto  → Hardi
            $petugasObjs[0]->id, // Mansur          → Febrinur
        ];

        $meteranIds = [];

        foreach ($pelangganObjs as $pi => $pl) {
            $angkaAwal = 0; // semua mulai dari 0
            foreach ($bulanList as $bi => $b) {
                $pemakaian  = rand(8, 25);
                $angkaAkhir = $angkaAwal + $pemakaian;

                $existing = DB::table('meteran_air')
                    ->where('pelanggan_id', $pl->id)
                    ->where('bulan', $b['bulan'])
                    ->where('tahun', $b['tahun'])
                    ->first();

                if ($existing) {
                    DB::table('meteran_air')->where('id', $existing->id)->update([
                        'angka_awal'  => $angkaAwal,
                        'angka_akhir' => $angkaAkhir,
                        'pemakaian'   => $pemakaian,
                        'updated_at'  => now(),
                    ]);
                    $meteranIds[$pi][$bi] = $existing->id;
                } else {
                    $meteranIds[$pi][$bi] = DB::table('meteran_air')->insertGetId([
                        'pelanggan_id' => $pl->id,
                        'petugas_id'   => $petugasMeteranArr[$pi],
                        'bulan'        => $b['bulan'],
                        'tahun'        => $b['tahun'],
                        'angka_awal'   => $angkaAwal,
                        'angka_akhir'  => $angkaAkhir,
                        'pemakaian'    => $pemakaian,
                        'tanggal_baca' => $b['tgl'],
                        'keterangan'   => null,
                        'foto_meter'   => null,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }

                $angkaAwal = $angkaAkhir;
            }
        }

        // =========================================================
        //  TAGIHAN AIR
        //  Harga: Rp 2.000/m³ (minimal Rp 5.000), denda 10% jika terlambat
        // =========================================================

        $tagihanStatus = [
            // bulan_idx => status per pelanggan [0..10]
            0 => ['lunas','lunas','lunas','lunas','lunas','lunas','lunas','lunas','lunas','lunas','lunas'],           // Feb
            1 => ['lunas','lunas','lunas','lunas','lunas','terlambat','lunas','lunas','lunas','lunas','lunas'],        // Mar
            2 => ['lunas','lunas','lunas','belum_bayar','lunas','belum_bayar','lunas','terlambat','belum_bayar','lunas','lunas'], // Apr
            3 => array_fill(0, 11, 'belum_bayar'),                                                                    // Mei
        ];

        $tagihanIds   = [];
        $tagihanTotal = [];

        foreach ($pelangganObjs as $pi => $pl) {
            foreach ($bulanList as $bi => $b) {
                $meteranId    = $meteranIds[$pi][$bi];
                $pemakaian    = DB::table('meteran_air')->where('id', $meteranId)->value('pemakaian');
                $totalTagihan = max(5000, $pemakaian * 2000);
                $status       = $tagihanStatus[$bi][$pi];
                $denda        = ($status === 'terlambat') ? round($totalTagihan * 0.1) : 0;
                $totalBayar   = $totalTagihan + $denda;
                $nomorTagihan = sprintf('TGH-%02d%04d-%04d', $b['bulan'], $b['tahun'], ($pi * 4 + $bi + 1));

                $existing = DB::table('tagihan_air')
                    ->where('pelanggan_id', $pl->id)
                    ->where('bulan', $b['bulan'])
                    ->where('tahun', $b['tahun'])
                    ->first();

                if ($existing) {
                    DB::table('tagihan_air')->where('id', $existing->id)->update([
                        'status'      => $status,
                        'denda'       => $denda,
                        'total_bayar' => $totalBayar,
                        'updated_at'  => now(),
                    ]);
                    $tagihanIds[$pi][$bi]   = $existing->id;
                    $tagihanTotal[$pi][$bi] = $totalBayar;
                } else {
                    $tagihanIds[$pi][$bi]   = DB::table('tagihan_air')->insertGetId([
                        'pelanggan_id'        => $pl->id,
                        'meteran_id'          => $meteranId,
                        'nomor_tagihan'       => $nomorTagihan,
                        'bulan'               => $b['bulan'],
                        'tahun'               => $b['tahun'],
                        'pemakaian'           => $pemakaian,
                        'total_tagihan'       => $totalTagihan,
                        'denda'               => $denda,
                        'total_bayar'         => $totalBayar,
                        'tanggal_denda'       => ($denda > 0) ? now()->subDays(5)->toDateString() : null,
                        'tanggal_tagihan'     => date('Y-m-d', strtotime("{$b['tahun']}-{$b['bulan']}-07")),
                        'tanggal_jatuh_tempo' => date('Y-m-d', strtotime("{$b['tahun']}-{$b['bulan']}-28")),
                        'status'              => $status,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ]);
                    $tagihanTotal[$pi][$bi] = $totalBayar;
                }
            }
        }

        // =========================================================
        //  PEMBAYARAN (untuk tagihan lunas & terlambat)
        // =========================================================

        $metodeBayarList = ['tunai','transfer','tunai','transfer','tunai','transfer','tunai','transfer','tunai','transfer','tunai'];

        foreach ($pelangganObjs as $pi => $pl) {
            foreach ($bulanList as $bi => $b) {
                $status = $tagihanStatus[$bi][$pi];
                if (!in_array($status, ['lunas', 'terlambat'])) {
                    continue;
                }

                $nomorPembayaran = sprintf('PAY-%s-%05d', now()->format('Ymd'), ($pi * 4 + $bi + 1));
                $exists = DB::table('pembayaran')->where('nomor_pembayaran', $nomorPembayaran)->exists();
                if ($exists) {
                    continue;
                }

                DB::table('pembayaran')->insert([
                    'tagihan_id'        => $tagihanIds[$pi][$bi],
                    'pelanggan_id'      => $pl->id,
                    'nomor_pembayaran'  => $nomorPembayaran,
                    'jumlah_bayar'      => $tagihanTotal[$pi][$bi],
                    'tanggal_bayar'     => date('Y-m-d', strtotime("{$b['tahun']}-{$b['bulan']}-15")),
                    'metode_bayar'      => $metodeBayarList[$pi],
                    'bukti_bayar'       => null,
                    'status'            => 'konfirmasi',
                    'dikonfirmasi_oleh' => $admin->id,
                    'catatan'           => null,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        }

        // =========================================================
        //  PENGADUAN (contoh)
        // =========================================================

        $pengaduanDummy = [
            [
                'pelanggan_id'    => $pelangganObjs[0]->id,
                'nomor'           => 'PGD-20260310-0001',
                'judul'           => 'Meteran Air Bocor',
                'deskripsi'       => 'Meteran air di depan rumah saya bocor sejak 3 hari yang lalu.',
                'jenis'           => 'kerusakan',
                'status'          => 'selesai',
                'prioritas'       => 'tinggi',
                'tanggapan'       => 'Sudah ditangani oleh petugas pada tanggal 12 Maret 2026. Meteran diganti baru.',
                'tanggal_selesai' => '2026-03-12 10:00:00',
            ],
            [
                'pelanggan_id'    => $pelangganObjs[2]->id,
                'nomor'           => 'PGD-20260401-0002',
                'judul'           => 'Tagihan Tidak Sesuai',
                'deskripsi'       => 'Tagihan bulan Maret tidak sesuai dengan pemakaian.',
                'jenis'           => 'tagihan',
                'status'          => 'diproses',
                'prioritas'       => 'sedang',
                'tanggapan'       => 'Sedang dalam proses pengecekan ulang oleh admin.',
                'tanggal_selesai' => null,
            ],
            [
                'pelanggan_id'    => $pelangganObjs[7]->id,
                'nomor'           => 'PGD-20260502-0003',
                'judul'           => 'Air Tidak Mengalir',
                'deskripsi'       => 'Sejak kemarin malam air tidak mengalir sama sekali ke rumah saya.',
                'jenis'           => 'kerusakan',
                'status'          => 'baru',
                'prioritas'       => 'tinggi',
                'tanggapan'       => null,
                'tanggal_selesai' => null,
            ],
        ];

        foreach ($pengaduanDummy as $pgd) {
            $exists = DB::table('pengaduan')->where('nomor_pengaduan', $pgd['nomor'])->exists();
            if (!$exists) {
                DB::table('pengaduan')->insert([
                    'pelanggan_id'    => $pgd['pelanggan_id'],
                    'nomor_pengaduan' => $pgd['nomor'],
                    'judul'           => $pgd['judul'],
                    'deskripsi'       => $pgd['deskripsi'],
                    'foto'            => null,
                    'jenis'           => $pgd['jenis'],
                    'status'          => $pgd['status'],
                    'prioritas'       => $pgd['prioritas'],
                    'tanggapan'       => $pgd['tanggapan'],
                    'ditangani_oleh'  => in_array($pgd['status'], ['diproses', 'selesai']) ? $admin->id : null,
                    'tanggal_selesai' => $pgd['tanggal_selesai'],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        // =========================================================
        //  NOTIFIKASI
        // =========================================================

        $notifDummy = [
            [$admin->id,                      'Laporan Bulan Mei Siap',       'Laporan pembayaran bulan Mei 2026 sudah dapat diunduh.',                         'info',    '/laporan/mei-2026',  false],
            [$admin->id,                      'Pengaduan Baru Masuk',         'Ada pengaduan baru dari Ayu Ardila mengenai air tidak mengalir.',                'warning', '/pengaduan/3',       false],
            [$petugasUserObjs[0]->id,         'Jadwal Baca Meter Besok',      'Anda memiliki jadwal pembacaan meteran di Jorong Pincuran Tujuah besok.',        'info',    '/meteran/jadwal',    false],
            [$petugasUserObjs[1]->id,         'Data Meteran Berhasil Disimpan','Pencatatan meteran bulan Mei untuk Jorong Banda Tangah selesai.',               'success', '/meteran',           true],
            [$pelangganUserObjs[0]->id,       'Tagihan Bulan Mei Terbit',     'Tagihan air Anda untuk bulan Mei 2026 telah terbit.',                            'warning', '/tagihan',           false],
            [$pelangganUserObjs[3]->id,       'Pembayaran Dikonfirmasi',      'Pembayaran tagihan bulan April 2026 Anda telah dikonfirmasi.',                   'success', '/pembayaran',        true],
            [$pelangganUserObjs[7]->id,       'Pengaduan Diterima',           'Pengaduan Anda mengenai air tidak mengalir sudah diterima dan akan segera ditangani.','info','/pengaduan',       false],
        ];

        foreach ($notifDummy as $n) {
            DB::table('notifikasi')->insert([
                'user_id'      => $n[0],
                'judul'        => $n[1],
                'pesan'        => $n[2],
                'tipe'         => $n[3],
                'url'          => $n[4],
                'sudah_dibaca' => $n[5],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // =========================================================
        //  SETTING APLIKASI
        // =========================================================

        $settings = [
            ['key' => 'nama_aplikasi',      'value' => 'PAMSIMAS',                            'label' => 'Nama Aplikasi',                    'tipe' => 'text',    'grup' => 'umum'],
            ['key' => 'nama_organisasi',    'value' => 'PAMSIMAS Nagari Bayua',               'label' => 'Nama Organisasi',                  'tipe' => 'text',    'grup' => 'umum'],
            ['key' => 'alamat_organisasi',  'value' => 'Desa Bayua, Kec. Tanjung Raya, Agam', 'label' => 'Alamat Organisasi',                'tipe' => 'textarea','grup' => 'umum'],
            ['key' => 'email_organisasi',   'value' => 'info@pamsimas.id',                    'label' => 'Email Organisasi',                 'tipe' => 'email',   'grup' => 'umum'],
            ['key' => 'telp_organisasi',    'value' => '(0752) 123456',                       'label' => 'Telepon Organisasi',               'tipe' => 'text',    'grup' => 'umum'],
            ['key' => 'tarif_per_m3',       'value' => '2000',                                'label' => 'Tarif per m³ (Rp)',                'tipe' => 'number',  'grup' => 'tarif'],
            ['key' => 'tarif_minimum',      'value' => '5000',                                'label' => 'Tagihan Minimum (Rp)',             'tipe' => 'number',  'grup' => 'tarif'],
            ['key' => 'persen_denda',       'value' => '10',                                  'label' => 'Persentase Denda (%)',             'tipe' => 'number',  'grup' => 'tagihan'],
            ['key' => 'tanggal_jatuh_tempo','value' => '28',                                  'label' => 'Tanggal Jatuh Tempo (tgl)',        'tipe' => 'number',  'grup' => 'tagihan'],
            ['key' => 'notif_tagihan_aktif','value' => '1',                                   'label' => 'Notifikasi Tagihan Aktif',         'tipe' => 'boolean', 'grup' => 'notifikasi'],
            ['key' => 'notif_jatuh_tempo',  'value' => '3',                                   'label' => 'Notif Sebelum Jatuh Tempo (hari)', 'tipe' => 'number', 'grup' => 'notifikasi'],
        ];

        foreach ($settings as $s) {
            DB::table('setting_aplikasi')->updateOrInsert(
                ['key' => $s['key']],
                array_merge($s, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // =========================================================
        //  OUTPUT RINGKASAN
        // =========================================================

        $this->command->info('');
        $this->command->info('✅ Seeder berhasil!');
        $this->command->info('');
        $this->command->info('=== AKUN LOGIN ===');
        $this->command->info('--- ADMIN ---');
        $this->command->info('admin@pamsimas.id              / password  (Administrator)');
        $this->command->info('');
        $this->command->info('--- PETUGAS ---');
        $this->command->info('febrinur@pamsimas.id           / password  (Febrinur – Kepala Unit)');
        $this->command->info('yuli.indrawati@pamsimas.id     / password  (Yuli Indrawati – Staf Administrasi)');
        $this->command->info('novia.riani@pamsimas.id        / password  (Novia Riani – Staf Keuangan)');
        $this->command->info('hardi.pranata@pamsimas.id      / password  (Hardi Pranata – Staf Teknis)');
        $this->command->info('hendri.awarman@pamsimas.id     / password  (Hendri Awarman – Staf Perencanaan)');
        $this->command->info('rinaldi@pamsimas.id            / password  (Rinaldi – Staf Pelaksanaan)');
        $this->command->info('dian.wahyuni@pamsimas.id       / password  (Dian Wahyuni – Staf Pengawasan)');
        $this->command->info('');
        $this->command->info('--- PELANGGAN ---');
        $this->command->info('gusmayanti@pamsimas.id         / password  (Gusmayanti   – PLG-0001)');
        $this->command->info('reno.sari@pamsimas.id          / password  (Reno Sari    – PLG-0002)');
        $this->command->info('yarti@pamsimas.id              / password  (Yarti        – PLG-0003)');
        $this->command->info('andra.wijaya@pamsimas.id       / password  (Andra Wijaya – PLG-0004)');
        $this->command->info('darnawati@pamsimas.id          / password  (Darnawati    – PLG-0005)');
        $this->command->info('yusnimar@pamsimas.id           / password  (Yusnimar     – PLG-0006)');
        $this->command->info('silvi@pamsimas.id              / password  (Silvi        – PLG-0007)');
        $this->command->info('ayu.ardila@pamsimas.id         / password  (Ayu Ardila   – PLG-0008)');
        $this->command->info('irfan@pamsimas.id              / password  (Irfan        – PLG-0009)');
        $this->command->info('gusti.edrianto@pamsimas.id     / password  (Gusti Edrianto – PLG-0010)');
        $this->command->info('mansur@pamsimas.id             / password  (Mansur       – PLG-0011)');
        $this->command->info('');
        $this->command->info('=== DATA DUMMY ===');
        $this->command->info('Jorong     : 5 (Pincuran Tujuah, Kapalo Koto, Lubuak Anyia, Banda Tangah, Lubuak Kandang)');
        $this->command->info('Petugas    : 7 (Kepala Unit + 6 Staf)');
        $this->command->info('Pelanggan  : 11 (PLG-0001 s/d PLG-0011)');
        $this->command->info('Meteran    : 4 periode (Feb–Mei 2026) × 11 pelanggan = 44 data');
        $this->command->info('Tagihan    : 44 tagihan (lunas, terlambat, belum_bayar)');
        $this->command->info('Pembayaran : sesuai tagihan lunas & terlambat');
        $this->command->info('Pengaduan  : 3 pengaduan');
        $this->command->info('Notifikasi : 7 notifikasi');
        $this->command->info('Setting    : 11 konfigurasi aplikasi');
        $this->command->info('');
    }
}