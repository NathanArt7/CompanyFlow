<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'equipment_id' => [
                'required',
                'integer',
                'exists:equipments,id',
            ],

            'description' => [
                'required',
                'string',
                'max:2000',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'equipment_id.required' => 'Merci de sélectionner un matériel.',
            'equipment_id.exists' => "Ce matériel n'existe pas.",
            'description.required' => 'Merci de décrire le problème rencontré.',
            'description.max' => 'La description ne doit pas dépasser 2000 caractères.',
        ];
    }
}
