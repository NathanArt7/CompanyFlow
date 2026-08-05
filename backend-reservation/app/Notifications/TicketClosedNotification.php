<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Notifications\Notification;

class TicketClosedNotification extends Notification
{
    public function __construct(
        private Ticket $ticket,
        private string $equipmentStateLabel
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [

            'title' => 'Ticket fermé',

            'message' => "L'état du matériel « {$this->ticket->equipment->nom} » de votre ticket est passé à « {$this->equipmentStateLabel} ».",

            'ticket_id' => $this->ticket->id,

        ];
    }
}
