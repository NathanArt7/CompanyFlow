<?php

namespace App\Http\Requests\Availability;

use Illuminate\Foundation\Http\FormRequest;

class AvailableEquipmentRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur
     * est autorisé.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        return [

            'date_reservation' => [

                'required',

                'date',

            ],

            'heure_debut' => [

                'required',

                'date_format:H:i',

            ],

            'heure_fin' => [

                'required',

                'date_format:H:i',

                'after:heure_debut',

            ],

        ];
    }
}