<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CronController extends Controller
{
    /**
     * Déclenche l'envoi des rappels de réservation. Route publique (appelée par un
     * service de cron externe, sans compte utilisateur), protégée par un secret
     * partagé transmis dans l'en-tête X-Cron-Secret plutôt que par Sanctum.
     */
    public function sendReservationReminders(Request $request): JsonResponse
    {
        $secret = config('app.cron_secret');

        abort_unless(
            $secret && hash_equals($secret, (string) $request->header('X-Cron-Secret')),
            403
        );

        Artisan::call('reservations:send-reminders');

        return response()->json([
            'message' => 'OK',
            'output' => trim(Artisan::output()),
        ]);
    }
}
