<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipmentStatusRequest extends FormRequest
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
     * L'état valide dépend du type d'utilisation (empruntable/non empruntable)
     * du matériel déjà enregistré : il est donc vérifié côté service, pas ici.
     */
    public function rules(): array
    {
        return [

            'etat' => [
                'required',
                'string',
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
            'etat.required' => "L'état est obligatoire.",
        ];
    }
}
