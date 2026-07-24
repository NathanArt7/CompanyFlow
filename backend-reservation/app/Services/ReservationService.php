<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\Reservation;
use App\Models\ReservationSetting;
use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\ServiceHour;
use App\Enums\Reservation\DayOfWeek;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\Reservation\ReservationStatus;


class ReservationService
{

/**
 * Salle concernée par la réservation.
 */
private ?Room $room = null;

/**
 * Configuration des réservations
 * de l'entreprise.
 */
private ?ReservationSetting $reservationSetting = null;

/**
 * Horaire de service correspondant
 * à la réservation.
 */
private ?ServiceHour $serviceHour = null;

/**
 * Retourne le délai entre deux réservations.
 */
private function reservationBuffer(
    User $connectedUser
): int {

    return $this
        ->reservationSetting(
            $connectedUser
        )
        ->reservation_buffer;

}

/**
 * Enregistre une nouvelle réservation.
 */
private function createReservationRecord(
    array $data,
    User $connectedUser
): Reservation {

    return Reservation::create([

        'entreprise_id' => $connectedUser->entreprise_id,

        'user_id' => $connectedUser->id,

        'room_id' => $data['room_id'],

        'motif' => $data['motif'],

        'nombre_participants' =>
            $data['nombre_participants'],

        'date_reservation' =>
            $data['date_reservation'],

        'heure_debut' =>
            $data['heure_debut'],

        'heure_fin' =>
            $data['heure_fin'],

        'statut' =>
            ReservationStatus::CONFIRMEE,

    ]);

}

/**
 * Vérifie que la réservation
 * peut être annulée.
 */
private function ensureReservationCanBeCancelled(
    Reservation $reservation
): void {

    if (
        $reservation->statut->isCancelled()
    ) {

        throw ValidationException::withMessages([

            'reservation' => [

                "Cette réservation est déjà annulée."

            ],

        ]);

    }

    if (
        $reservation->statut->isFinished()
    ) {

        throw ValidationException::withMessages([

            'reservation' => [

                "Une réservation terminée ne peut pas être annulée."

            ],

        ]);

    }

}

/**
 * Vérifie que l'utilisateur peut
 * annuler la réservation.
 */
private function ensureUserCanCancelReservation(
    Reservation $reservation,
    User $connectedUser
): void {

    /*
    |--------------------------------------------------------------------------
    | Créateur
    |--------------------------------------------------------------------------
    */

    if (
        $reservation->user_id ===
        $connectedUser->id
    ) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Administrateur
    |--------------------------------------------------------------------------
    */

    if (

        $connectedUser->hasPermission(
            'annuler_reservation'
        )

    ) {
        return;
    }

    throw new AuthorizationException(

        "Vous n'êtes pas autorisé à annuler cette réservation."

    );

}

/**
 * Annule une réservation.
 */
private function cancelReservationRecord(
    Reservation $reservation,
    User $connectedUser,
    string $reason
): void {

    $reservation->update([

        'statut' =>
            ReservationStatus::ANNULEE,

        'cancelled_at' =>
            now(),

        'cancelled_by' =>
            $connectedUser->id,

        'cancellation_reason' =>
            $reason,

    ]);

}

public function cancelReservation(
    Reservation $reservation,
    array $data,
    User $connectedUser
): Reservation {

    $reservation = $this->baseQuery(
        $connectedUser
    )
    ->whereKey(
        $reservation->id
    )
    ->firstOrFail();

    $this->ensureReservationCanBeCancelled(
        $reservation
    );

    $this->ensureUserCanCancelReservation(
        $reservation,
        $connectedUser
    );

    return DB::transaction(function () use (
        $reservation,
        $connectedUser,
        $data
    ) {

        $this->cancelReservationRecord(
            $reservation,
            $connectedUser,
            $data['reason']
        );

        $this->notifyCreator(
            $reservation
        );

        return $this->loadReservation(
            $reservation
        );

    });

}

/**
 * Crée une réservation.
 */
public function createReservation(
    array $data,
    User $connectedUser
): Reservation {

    $this->validateReservation(
        $data,
        $connectedUser
    );

    return DB::transaction(function () use (
        $data,
        $connectedUser
    ) {

        $reservation =
            $this->createReservationRecord(
                $data,
                $connectedUser
            );

        $this->syncEquipments(
            $reservation,
            $data
        );

        $this->notifyAdmins(
            $reservation
        );

        return $this->loadReservation(
            $reservation
        );

    });

}

/**
 * Retourne une réservation.
 */
public function getReservation(
    Reservation $reservation,
    User $connectedUser
): Reservation {

    return $this->baseQuery(
        $connectedUser
    )

    ->whereKey(
        $reservation->id
    )

    ->firstOrFail();

}

/**
 * Retourne la liste des réservations.
 */
public function getReservations(
    array $filters,
    User $connectedUser
): LengthAwarePaginator {

    $query = $this->baseQuery(
        $connectedUser
    );

    if (! empty($filters['search'])) {

        $query->where(function (Builder $query) use ($filters) {

            $query

                ->where(
                    'motif',
                    'like',
                    "%{$filters['search']}%"
                )

                ->orWhereHas(
                    'room',
                    function (Builder $query) use ($filters) {

                        $query->where(
                            'nom',
                            'like',
                            "%{$filters['search']}%"
                        );

                    }
                );

        });

    }

    return $query

        ->orderByDesc(
            'date_reservation'
        )

        ->orderByDesc(
            'heure_debut'
        )

        ->paginate(20);

}

/**
 * Met à jour une réservation.
 */
private function updateReservationRecord(
    Reservation $reservation,
    array $data
): void {

    $reservation->update([

        'room_id' => $data['room_id'],

        'motif' => $data['motif'],

        'nombre_participants' =>
            $data['nombre_participants'],

        'date_reservation' =>
            $data['date_reservation'],

        'heure_debut' =>
            $data['heure_debut'],

        'heure_fin' =>
            $data['heure_fin'],

    ]);

}

public function updateReservation(
    Reservation $reservation,
    array $data,
    User $connectedUser
): Reservation {

    $reservation = $this->baseQuery(
        $connectedUser
    )
    ->whereKey(
        $reservation->id
    )
    ->firstOrFail();

    $this->validateReservation(
        $data,
        $connectedUser,
        $reservation
    );

    return DB::transaction(function () use (
        $reservation,
        $data
    ) {

        $this->updateReservationRecord(
            $reservation,
            $data
        );

        $this->syncEquipments(
            $reservation,
            $data
        );

        $this->notifyAdmins(
            $reservation
        );

        return $this->loadReservation(
            $reservation
        );

    });

}

/**
 * Requête de base des réservations.
 */
private function baseQuery(
    User $connectedUser
): Builder {

    return Reservation::query()

        ->with([
            'creator',
            'room',
            'equipments',
            'cancelledBy',
        ])

        ->where(
            'entreprise_id',
            $connectedUser->entreprise_id
        );

}

/**
 * Charge les relations
 * d'une réservation.
 */
private function loadReservation(
    Reservation $reservation
): Reservation {

    return $reservation->load([

        'creator',

        'room',

        'equipments',

        'cancelledBy',

    ]);

}

/**
 * Requête de base des réservations.
 */
private function reservationQuery(
    array $data,
    ?Reservation $reservation = null
): Builder {

    $query = Reservation::query()

        ->where(
            'date_reservation',
            $data['date_reservation']
        );

    if ($reservation) {

        $query->where(
            'id',
            '!=',
            $reservation->id
        );

    }

    return $query;

}

/**
 * Vérifie toutes les règles métier
 * d'une réservation.
 */
private function validateReservation(
    array $data,
    User $connectedUser,
    ?Reservation $reservation = null
): void {

    $this->ensureUserCanReserve(
        $connectedUser
    );

    $this->ensureRoomBelongsToEntreprise(
        $connectedUser,
        $data
    );

    $this->ensureRoomCapacity(
        $data
    );

    $this->ensureReservationWithinServiceHours(
        $connectedUser,
        $data
    );

    $this->ensureRoomAvailability(
        $connectedUser,
        $data,
        $reservation
    );

    $this->ensureUserAvailability(
        $connectedUser,
        $data,
        $reservation
    );

    $this->ensureEquipments(
        $connectedUser,
        $data,
        $reservation
    );

}

/**
 * Retourne la salle concernée.
 */
private function room(
    int $roomId
): Room {

    if ($this->room === null) {

        $this->room = Room::findOrFail(
            $roomId
        );

    }

    return $this->room;

}

/**
 * Retourne l'horaire de service
 * correspondant à la réservation.
 */
private function serviceHour(
    User $connectedUser,
    string $date
): ServiceHour {

    if ($this->serviceHour === null) {

        $dayOfWeek = DayOfWeek::from(
            strtoupper(
                Carbon::parse($date)
                    ->englishDayOfWeek
            )
        );

        $this->serviceHour =
            $this->reservationSetting(
                $connectedUser
            )
            ->serviceHours
            ->firstWhere(
                'day_of_week',
                $dayOfWeek
            );

    }

    if (! $this->serviceHour) {

        throw ValidationException::withMessages([

            'date_reservation' => [

                "Aucun horaire de service n'est configuré pour ce jour."

            ],

        ]);

    }

    return $this->serviceHour;

}

/**
 * Vérifie que l'utilisateur peut réserver
 * une salle.
 */
private function ensureUserCanReserve(
    User $connectedUser
): void {

    if (
        ! $connectedUser->hasPermission(
            'reserver_salle'
        )
    ) {

        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à effectuer une réservation."
        );

    }

}

/**
 * Vérifie que la salle appartient
 * à l'entreprise.
 */
private function ensureRoomBelongsToEntreprise(
    User $connectedUser,
    array $data
): void {

    $room = $this->room(
        $data['room_id']
    );

    if (
        $room->entreprise_id !==
        $connectedUser->entreprise_id
    ) {

        throw ValidationException::withMessages([

            'room_id' => [

                "La salle sélectionnée n'appartient pas à votre entreprise."

            ],

        ]);

    }

}

/**
 * Vérifie que la salle peut accueillir
 * le nombre de participants demandé.
 */
private function ensureRoomCapacity(
    array $data
): void {

    $room = $this->room(
        $data['room_id']
    );

    if (
        $data['nombre_participants']
        >
        $room->capacite
    ) {

        throw ValidationException::withMessages([

            'nombre_participants' => [

                "Le nombre de participants dépasse la capacité de la salle."

            ],

        ]);

    }

}

/**
 * Retourne la configuration
 * des réservations de l'entreprise.
 */
private function reservationSetting(
    User $connectedUser
): ReservationSetting {

    if ($this->reservationSetting === null) {

        $this->reservationSetting = ReservationSetting::query()

            ->with('serviceHours')

            ->where(
                'entreprise_id',
                $connectedUser->entreprise_id
            )

            ->firstOrFail();

    }

    return $this->reservationSetting;

}

/**
 * Vérifie que la réservation est comprise
 * dans les horaires de service.
 */
private function ensureReservationWithinServiceHours(
    User $connectedUser,
    array $data
): void {

    $serviceHour = $this->serviceHour(
        $connectedUser,
        $data['date_reservation']
    );

    if (! $serviceHour->is_open) {

        throw ValidationException::withMessages([

            'date_reservation' => [

                "L'entreprise est fermée ce jour."

            ],

        ]);

    }

    if (
        $data['heure_debut'] < $serviceHour->start_time
    ) {

        throw ValidationException::withMessages([

            'heure_debut' => [

                "L'heure de début est en dehors des horaires de service."

            ],

        ]);

    }

    if (
        $data['heure_fin'] > $serviceHour->end_time
    ) {

        throw ValidationException::withMessages([

            'heure_fin' => [

                "L'heure de fin est en dehors des horaires de service."

            ],

        ]);

    }

}

/**
 * Retourne les réservations de la salle.
 */
private function roomReservations(
    array $data,
    ?Reservation $reservation = null
): Collection {

    return $this
        ->reservationQuery(
            $data,
            $reservation
        )

        ->where(
            'room_id',
            $data['room_id']
        )

        ->get();

}

/**
 * Détermine si deux réservations
 * se chevauchent.
 */
private function reservationsOverlap(
    string $newStart,
    string $newEnd,
    string $existingStart,
    string $existingEnd,
    int $buffer
): bool {

    $newStart = Carbon::parse($newStart);

    $newEnd = Carbon::parse($newEnd);

    $existingStart = Carbon::parse(
        $existingStart
    )->subMinutes($buffer);

    $existingEnd = Carbon::parse(
        $existingEnd
    )->addMinutes($buffer);

    return
        $newStart->lt($existingEnd)
        &&
        $newEnd->gt($existingStart);

}

/**
 * Vérifie que la salle est disponible.
 */
private function ensureRoomAvailability(
    User $connectedUser,
    array $data,
    ?Reservation $reservation = null
): void {

    $buffer = $this->reservationBuffer(
        $connectedUser
    );

    foreach (

        $this->roomReservations(
            $data,
            $reservation
        )

        as $existingReservation

    ) {

        if (

            $this->reservationsOverlap(

                $data['heure_debut'],

                $data['heure_fin'],

                $existingReservation->heure_debut,

                $existingReservation->heure_fin,

                $buffer

            )

        ) {

            throw ValidationException::withMessages([

                'room_id' => [

                    "La salle est déjà réservée sur ce créneau."

                ],

            ]);

        }

    }

}

/**
 * Retourne les réservations de l'utilisateur.
 */
private function userReservations(
    User $connectedUser,
    array $data,
    ?Reservation $reservation = null
): Collection {

    return $this
        ->reservationQuery(
            $data,
            $reservation
        )

        ->where(
            'user_id',
            $connectedUser->id
        )

        ->get();

}

/**
 * Vérifie que l'utilisateur n'a pas déjà
 * une réservation sur ce créneau.
 */
private function ensureUserAvailability(
    User $connectedUser,
    array $data,
    ?Reservation $reservation = null
): void {

    $buffer = $this->reservationBuffer(
        $connectedUser
    );

    foreach (

        $this->userReservations(
            $connectedUser,
            $data,
            $reservation
        )

        as $existingReservation

    ) {

        if (

            $this->reservationsOverlap(

                $data['heure_debut'],

                $data['heure_fin'],

                $existingReservation->heure_debut,

                $existingReservation->heure_fin,

                $buffer

            )

        ) {

            throw ValidationException::withMessages([

                'reservation' => [

                    "Vous possédez déjà une réservation sur ce créneau."

                ],

            ]);

        }

    }

}

/**
 * Retourne les matériels concernés
 * par la réservation.
 */
private function equipments(
    array $data
): Collection {

    if (empty($data['equipments'])) {

        return new Collection();

    }

    return Equipment::query()

        ->whereIn(
            'id',
            $data['equipments']
        )

        ->get();

}

/**
 * Retourne les réservations
 * utilisant un matériel.
 */
private function equipmentReservations(
    Equipment $equipment,
    array $data,
    ?Reservation $reservation = null
): Collection {

    return $this
        ->reservationQuery(
            $data,
            $reservation
        )

        ->whereHas(
            'equipments',
            function (Builder $query) use ($equipment) {

                $query->where(
                    'equipment_id',
                    $equipment->id
                );

            }
        )

        ->get();

}

/**
 * Vérifie que les matériels demandés
 * sont valides et disponibles.
 */
private function ensureEquipments(
    User $connectedUser,
    array $data,
    ?Reservation $reservation = null
): void {

    $buffer = $this->reservationBuffer(
        $connectedUser
    );

    foreach (

        $this->equipments($data)

        as $equipment

    ) {

        /*
        |--------------------------------------------------------------------------
        | Entreprise
        |--------------------------------------------------------------------------
        */

        if (
            $equipment->entreprise_id !==
            $connectedUser->entreprise_id
        ) {

            throw ValidationException::withMessages([

                'equipments' => [

                    "Un des matériels sélectionnés n'appartient pas à votre entreprise."

                ],

            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | Empruntable
        |--------------------------------------------------------------------------
        */

        if (
            ! $equipment->usage_type->isBorrowable()
        ) {

            throw ValidationException::withMessages([

                'equipments' => [

                    "Le matériel {$equipment->nom} ne peut pas être réservé."

                ],

            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | Etat
        |--------------------------------------------------------------------------
        */

        if (
            ! $equipment->etat->isAvailable()
        ) {

            throw ValidationException::withMessages([

                'equipments' => [

                    "Le matériel {$equipment->nom} n'est pas disponible."

                ],

            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | Disponibilité
        |--------------------------------------------------------------------------
        */

        foreach (

            $this->equipmentReservations(
                $equipment,
                $data,
                $reservation
            )

            as $existingReservation

        ) {

            if (

                $this->reservationsOverlap(
                    $data['heure_debut'],
                    $data['heure_fin'],
                    $existingReservation->heure_debut,
                    $existingReservation->heure_fin,
                    $buffer

                )

            ) {

                throw ValidationException::withMessages([
                    'equipments' => [
                        "Le matériel {$equipment->nom} est déjà réservé sur ce créneau."
                    ],

                ]);

            }

        }

    }

}

/**
 * Associe les matériels
 * à une réservation.
 */
private function syncEquipments(
    Reservation $reservation,
    array $data
): void {

    $reservation
        ->equipments()
        ->sync(
            $data['equipments'] ?? []
        );

}

/**
 * Notifie les administrateurs.
 */
private function notifyAdmins(
    Reservation $reservation
): void {

    if (
        $reservation->equipments()->doesntExist()
    ) {
        return;
    }

    // TODO
}

/**
 * Notifie le créateur
 * de la réservation.
 */
private function notifyCreator(
    Reservation $reservation
): void {

    if (
        $reservation->cancelled_by ===
        $reservation->user_id
    ) {
        return;
    }

    // TODO
}
}