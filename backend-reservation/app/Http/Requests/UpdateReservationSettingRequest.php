<?php

namespace App\Http\Requests;

use App\Enums\Reservation\DayOfWeek;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReservationSettingRequest extends FormRequest
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

            'reservation_buffer' => [
                'required',
                'integer',
                'min:0',
            ],

            'service_hours' => [
                'required',
                'array',
                'size:7',
            ],

            'service_hours.*.day_of_week' => [
                'required',
                Rule::in(
                    DayOfWeek::values()
                ),
            ],

            'service_hours.*.is_open' => [
                'required',
                'boolean',
            ],

            'service_hours.*.start_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'service_hours.*.end_time' => [
                'nullable',
                'date_format:H:i',
            ],

        ];
    }

    /**
 * Ajoute les validations métier.
 */
public function withValidator(
    $validator
): void {

    $validator->after(function ($validator) {

        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $this->validateBusinessRules(
            $validator
        );

    });

}

/**
 * Vérifie toutes les règles métier.
 */
private function validateBusinessRules(
    $validator
): void {

    $this->ensureUniqueDays(
        $validator
    );

    $this->ensureOpeningHoursAreValid(
        $validator
    );

}

/**
 * Vérifie que chaque jour n'apparaît
 * qu'une seule fois.
 */
private function ensureUniqueDays(
    $validator
): void {

    $days = collect(
        $this->input('service_hours')
    )
    ->pluck('day_of_week');

    if (
        $days->duplicates()->isNotEmpty()
    ) {

        $validator->errors()->add(
            'service_hours',
            "Chaque jour de la semaine ne peut apparaître qu'une seule fois."
        );

    }

}

/**
 * Vérifie la cohérence des horaires
 * d'ouverture.
 */
private function ensureOpeningHoursAreValid(
    $validator
): void {

    foreach (
        $this->input('service_hours')
        as $index => $serviceHour
    ) {

        $isOpen = $serviceHour['is_open'];

        $startTime = $serviceHour['start_time'];

        $endTime = $serviceHour['end_time'];

        /*
        |--------------------------------------------------------------------------
        | Jour fermé
        |--------------------------------------------------------------------------
        */

        if (! $isOpen) {

            if (
                $startTime !== null ||
                $endTime !== null
            ) {

                $validator->errors()->add(
                    "service_hours.$index",
                    "Un jour fermé ne doit pas contenir d'heures d'ouverture."
                );

            }

            continue;

        }

        /*
        |--------------------------------------------------------------------------
        | Jour ouvert
        |--------------------------------------------------------------------------
        */

        if (
            empty($startTime) ||
            empty($endTime)
        ) {

            $validator->errors()->add(
                "service_hours.$index",
                "Les heures d'ouverture et de fermeture sont obligatoires."
            );

            continue;

        }

        if ($startTime >= $endTime) {

            $validator->errors()->add(
                "service_hours.$index",
                "L'heure de fermeture doit être postérieure à l'heure d'ouverture."
            );

        }

    }

}
}