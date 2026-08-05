<?php

namespace App\Http\Requests\EquipmentCategory;

use Illuminate\Foundation\Http\FormRequest;

class SearchEquipmentCategoryRequest extends FormRequest
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

            'page' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'search.string' => 'Le terme de recherche doit être une chaîne de caractères.',
            'search.max' => 'Le terme de recherche ne doit pas dépasser 255 caractères.',
            'per_page.integer' => 'Le nombre d\'éléments par page doit être un entier.',
            'per_page.max' => 'Le nombre d\'éléments par page ne doit pas dépasser 100.',
        ];
    }
}