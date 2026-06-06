<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PelangganController as AdminPelanggan;
use App\Http\Controllers\Admin\PetugasController as AdminPetugas;
use App\Http\Controllers\Admin\TagihanController as AdminTagihan;
use App\Http\Controllers\Admin\PembayaranController as AdminPembayaran;
use App\Http\Controllers\Admin\PengaduanController as AdminPengaduan;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\NotifikasiController as AdminNotifikasi;
use App\Http\Controllers\Admin\RegistrasiController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboard;
use App\Http\Controllers\Petugas\MeteranController;
use App\Http\Controllers\Petugas\RiwayatController as PetugasRiwayat;
use App\Http\Controllers\Petugas\PengaduanController as PetugasPengaduan;
use App\Http\Controllers\Pelanggan\DashboardController as PelangganDashboard;
use App\Http\Controllers\Pelanggan\TagihanController as PelangganTagihan;
use App\Http\Controllers\Pelanggan\RiwayatController as PelangganRiwayat;
use App\Http\Controllers\Pelanggan\PengaduanController as PelangganPengaduan;
use App\Http\Controllers\LandingController;

// ── LANDING ────────────────────────────────────────────────
Route::get('/', [LandingController::class, 'index'])->name('landing');

// ── AUTH ───────────────────────────────────────────────────
Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/daftar',         fn() => redirect('/daftar/pelanggan'))->name('register');
Route::get('/daftar/{role}',  [RegisterController::class, 'showForm'])->name('register.form');
Route::post('/daftar',        [RegisterController::class, 'register'])->name('register.post');

// ── ADMIN ──────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::resource('users',     UserController::class);
    Route::post('pelanggan/bulk-assign-petugas', [AdminPelanggan::class, 'bulkAssignPetugas'])->name('pelanggan.bulk-assign-petugas');
    Route::post('pelanggan/{pelanggan}/assign-petugas', [AdminPelanggan::class, 'assignPetugas'])->name('pelanggan.assign-petugas');
    Route::delete('pelanggan/{pelanggan}/hapus-petugas', [AdminPelanggan::class, 'hapusPetugas'])->name('pelanggan.hapus-petugas');
    Route::resource('pelanggan', AdminPelanggan::class);
    Route::resource('petugas',   AdminPetugas::class)->parameters(['petugas' => 'petugas']);
    Route::resource('tagihan',   AdminTagihan::class)->except(['create', 'store']);
    Route::post('tagihan/{pelanggan}/generate', [AdminTagihan::class, 'generate'])->name('tagihan.generate');
    Route::get('pembayaran/pelanggan/{pelanggan}/tagihan', [AdminPembayaran::class, 'tagihanPelanggan'])->name('pembayaran.pelanggan.tagihan');
    Route::get('/log', [App\Http\Controllers\Admin\AktivitasLogController::class, 'index'])->name('log.index');

    // Pembayaran
    Route::resource('pembayaran', AdminPembayaran::class)->only(['index', 'show', 'update', 'destroy']);
    Route::post('pembayaran/{tagihan}/konfirmasi', [AdminPembayaran::class, 'konfirmasi'])->name('pembayaran.konfirmasi');
    Route::get('pembayaran/{pembayaran}/struk', [AdminPembayaran::class, 'struk'])->name('pembayaran.struk');
    Route::post('pembayaran/tunai',      [AdminPembayaran::class, 'bayarTunai'])->name('pembayaran.tunai');
    Route::post('pembayaran/midtrans',   [AdminPembayaran::class, 'bayarMidtrans'])->name('pembayaran.midtrans');
    Route::post('pembayaran/notifikasi', [AdminPembayaran::class, 'notifikasi'])->name('pembayaran.notifikasi');

    Route::resource('pengaduan', AdminPengaduan::class)->only(['index', 'show', 'update']);
    Route::post('pengaduan/{pengaduan}/tanggapi', [AdminPengaduan::class, 'tanggapi'])->name('pengaduan.tanggapi');

    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/',               [LaporanController::class, 'index'])->name('index');
        Route::get('/tagihan',        [LaporanController::class, 'tagihan'])->name('tagihan');
        Route::get('/pembayaran',     [LaporanController::class, 'pembayaran'])->name('pembayaran');
        Route::get('/pemakaian',      [LaporanController::class, 'pemakaian'])->name('pemakaian');
        Route::get('/tagihan/pdf',    [LaporanController::class, 'tagihanPdf'])->name('tagihan.pdf');
        Route::get('/pembayaran/pdf', [LaporanController::class, 'pembayaranPdf'])->name('pembayaran.pdf');
    });

    Route::get('/pengaturan',                    [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan',                    [PengaturanController::class, 'update'])->name('pengaturan.update');
    Route::post('/pengaturan/proses-denda',      [PengaturanController::class, 'prosesDenda'])->name('pengaturan.proses-denda');

    Route::get('/notifikasi',                    [AdminNotifikasi::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{notifikasi}/baca', [AdminNotifikasi::class, 'baca'])->name('notifikasi.baca');
    Route::post('/notifikasi/baca-semua',        [AdminNotifikasi::class, 'bacaSemua'])->name('notifikasi.baca-semua');
    Route::delete('/notifikasi/hapus-dibaca',    [AdminNotifikasi::class, 'hapusDibaca'])->name('notifikasi.hapus-dibaca');

    Route::get('/registrasi',                      [RegistrasiController::class, 'index'])->name('registrasi.index');
    Route::get('/registrasi/{type}/{id}',          [RegistrasiController::class, 'show'])->name('registrasi.show');
    Route::post('/registrasi/{type}/{id}/approve', [RegistrasiController::class, 'approve'])->name('registrasi.approve');
    Route::post('/registrasi/{type}/{id}/reject',  [RegistrasiController::class, 'reject'])->name('registrasi.reject');

    // Assign Petugas
    Route::get('assign-petugas', [App\Http\Controllers\Admin\AssignPetugasController::class, 'index'])->name('assign-petugas.index');
    Route::post('assign-petugas', [App\Http\Controllers\Admin\AssignPetugasController::class, 'store'])->name('assign-petugas.store');
    Route::get('assign-petugas/petugas/{petugas}', [App\Http\Controllers\Admin\AssignPetugasController::class, 'detailPetugas'])->name('assign-petugas.detail-petugas');
    Route::delete('assign-petugas/{assignPetugas}', [App\Http\Controllers\Admin\AssignPetugasController::class, 'destroy'])->name('assign-petugas.destroy');
    Route::patch('assign-petugas/{assignPetugas}/toggle', [App\Http\Controllers\Admin\AssignPetugasController::class, 'toggleAktif'])->name('assign-petugas.toggle');
    Route::patch('assign-petugas/{assignPetugas}', [App\Http\Controllers\Admin\AssignPetugasController::class, 'update'])->name('assign-petugas.update');

    // Jorong
    Route::get('jorong', [App\Http\Controllers\Admin\JorongController::class, 'index'])->name('jorong.index');
    Route::post('jorong', [App\Http\Controllers\Admin\JorongController::class, 'store'])->name('jorong.store');
    Route::put('jorong/{jorong}', [App\Http\Controllers\Admin\JorongController::class, 'update'])->name('jorong.update');
    Route::delete('jorong/{jorong}', [App\Http\Controllers\Admin\JorongController::class, 'destroy'])->name('jorong.destroy');
    Route::patch('jorong/{jorong}/toggle', [App\Http\Controllers\Admin\JorongController::class, 'toggleAktif'])->name('jorong.toggle');
});


// ── PETUGAS ────────────────────────────────────────────────
    Route::prefix('petugas')->name('petugas.')->middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/dashboard',             [PetugasDashboard::class, 'index'])->name('dashboard');
    Route::get('/meteran',               [MeteranController::class, 'index'])->name('meteran.index');
    Route::get('/meteran/create',        [MeteranController::class, 'create'])->name('meteran.create');
    Route::post('/meteran',              [MeteranController::class, 'store'])->name('meteran.store');
    Route::get('/meteran/{meteranAir}',  [MeteranController::class, 'show'])->name('meteran.show');
    Route::get('/peta', [\App\Http\Controllers\Petugas\PetaController::class, 'index'])->name('peta.index');
    Route::get('/riwayat',               [PetugasRiwayat::class, 'index'])->name('riwayat.index');
    Route::get('/pengaduan',             [PetugasPengaduan::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/{pengaduan}', [PetugasPengaduan::class, 'show'])->name('pengaduan.show');
    Route::post('/pengaduan/{pengaduan}/proses', [PetugasPengaduan::class, 'proses'])->name('pengaduan.proses');
});

// ── PELANGGAN ──────────────────────────────────────────────
    Route::prefix('pelanggan')->name('pelanggan.')->middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/dashboard',              [PelangganDashboard::class, 'index'])->name('dashboard');
    Route::get('/tagihan',                [PelangganTagihan::class, 'index'])->name('tagihan.index');
    Route::get('/tagihan/{tagihan}',      [PelangganTagihan::class, 'show'])->name('tagihan.show');
    Route::get('/riwayat',                [PelangganRiwayat::class, 'index'])->name('riwayat.index');
    Route::get('/pengaduan',              [PelangganPengaduan::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/create',       [PelangganPengaduan::class, 'create'])->name('pengaduan.create');
    Route::post('/pengaduan',             [PelangganPengaduan::class, 'store'])->name('pengaduan.store');
    Route::get('/pengaduan/{pengaduan}',  [PelangganPengaduan::class, 'show'])->name('pengaduan.show');
});