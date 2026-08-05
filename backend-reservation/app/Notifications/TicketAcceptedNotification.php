<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Notifications\Notification;

class TicketAcceptedNotification extends Notification
{
    public function __construct(
        private Ticket $ticket
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [

            'title' => 'Ticket pris en charge',

            'message' => "Votre ticket pour « {$this->ticket->equipment->nom} » a été pris en charge.",

            'ticket_id' => $this->ticket->id,

        ];
    }
}
