<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Notification;

class ReservationCancelledNotification extends Notification
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

            'title' => 'Réservation annulée',

            'message' => "Votre réservation pour la salle « {$this->reservation->room->nom} » du {$this->reservation->date_reservation->format('d/m/Y')} a été annulée.",

            'reservation_id' => $this->reservation->id,

        ];
    }
}
