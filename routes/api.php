<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/midtrans/notifikasi', [App\Http\Controllers\PembayaranController::class, 'notifikasi']);