<?php

namespace App\Console\Commands;

use App\Enums\Reservation\ReservationStatus;
use App\Models\Reservation;
use App\Notifications\ReservationReminderNotification;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReservationReminders extends Command
{
    protected $signature = 'reservations:send-reminders';

    protected $description = "Envoie un rappel aux créateurs des réservations confirmées qui débutent dans 20 minutes.";

    public function handle(NotificationService $notificationService): int
    {
        $target = now()->addMinutes(20);
        $windowStart = $target->copy()->subSeconds(30);
        $windowEnd = $target->copy()->addSeconds(30);

        $reservations = Reservation::query()
            ->where('statut', ReservationStatus::CONFIRMEE)
            ->whereNull('reminder_sent_at')
            ->whereDate('date_reservation', now()->toDateString())
            ->with(['user', 'room'])
            ->get()
            ->filter(function (Reservation $reservation) use ($windowStart, $windowEnd) {

                $start = Carbon::parse(
                    $reservation->date_reservation->format('Y-m-d') . ' ' . $reservation->heure_debut
                );

                return $start->between($windowStart, $windowEnd);

            });

        foreach ($reservations as $reservation) {

            if ($reservation->user) {

                $notificationService->notify(
                    $reservation->user,
                    'rappels',
                    new ReservationReminderNotification($reservation)
                );

            }

            $reservation->update([
                'reminder_sent_at' => now(),
            ]);

        }

        $this->info("{$reservations->count()} rappel(s) de réservation envoyé(s).");

        return self::SUCCESS;
    }
}
