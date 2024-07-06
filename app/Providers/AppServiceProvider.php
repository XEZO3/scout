<?php

namespace App\Providers;

use App\Services\GeneralInterfaces\AuthServiceInterface;
use App\Services\LeadersService\LeaderAuthService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class,LeaderAuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
