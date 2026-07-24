<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'nom'      => ['required', 'string', 'max:255'],
            'prenom'   => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'role_id'  => ['required', 'exists:roles,id'],
        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [
            'nom.required'      => 'Le nom est obligatoire.',
            'prenom.required'   => 'Le prénom est obligatoire.',
            'email.required'    => "L'adresse email est obligatoire.",
            'email.email'       => "L'adresse email est invalide.",
            'email.unique'      => "Cette adresse email est déjà utilisée.",
            'role_id.required'  => 'Le rôle est obligatoire.',
            'role_id.exists'    => "Le rôle sélectionné n'existe pas.",
        ];
    }
}