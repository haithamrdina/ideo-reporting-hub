<?php

namespace App\Providers;

use App\Services\UserFieldsService;
use Illuminate\Support\ServiceProvider;
use Saloon\Laravel\SaloonServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(SaloonServiceProvider::class);
        $this->app->singleton(UserFieldsService::class, function ($app) {
            return new UserFieldsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
