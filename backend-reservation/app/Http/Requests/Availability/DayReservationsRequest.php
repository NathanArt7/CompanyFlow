<?php

namespace App\Http\Requests\Availability;

use Illuminate\Foundation\Http\FormRequest;

class DayReservationsRequest extends FormRequest
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

            'date' => [

                'required',

                'date',

            ],

        ];
    }
}
