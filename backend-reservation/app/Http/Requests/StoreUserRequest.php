<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            // whereNull('deleted_at') : un compte supprimé (soft delete) ne doit pas
            // bloquer indéfiniment la réutilisation de son email pour une nouvelle
            // invitation (ex. lien d'activation expiré puis compte supprimé).
            'email'    => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at')],
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