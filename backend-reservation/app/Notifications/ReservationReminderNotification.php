<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Notification;

class ReservationReminderNotification extends Notification
{
    public function __construct(
        private Reservation $reservation
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [

            'title' => 'Réservation dans 20 minutes',

            'message' => "Votre réservation pour la salle « {$this->reservation->room->nom} » commence dans 20 minutes ({$this->reservation->heure_debut}).",

            'reservation_id' => $this->reservation->id,

        ];
    }
}
