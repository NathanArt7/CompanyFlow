<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
    'id' => $this->id,

    'nom' => $this->nom,

    'code' => $this->code,

    'marque' => $this->marque,

    'modele' => $this->modele,

    'numero_serie' => $this->numero_serie,

    'usage_type' => $this->usage_type,

    'etat' => $this->etat,

    'category' => new EquipmentCategoryResource(
        $this->whenLoaded('category')
    ),

    'storage_room' => new RoomResource(
        $this->whenLoaded('storageRoom')
    ),

    'assigned_user' => new UserResource(
        $this->whenLoaded('assignedUser')
    ),

    'localisation' => $this->localisation,

    'created_at' => $this->created_at,

    'updated_at' => $this->updated_at,
];
    }
}
