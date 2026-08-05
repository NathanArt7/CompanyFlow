<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification
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

            'title' => 'Nouveau ticket',

            'message' => "Un nouveau ticket a été créé pour le matériel « {$this->ticket->equipment->nom} ».",

            'ticket_id' => $this->ticket->id,

        ];
    }
}
