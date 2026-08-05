<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ReservationCountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'from' => [
                'required',
                'date',
            ],

            'to' => [
                'required',
                'date',
                'after_or_equal:from',
            ],

        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            if (
                $this->filled('from') &&
                $this->filled('to') &&
                Carbon::parse($this->input('from'))
                    ->diffInDays(Carbon::parse($this->input('to'))) > 62
            ) {

                $validator->errors()->add(
                    'to',
                    'La plage de dates ne peut pas dépasser 62 jours.'
                );

            }

        });
    }
}
