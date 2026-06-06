<?php

namespace App\Providers;

use App\Services\TagihanService;
use App\Services\DendaService;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TagihanService::class, fn() => new TagihanService());
        $this->app->singleton(DendaService::class, fn() => new DendaService());
    }

    public function boot(): void
    {
        // if (app()->environment('local')) {
        //     URL::forceScheme('https');
        // }

        Paginator::defaultView('pagination::tailwind');
        Paginator::defaultSimpleView('pagination::simple-tailwind');
        Carbon::setLocale('id');

        // Inisialisasi Midtrans global config
        \Midtrans\Config::$serverKey    = config('services.midtrans.server_key', '');
        \Midtrans\Config::$clientKey    = config('services.midtrans.client_key', '');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;
    }
}