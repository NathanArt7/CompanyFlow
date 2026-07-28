<?php

namespace App\Http\Requests;

use App\Enums\Reservation\ReservationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class FilterReservationRequest extends FormRequest
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
                'max:255'
            ],

            'from_date' => [
                'nullable',
                'date'
            ],

            'status' => [
                'nullable',
                new Enum(ReservationStatus::class)
            ],

            'per_page' => [
                'nullable',
                'integer',
                'between:1,100'
            ],

            'sort' => [
                'nullable',
                Rule::in([
                    'date_reservation'
                ])
            ],

            'direction' => [
                'nullable',
                Rule::in([
                    'asc',
                    'desc'
                ])
            ],

        ];
    }
}
