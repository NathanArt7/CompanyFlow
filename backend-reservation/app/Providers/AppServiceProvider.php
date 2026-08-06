<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

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

        // Envoi des e-mails via l'API HTTP de Brevo plutôt que par SMTP :
        // certains hébergeurs (dont Render) bloquent ou rendent instable le SMTP
        // sortant, ce qui provoquait des requêtes bloquées ~60s (timeout socket
        // PHP) sur toute action déclenchant un envoi de mail (inscription, etc.).
        // L'API HTTP passe par HTTPS, qui n'est jamais filtré.
        Mail::extend('brevo', function () {

            return (new BrevoTransportFactory())->create(
                new Dsn(
                    'brevo+api',
                    'default',
                    config('services.brevo.key')
                )
            );

        });
    }
}
