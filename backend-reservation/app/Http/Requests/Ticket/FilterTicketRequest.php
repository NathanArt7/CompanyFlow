<?php

namespace App\Http\Requests\Ticket;

use App\Enums\Ticket\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterTicketRequest extends FormRequest
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
            'statut' => [
                'nullable',
                Rule::in(TicketStatus::values()),
            ],

            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
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
}
