<?php

namespace App\Providers;

use Financys\Account\Domain\AccountRepository;
use Financys\Account\Infrastructure\Postgre\AccountPostgreRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AccountRepository::class, AccountPostgreRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
