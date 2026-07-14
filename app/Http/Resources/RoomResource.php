<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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

            'type' => [
                'value' => $this->type->value,
                'label' => $this->type->label(),
            ],

            'capacite' => $this->capacite,

            'localisation' => $this->localisation,

            'description' => $this->description,

            'statut' => [
                'value' => $this->statut->value,
                'label' => $this->statut->label(),
            ],

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}