<?php

use App\Http\Controllers\PakasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Webhook Pakasir dikecualikan dari CSRF secara otomatis karena berada
| di grup 'api' (tidak ada VerifyCsrfToken middleware di api).
|
*/

Route::post('/pakasir/callback', [PakasirController::class, 'webhook'])
    ->middleware('verify.pakasir.webhook');