<?php

namespace App\Http\Controllers;

/**
 * PembayaranController
 *
 * File ini sebelumnya menangani webhook Midtrans.
 * Midtrans telah dihapus total dan diganti dengan Pakasir.
 *
 * Webhook Pakasir sekarang ditangani langsung oleh:
 *   App\Http\Controllers\PakasirController::webhook()
 *
 * Route webhook:
 *   POST /api/pakasir/callback  → PakasirController@webhook
 *
 * File ini dipertahankan agar tidak ada error jika ada referensi lain,
 * tetapi tidak mengandung logika apapun.
 */
class PembayaranController extends Controller
{
    // Kosong — semua logika pembayaran digital ada di PakasirController.
}