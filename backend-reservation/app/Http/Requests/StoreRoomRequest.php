<?php

namespace App\Http\Requests;

use App\Enums\RoomStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\RoomType;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'nom' => [
                'required',
                'string',
                'max:255'
            ],

             'type' => [
               'required',
                new Enum(RoomType::class),
            ],

            'code' => [
            'required',
            'string',
            'max:50',
            Rule::unique('rooms')
                ->where(fn ($query) => $query->where(
                'entreprise_id',
                 $this->user()->entreprise_id
            )),
            ],

            'capacite' => [
                Rule::requiredIf(fn () => 
                $this->input('type') === RoomType::MEETING->value),
                'nullable',
                'integer',
                'min:1',
            ],

            'localisation' => [
                'required',
                'string',
                'max:255'
            ],

            'description' => [
                'nullable',
                'string'
            ],

            'statut' => [
                'required',
                new Enum(RoomStatus::class)
            ],

        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (
                $this->input('type') === RoomType::STORAGE->value
                && $this->input('statut') === RoomStatus::OCCUPEE->value
            ) {
                $validator->errors()->add(
                    'statut',
                    'Une salle de stockage ne peut pas avoir le statut "Occupée".'
                );
            }
        });
    }
}