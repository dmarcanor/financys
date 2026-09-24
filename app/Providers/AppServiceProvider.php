<?php

namespace App\Providers;

use Financys\Account\Domain\AccountRepository;
use Financys\Account\Infrastructure\Postgre\AccountPostgreRepository;
use Illuminate\Support\ServiceProvider;
use Shared\Domain\EventBus;
use Shared\Infrastructure\EventBus\LaravelEventBus;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AccountRepository::class, AccountPostgreRepository::class);
        $this->app->bind(EventBus::class, LaravelEventBus::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
