<?php

namespace App\Providers;

use App\Services\AuditService;
use App\Services\BorrowingService;
use App\Services\StockService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuditService::class);
        $this->app->singleton(StockService::class);
        $this->app->singleton(BorrowingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
