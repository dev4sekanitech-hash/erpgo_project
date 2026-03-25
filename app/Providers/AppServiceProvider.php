<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Company owners (type = 'company') have implicit access to all abilities
        // within their workspace. Gate::before runs before Spatie permission checks,
        // so company users pass every can() call in every module controller.
        // Staff, vendor, and client users are NOT affected — they still go through
        // the normal Spatie role/permission checks.
        Gate::before(function ($user, $ability) {
            if ($user->type === 'company') {
                return true;
            }
        });
    }
}
