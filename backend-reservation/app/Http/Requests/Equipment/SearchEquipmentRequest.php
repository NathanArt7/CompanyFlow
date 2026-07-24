<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;

class SearchEquipmentRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé
     * à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [

            'search.string' =>
                'Le terme de recherche doit être une chaîne de caractères.',

            'search.max' =>
                'Le terme de recherche ne doit pas dépasser 255 caractères.',

        ];
    }

    /**
     * Nom lisible des attributs.
     */
    public function attributes(): array
    {
        return [

            'search' => 'recherche',

        ];
    }
}