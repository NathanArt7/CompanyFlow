<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
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

            'token' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],

        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [

            'token.required' =>
                'Le token est obligatoire.',

            'password.required' =>
                'Le mot de passe est obligatoire.',

            'password.confirmed' =>
                'La confirmation du mot de passe est incorrecte.',

        ];
    }
}