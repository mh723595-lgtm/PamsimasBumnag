<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pakasir Payment Gateway Configuration
    |--------------------------------------------------------------------------
    */

    'merchant_slug' => env('PAKASIR_MERCHANT_SLUG', 'Pamsimas'),

    'api_key' => env('PAKASIR_API_KEY', '7sXxPXkzuiScu7oiovOu7x8Hi20tngSU'),

    'base_url' => env('PAKASIR_BASE_URL', 'https://pakasir.com/api/v1'),

    'callback_url' => env('PAKASIR_CALLBACK_URL', env('APP_URL') . '/api/pakasir/callback'),

    'success_url' => env('PAKASIR_SUCCESS_URL', env('APP_URL') . '/pelanggan/pakasir/sukses'),

    'failed_url' => env('PAKASIR_FAILED_URL', env('APP_URL') . '/pelanggan/pakasir/gagal'),

    'webhook_secret' => env('PAKASIR_WEBHOOK_SECRET', env('PAKASIR_API_KEY', '7sXxPXkzuiScu7oiovOu7x8Hi20tngSU')),

    /*
    | Durasi expired transaksi dalam menit (default 60 menit)
    */
    'expiry_minutes' => env('PAKASIR_EXPIRY_MINUTES', 60),
];