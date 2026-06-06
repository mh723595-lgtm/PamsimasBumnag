<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SettingAplikasi;
use App\Models\AktivitasLog;
use App\Services\DendaService;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    private array $defaults = [
        // Umum
        ['key'=>'nama_sistem',    'value'=>'PAMSIMAS',               'label'=>'Nama Sistem',                  'tipe'=>'text',    'grup'=>'umum'],
        ['key'=>'nama_desa',      'value'=>'Desa Sukamaju',          'label'=>'Nama Desa/Kelurahan',          'tipe'=>'text',    'grup'=>'umum'],
        ['key'=>'kecamatan',      'value'=>'Sukajaya',               'label'=>'Kecamatan',                    'tipe'=>'text',    'grup'=>'umum'],
        ['key'=>'kabupaten',      'value'=>'Bogor',                  'label'=>'Kabupaten/Kota',               'tipe'=>'text',    'grup'=>'umum'],
        ['key'=>'alamat_kantor',  'value'=>'Jl. Raya Sukamaju No.1', 'label'=>'Alamat Kantor',                'tipe'=>'textarea','grup'=>'umum'],
        ['key'=>'telepon',        'value'=>'081234567890',           'label'=>'Nomor Telepon',                'tipe'=>'text',    'grup'=>'umum'],
        ['key'=>'email_sistem',   'value'=>'admin@pamsimas.id',      'label'=>'Email Sistem',                 'tipe'=>'text',    'grup'=>'umum'],
        // Tarif
        ['key'=>'tarif_blok1',   'value'=>'20000','label'=>'Tarif Blok 1 (0-10 m³) — Flat Rp',   'tipe'=>'number','grup'=>'tarif'],
        ['key'=>'tarif_blok2',   'value'=>'1500', 'label'=>'Tarif Blok 2 (11-20 m³) — per m³ Rp','tipe'=>'number','grup'=>'tarif'],
        ['key'=>'tarif_blok3',   'value'=>'2000', 'label'=>'Tarif Blok 3 (>20 m³) — per m³ Rp',  'tipe'=>'number','grup'=>'tarif'],
        ['key'=>'biaya_admin',   'value'=>'0',    'label'=>'Biaya Administrasi (flat) Rp — isi 0 jika tidak ada',  'tipe'=>'number','grup'=>'tarif'],
        // Tagihan
        ['key'=>'hari_jatuh_tempo','value'=>'20','label'=>'Tanggal Jatuh Tempo (tgl berapa tiap bulan)','tipe'=>'number','grup'=>'tagihan'],
        ['key'=>'notif_h_minus',  'value'=>'3',  'label'=>'Kirim notifikasi H- hari sebelum jatuh tempo','tipe'=>'number','grup'=>'tagihan'],
        // Denda
        ['key'=>'denda_aktif',            'value'=>'1', 'label'=>'Aktifkan Denda Keterlambatan','tipe'=>'boolean','grup'=>'denda'],
        ['key'=>'denda_persen',           'value'=>'2', 'label'=>'Persentase Denda per Bulan (%)',  'tipe'=>'number','grup'=>'denda'],
        ['key'=>'denda_minimum',          'value'=>'2000','label'=>'Denda Minimum (Rp)',            'tipe'=>'number','grup'=>'denda'],
        ['key'=>'denda_maksimum_persen',  'value'=>'50', 'label'=>'Maksimum Denda (% dari tagihan)','tipe'=>'number','grup'=>'denda'],
        ['key'=>'denda_grace_period',     'value'=>'0',  'label'=>'Grace Period (hari setelah jatuh tempo)','tipe'=>'number','grup'=>'denda'],
        // Notifikasi
        ['key'=>'notif_tagihan_baru',     'value'=>'1','label'=>'Notifikasi tagihan baru ke pelanggan', 'tipe'=>'boolean','grup'=>'notifikasi'],
        ['key'=>'notif_pembayaran_ok',    'value'=>'1','label'=>'Notifikasi pembayaran dikonfirmasi',   'tipe'=>'boolean','grup'=>'notifikasi'],
        ['key'=>'notif_pengaduan_update', 'value'=>'1','label'=>'Notifikasi update status pengaduan',   'tipe'=>'boolean','grup'=>'notifikasi'],
    ];

    public function index()
    {
        // Auto-init: buat setting default jika belum ada (tanpa seeder)
        foreach ($this->defaults as $d) {
            SettingAplikasi::firstOrCreate(['key' => $d['key']], $d);
        }

        $settings = SettingAplikasi::query()->orderBy('grup')->orderBy('id')->get()->groupBy('grup');
        return view('admin.pengaturan.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Validasi field number
        $rules = [];
        $numberKeys = ['tarif_blok1','tarif_blok2','tarif_blok3','biaya_admin','hari_jatuh_tempo','notif_h_minus',
                       'denda_persen','denda_minimum','denda_maksimum_persen','denda_grace_period'];
        $data = $request->except(['_token','_method']);

        foreach ($numberKeys as $k) {
            if (array_key_exists($k, $data)) {
                $rules[$k] = 'required|numeric|min:0';
            }
        }

        if (!empty($rules)) {
            $request->validate($rules);
        }

        // Simpan semua nilai
        foreach ($data as $key => $value) {
            SettingAplikasi::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Handle boolean unchecked
        $boolKeys = ['denda_aktif','notif_tagihan_baru','notif_pembayaran_ok','notif_pengaduan_update'];
        foreach ($boolKeys as $bk) {
            if (!array_key_exists($bk, $data)) {
                SettingAplikasi::where('key', $bk)->update(['value' => '0']);
            }
        }

        // Invalidasi cache setting agar kalkulasi tagihan & denda langsung pakai nilai baru
        SettingAplikasi::flushCache();

        AktivitasLog::catat('update_pengaturan', 'Update pengaturan sistem PAMSIMAS');

        return back()->with('success', '✅ Pengaturan berhasil disimpan.');
    }

    /**
     * Proses denda manual (dipanggil dari halaman pengaturan)
     */
    public function prosesDenda()
    {
        $dendaService = app(DendaService::class);
        $hasil = $dendaService->prosesSemuaDenda();

        return back()->with('success',
            "✅ Denda diproses: {$hasil['diproses']} tagihan, total denda Rp " .
            number_format($hasil['total_denda'], 0, ',', '.'));
    }
}