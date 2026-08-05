<?php

namespace App\Notifications;

use App\Models\Equipment;
use Illuminate\Notifications\Notification;

class EquipmentStateChangedNotification extends Notification
{
    public function __construct(
        private Equipment $equipment,
        private string $previousStateLabel,
        private string $newStateLabel
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [

            'title' => 'Statut du matériel modifié',

            'message' => "Le statut du matériel « {$this->equipment->nom} » est passé de « {$this->previousStateLabel} » à « {$this->newStateLabel} ».",

            'equipment_id' => $this->equipment->id,

        ];
    }
}
