<?php

namespace App\Http\Requests;

use App\Enums\Reservation\ReservationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Room;

class StoreReservationRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé.
     */
    public function authorize(): bool
    {
        return true;
    }



    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        return [

            'room_id' => [
                'required',
                'integer',
                'exists:rooms,id',
            ],

            'motif' => [
                'required',
                'string',
                'max:255',
            ],

            'nombre_participants' => [
                'required',
                'integer',
                'min:1',
            ],

            'date_reservation' => [
                'required',
                'date',
            ],

            'heure_debut' => [
                'required',
                'date_format:H:i',
            ],

            'heure_fin' => [
                'required',
                'date_format:H:i',
                'after:heure_debut',
            ],

            'equipments' => [
                'nullable',
                'array',
            ],

            'equipments.*' => [
                'integer',
                'distinct',
                'exists:equipments,id',
            ],

        ];
    }

 

}