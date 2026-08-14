<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEntrepriseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation des données.
     */
   public function rules(): array
{
    return [

        'nom' => [
            'required',
            'string',
            'max:255',
        ],

        'prenom' => [
            'required',
            'string',
            'max:255',
        ],

        'email' => [
            'required',
            'email',
            'max:255',
            // whereNull('deleted_at') : cf. StoreUserRequest.
            Rule::unique('users', 'email')->whereNull('deleted_at'),
        ],

    ];
}
}