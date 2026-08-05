<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentCategoryResource extends JsonResource
{
    /**
     * Transforme la ressource en tableau.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'equipments_count' => $this->whenCounted('equipments'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}