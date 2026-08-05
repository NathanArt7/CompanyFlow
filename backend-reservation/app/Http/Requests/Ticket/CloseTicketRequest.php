<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CloseTicketRequest extends FormRequest
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
            'equipment_state' => [
                'required',
                Rule::in(['FONCTIONNEL', 'HORS_SERVICE']),
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'equipment_state.required' => "Merci de préciser l'état du matériel après fermeture.",
            'equipment_state.in' => "L'état du matériel doit être « Fonctionnel » ou « Hors service ».",
        ];
    }
}
