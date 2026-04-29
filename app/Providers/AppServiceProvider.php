<?php

namespace App\Providers;

use App\Services\TagihanService;
use App\Services\DendaService;
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
        Paginator::defaultView('pagination::tailwind');
        Paginator::defaultSimpleView('pagination::simple-tailwind');
        Carbon::setLocale('id');
    }
}
