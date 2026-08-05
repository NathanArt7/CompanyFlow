<?php

namespace App\Http\Requests;

use App\Enums\ActivityLog\ActivityModule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterActivityLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'module' => [
                'required',
                Rule::in(ActivityModule::values()),
            ],

            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

            'from_date' => [
                'nullable',
                'date',
            ],

            'to_date' => [
                'nullable',
                'date',
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
