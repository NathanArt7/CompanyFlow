<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\ServiceHour;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\Reservation\DayOfWeek;
use App\Enums\Reservation\SlotStatus;
use App\Enums\RoomStatus;
use App\Models\ReservationSetting;

class AvailabilityService
{
    /*
    |--------------------------------------------------------------------------
    | Disponibilités
    |--------------------------------------------------------------------------
    */

 /**
 * Retourne les disponibilités
 * d'une semaine.
 */
public function getWeeklyAvailability(
    Carbon $date,
    User $connectedUser
): array {

    $weekStart =
        $date->copy()->startOfWeek();

    $weekEnd =
        $date->copy()->endOfWeek();

    $rooms = $this->getRooms(
        $connectedUser
    );

    $serviceHours =
        $this->getServiceHours(
            $connectedUser
        );

    $reservationSetting =
        $this->getReservationSetting(
            $connectedUser
        );

    $reservations =
        $this->getWeekReservations(
            $connectedUser,
            $weekStart,
            $weekEnd
        );

    $groupedReservations =
        $this->groupReservationsByRoomAndDate(
            $reservations
        );

    return $this->buildWeek(

        $weekStart,

        $rooms,

        $serviceHours,

        $groupedReservations,

        $reservationSetting
            ->reservation_buffer

    );

}

/**
 * Retourne les paramètres
 * de réservation de l'entreprise.
 */
private function getReservationSetting(
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
 * Retourne les identifiants des matériels
 * déjà réservés sur un créneau.
 */
private function getReservedEquipmentIds(
    User $connectedUser,
    array $data
): array {

    return Reservation::query()

        ->where(
            'entreprise_id',
            $connectedUser->entreprise_id
        )

        ->where(
            'date_reservation',
            $data['date_reservation']
        )

        ->where(function ($query) use ($data) {

            $query

                ->where(
                    'heure_debut',
                    '<',
                    $data['heure_fin']
                )

                ->where(
                    'heure_fin',
                    '>',
                    $data['heure_debut']
                );

        })

        ->with('equipments:id')

        ->get()

        ->flatMap(function (
            Reservation $reservation
        ) {

            return $reservation
                ->equipments
                ->pluck('id');

        })

        ->unique()

        ->values()

        ->all();

}
    
/**
 * Retourne les matériels
 * disponibles pour un créneau.
 */
public function getAvailableEquipments(
    array $data,
    User $connectedUser
): Collection {

    $equipments = Equipment::query()

        ->where(
            'entreprise_id',
            $connectedUser->entreprise_id
        )

        ->orderBy('nom')

        ->get();

    $reservedEquipmentIds =
        $this->getReservedEquipmentIds(
            $connectedUser,
            $data
        );

    return $equipments->map(

        function (
            Equipment $equipment
        ) use (
            $reservedEquipmentIds
        ) {

            return [

                'id' => $equipment->id,

                'nom' => $equipment->nom,

                'statut' => $equipment->etat->value,

                'disponible' => ! in_array(

                    $equipment->id,

                    $reservedEquipmentIds,

                    true

                ),

            ];

        }

    );

}

    /*
    |--------------------------------------------------------------------------
    | Construction
    |--------------------------------------------------------------------------
    */

 /**
 * Construit les disponibilités
 * de la semaine.
 */
private function buildWeek(
    Carbon $weekStart,
    Collection $rooms,
    Collection $serviceHours,
    array $groupedReservations,
    int $buffer
): array {

    $days = [];

    for ($i = 0; $i < 7; $i++) {

        $date = $weekStart
            ->copy()
            ->addDays($i);

        $dayOfWeek =
            DayOfWeek::fromCarbon(
                $date
            );

        $serviceHour =
            $serviceHours->get(
                $dayOfWeek->value
            );

        $roomsAvailability = [];

        foreach ($rooms as $room) {

            $reservations = collect(

                $groupedReservations
                    [$room->id]
                    [$date->toDateString()]

                ?? []

            );

            $roomsAvailability[] =
                $this->buildRoomAvailability(

                    $room,

                    $serviceHour,

                    $reservations,

                    $buffer

                );

        }

        $days[] = [

            'date' => $date->toDateString(),

            'ouvert' => $serviceHour->is_open,

            'salles' => $roomsAvailability,

        ];

    }

    return [

        'jours' => $days,

    ];

}

    
 /**
 * Construit la disponibilité
 * d'une salle.
 */
private function buildRoomAvailability(
    Room $room,
    ServiceHour $serviceHour,
    \Illuminate\Support\Collection $reservations,
    int $buffer
): array {

    if (
        in_array(
            $room->statut,
            [
                RoomStatus::MAINTENANCE,
                RoomStatus::HORS_SERVICE,
            ],
            true
        )
    ) {

        return [

            'id' => $room->id,

            'nom' => $room->nom,

            'statut' => $room->statut->value,

            'creneaux' => [],

        ];

    }

    return [

        'id' => $room->id,

        'nom' => $room->nom,

        'statut' => $room->statut->value,

        'creneaux' => $this->buildSlots(

            $serviceHour,

            $reservations,

            $buffer

        ),

    ];

}

/**
 * Construit les créneaux
 * d'une journée.
 */
private function buildSlots(
    ServiceHour $serviceHour,
    \Illuminate\Support\Collection $reservations,
    int $buffer
): array
{
    $slots = [];

    /*
    |--------------------------------------------------------------------------
    | Journée fermée
    |--------------------------------------------------------------------------
    */

    if (! $serviceHour->is_open) {
        return $slots;
    }

    $cursor = Carbon::parse(
        $serviceHour->start_time
    );

    $closingTime = Carbon::parse(
        $serviceHour->end_time
    );

    /*
    |--------------------------------------------------------------------------
    | Aucune réservation
    |--------------------------------------------------------------------------
    */

    if ($reservations->isEmpty()) {

        $this->addSlot(
            $slots,
            $cursor,
            $closingTime,
            SlotStatus::LIBRE
        );

        return $slots;

    }

    /*
    |--------------------------------------------------------------------------
    | Réservations
    |--------------------------------------------------------------------------
    */

    foreach ($reservations as $reservation) {

        $reservationStart = Carbon::parse(
            $reservation->heure_debut
        );

        $reservationEnd = Carbon::parse(
            $reservation->heure_fin
        );

        $freeEnd = $reservationStart
            ->copy()
            ->subMinutes($buffer);

        /*
        |--------------------------------------------------------------------------
        | Créneau libre avant la réservation
        |--------------------------------------------------------------------------
        */

        if ($cursor->lt($freeEnd)) {

            $this->addSlot(
                $slots,
                $cursor,
                $freeEnd,
                SlotStatus::LIBRE
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Créneau occupé
        |--------------------------------------------------------------------------
        */

        $this->addSlot(
            $slots,
            $reservationStart,
            $reservationEnd,
            SlotStatus::OCCUPE
        );

        /*
        |--------------------------------------------------------------------------
        | Nouveau point de départ
        |--------------------------------------------------------------------------
        */

        $cursor = $reservationEnd
            ->copy()
            ->addMinutes($buffer);

    }

    /*
    |--------------------------------------------------------------------------
    | Dernier créneau libre
    |--------------------------------------------------------------------------
    */

    if ($cursor->lt($closingTime)) {

        $this->addSlot(
            $slots,
            $cursor,
            $closingTime,
            SlotStatus::LIBRE
        );

    }

    return $slots;
}

/**
 * Ajoute un créneau.
 */
private function addSlot(
    array &$slots,
    Carbon $debut,
    Carbon $fin,
    SlotStatus $statut
): void {

    if ($debut->gte($fin)) {
        return;
    }

    $slots[] = [

        'debut' => $debut->format('H:i'),

        'fin' => $fin->format('H:i'),

        'statut' => $statut->value,

    ];

}

    /*
    |--------------------------------------------------------------------------
    | Données
    |--------------------------------------------------------------------------
    */

    /**
 * Retourne les salles
 * de l'entreprise.
 */
private function getRooms(
    User $connectedUser
): Collection {

    return Room::query()

        ->where(
            'entreprise_id',
            $connectedUser->entreprise_id
        )

        ->orderBy('nom')

        ->get();

}

   /**
 * Retourne les réservations
 * de la semaine.
 */
private function getWeekReservations(
    User $connectedUser,
    Carbon $weekStart,
    Carbon $weekEnd
): Collection {

    return Reservation::query()

        ->where(
            'entreprise_id',
            $connectedUser->entreprise_id
        )

        ->whereBetween(

            'date_reservation',

            [

                $weekStart->toDateString(),

                $weekEnd->toDateString()

            ]

        )

        ->orderBy(
            'date_reservation'
        )

        ->orderBy(
            'heure_debut'
        )

        ->get();

}

   /**
 * Retourne les horaires
 * de service de la semaine.
 */
private function getServiceHours(
    User $connectedUser
): Collection {

    return ServiceHour::query()

        ->whereHas(
            'reservationSetting',
            function ($query) use (
                $connectedUser
            ) {

                $query->where(
                    'entreprise_id',
                    $connectedUser->entreprise_id
                );

            }
        )

        ->get()

        ->keyBy(
            'day_of_week'
        );

}

    /*
    |--------------------------------------------------------------------------
    | Outils
    |--------------------------------------------------------------------------
    */

   /**
 * Regroupe les réservations
 * par salle et par date.
 */
private function groupReservationsByRoomAndDate(
    Collection $reservations
): array {

    $grouped = [];

    foreach (
        $reservations
        as $reservation
    ) {

        $grouped

            [$reservation->room_id]

            [$reservation->date_reservation->toDateString()]

        [] = $reservation;

    }

    return $grouped;

}

}