<?php

namespace App\Http\Requests;

use App\Enums\Reservation\ReservationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Room;
use Carbon\Carbon;

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
                'after_or_equal:today',
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

    /**
     * Ajoute les règles métier après la validation classique.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $date = $this->input('date_reservation');
            $heureDebut = $this->input('heure_debut');

            if (
                $date === Carbon::now()->toDateString()
                && $heureDebut < Carbon::now()->format('H:i')
            ) {
                $validator->errors()->add(
                    'heure_debut',
                    "Le créneau horaire sélectionné est déjà passé."
                );
            }

        });
    }

}