<?php

namespace App\Http\Requests\EquipmentCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentCategoryRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
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
            'nom' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de la catégorie est obligatoire.',
            'nom.string' => 'Le nom de la catégorie doit être une chaîne de caractères.',
            'nom.max' => 'Le nom de la catégorie ne doit pas dépasser 255 caractères.',

            'description.string' => 'La description doit être une chaîne de caractères.',
        ];
    }

    /**
     * Nom lisible des attributs.
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom',
            'description' => 'description',
        ];
    }
}