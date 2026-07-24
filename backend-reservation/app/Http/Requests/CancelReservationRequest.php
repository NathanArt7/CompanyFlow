<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CancelReservationRequest extends FormRequest
{
   /**
 * Détermine si l'utilisateur est autorisé.
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

        'reason' => [
            'required',
            'string',
            'max:500',
        ],

    ];
}
}
