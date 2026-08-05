<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        // Limite les tentatives de connexion par couple email+IP (et non par IP seule) :
        // empêche le brute-force sur un compte ciblé tout en évitant qu'un attaquant
        // distribue ses tentatives sur plusieurs IP pour un même email sans être ralenti.
        RateLimiter::for('login', function (Request $request) {

            $key = Str::lower((string) $request->input('email')) . '|' . $request->ip();

            return Limit::perMinute(5)->by($key);

        });
    }
}
