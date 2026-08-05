<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'reservations' => [
                'required',
                'boolean',
            ],

            'rappels' => [
                'required',
                'boolean',
            ],

            'systeme' => [
                'required',
                'boolean',
            ],

            'rapports_hebdomadaires' => [
                'required',
                'boolean',
            ],

        ];
    }
}
