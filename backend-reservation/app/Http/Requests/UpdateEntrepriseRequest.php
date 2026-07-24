<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEntrepriseRequest extends FormRequest
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

            'nom' => [
                'required',
                'string',
                'max:255',
            ],

            'telephone' => [
                'required',
                'string',
                'max:30',
            ],

            'adresse' => [
                'required',
                'string',
                'max:255',
            ],

            'site_web' => [
                'nullable',
                'url',
                'max:255',
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,svg,webp',
                'max:2048',
            ],

        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [

            'nom.required' => 'Le nom de l’entreprise est obligatoire.',

            'telephone.required' => 'Le numéro de téléphone est obligatoire.',

            'adresse.required' => 'L’adresse est obligatoire.',

            'site_web.url' => 'Le site web doit être une URL valide.',

            'logo.image' => 'Le logo doit être une image.',

            'logo.mimes' => 'Le logo doit être au format jpg, jpeg, png, svg ou webp.',

            'logo.max' => 'Le logo ne doit pas dépasser 2 Mo.',

        ];
    }
}