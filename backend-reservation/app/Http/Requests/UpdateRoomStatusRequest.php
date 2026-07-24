<?php

namespace App\Http\Requests;

use App\Enums\RoomStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateRoomStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'statut' => [
                'required',
                new Enum(RoomStatus::class),
            ],

        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            if ($this->input('statut') === RoomStatus::OCCUPEE->value) {

                $validator->errors()->add(
                    'statut',
                    'Le statut "Occupée" est géré automatiquement par le système.'
                );

            }

        });
    }
}