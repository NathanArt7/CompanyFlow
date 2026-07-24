<?php

namespace App\Services;

use App\Models\ReservationSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class ReservationSettingService {
    /**
 * Retourne la configuration des réservations.
 */
public function getReservationSettings(
    User $connectedUser
): ReservationSetting {

    return $this
        ->reservationSetting(
            $connectedUser
        )
        ->load(
            'serviceHours'
        );

}

/**
 * Met à jour la configuration des réservations.
 */
public function updateReservationSettings(
    array $data,
    User $connectedUser
): ReservationSetting {

    $reservationSetting =
        $this->reservationSetting(
            $connectedUser
        );

    return DB::transaction(function () use (
        $reservationSetting,
        $data
    ) {

        $this->updateReservationBuffer(
            $reservationSetting,
            $data['reservation_buffer']
        );

        $this->updateServiceHours(
            $reservationSetting,
            $data['service_hours']
        );

        return $reservationSetting
            ->fresh()
            ->load('serviceHours');

    });

}

/**
 * Retourne la configuration
 * de l'entreprise.
 */
private function reservationSetting(
    User $connectedUser
): ReservationSetting {

    return ReservationSetting::query()

        ->where(
            'entreprise_id',
            $connectedUser->entreprise_id
        )

        ->firstOrFail();

}

/**
 * Met à jour le délai entre
 * deux réservations.
 */
private function updateReservationBuffer(
    ReservationSetting $reservationSetting,
    int $reservationBuffer
): void {

    $reservationSetting->update([

        'reservation_buffer' =>
            $reservationBuffer,

    ]);

}

/**
 * Met à jour les horaires
 * de service.
 */
private function updateServiceHours(
    ReservationSetting $reservationSetting,
    array $serviceHours
): void {

    foreach (
        $serviceHours
        as $serviceHour
    ) {

        $reservationSetting
            ->serviceHours()

            ->where(
                'day_of_week',
                $serviceHour['day_of_week']
            )

            ->update([

                'is_open' =>
                    $serviceHour['is_open'],

                'start_time' =>
                    $serviceHour['start_time'],

                'end_time' =>
                    $serviceHour['end_time'],

            ]);

    }

}
}