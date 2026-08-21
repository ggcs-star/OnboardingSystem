<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);

        // Safety net: never let debug output (stack traces, SQL, file paths)
        // reach a browser in production, even if APP_DEBUG was left on in
        // the .env file for a deploy by mistake.
        if ($this->app->environment('production')) {
            config(['app.debug' => false]);
        }
    }
}
