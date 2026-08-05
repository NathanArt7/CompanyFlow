<?php

namespace App\Http\Requests;

use App\Enums\Reservation\ReservationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

            'date' => [
                'nullable',
                'date'
            ],

            'room_id' => [
                'nullable',
                'integer',
                'exists:rooms,id'
            ],

            'status' => [
                'nullable',
                Rule::in([
                    ReservationStatus::CONFIRMEE->value,
                    ReservationStatus::ANNULEE->value,
                    'EN_COURS',
                    'PASSEE',
                ]),
            ],

            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id'
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
