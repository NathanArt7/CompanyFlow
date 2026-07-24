<?php

namespace App\Http\Requests;

use App\Enums\RoomStatus;
use App\Enums\RoomType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class FilterRoomRequest extends FormRequest
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

            'type' => [
                'nullable',
                new Enum(RoomType::class)
            ],

            'statut' => [
                'nullable',
                new Enum(RoomStatus::class)
            ],

            'per_page' => [
                'nullable',
                'integer',
                'between:1,100'
            ],

            'sort' => [
                'nullable',
                Rule::in([
                    'nom',
                    'code',
                    'capacite',
                    'localisation',
                    'type',
                    'statut',
                    'created_at'
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