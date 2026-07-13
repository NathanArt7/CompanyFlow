<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    /**
     * Transforme la ressource en tableau.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'nom' => $this->nom,

            'prenom' => $this->prenom,

            'email' => $this->email,

            'photo' => $this->photo,

            'actif' => $this->actif,

            'password_changed' => $this->password_changed,

            'role' => $this->role->nom,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}