<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

            'role_id' => [
                'nullable',
                'exists:roles,id',
            ],

            'actif' => [
                'nullable',
                'boolean',
            ],

            'sort' => [
                'nullable',
                Rule::in([
                    'nom',
                    'prenom',
                    'email',
                    'created_at',
                ]),
            ],

            'direction' => [
                'nullable',
                Rule::in([
                    'asc',
                    'desc',
                ]),
            ],

            'per_page' => [
                'nullable',
                'integer',
                'between:1,100',
            ],

        ];
    }
}