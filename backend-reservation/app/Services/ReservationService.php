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
use App\Enums\Reservation\CancellationReason;
use App\Enums\Equipment\EmpruntableEquipmentStatus;
use App\Enums\RoomStatus;
use App\Enums\ActivityLog\ActivityModule;
use App\Notifications\ReservationCancelledNotification;


class ReservationService
{
    public function __construct(
        private ActivityLogService $activityLogService,
        private NotificationService $notificationService
    ) {
    }

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

    $end = Carbon::parse(
        $reservation->date_reservation->toDateString()
        . ' '
        . $reservation->heure_fin
    );

    if ($end->isPast()) {

        throw ValidationException::withMessages([

            'reservation' => [

                "Une réservation déjà passée ne peut pas être annulée."

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
 * Vérifie que l'utilisateur peut
 * modifier la réservation.
 */
private function ensureUserCanEditReservation(
    Reservation $reservation,
    User $connectedUser
): void {

    if (
        $reservation->user_id !==
        $connectedUser->id
    ) {

        throw new AuthorizationException(

            "Seul le créateur de la réservation peut la modifier."

        );

    }

}

/**
 * Vérifie que la réservation peut
 * encore être modifiée.
 */
private function ensureReservationIsEditable(
    Reservation $reservation
): void {

    if (
        $reservation->statut->isCancelled()
    ) {

        throw ValidationException::withMessages([

            'reservation' => [

                "Cette réservation est annulée et ne peut plus être modifiée."

            ],

        ]);

    }

    $end = Carbon::parse(
        $reservation->date_reservation->toDateString()
        . ' '
        . $reservation->heure_fin
    );

    if ($end->isPast()) {

        throw ValidationException::withMessages([

            'reservation' => [

                "Une réservation déjà passée ne peut plus être modifiée."

            ],

        ]);

    }

}

/**
 * Annule une réservation.
 */
private function cancelReservationRecord(
    Reservation $reservation,
    User $connectedUser
): void {

    $reason = $reservation->user_id === $connectedUser->id
        ? CancellationReason::USER_REQUEST
        : CancellationReason::ADMIN_DECISION;

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
            $connectedUser
        );

        $this->notifyCreator(
            $reservation
        );

        $reservation = $this->loadReservation(
            $reservation
        );

        $this->activityLogService->log(
            $connectedUser,
            ActivityModule::RESERVATION,
            'reservation.cancelled',
            "A annulé la réservation de la salle {$reservation->room->nom} du {$reservation->date_reservation->format('d/m/Y')} (motif : {$reservation->cancellation_reason->label()}).",
            $reservation
        );

        return $reservation;

    });

}

/**
 * Crée une réservation.
 */
public function createReservation(
    array $data,
    User $connectedUser
): Reservation {

    return DB::transaction(function () use (
        $data,
        $connectedUser
    ) {

        // La validation (dont la vérification des chevauchements) doit s'exécuter à
        // l'intérieur de la transaction : reservationQuery() pose un verrou pessimiste
        // (lockForUpdate) sur les réservations existantes, qui n'est tenu que le temps
        // de la transaction. L'exécuter avant DB::transaction() laisserait une fenêtre
        // où deux requêtes concurrentes passent toutes deux la vérification avant que
        // l'une d'elles n'insère sa réservation (race condition de double réservation).
        $this->validateReservation(
            $data,
            $connectedUser
        );

        $reservation =
            $this->createReservationRecord(
                $data,
                $connectedUser
            );

        $this->syncEquipments(
            $reservation,
            $data
        );

        $reservation = $this->loadReservation(
            $reservation
        );

        $this->activityLogService->log(
            $connectedUser,
            ActivityModule::RESERVATION,
            'reservation.created',
            "A créé une réservation pour la salle {$reservation->room->nom} le {$reservation->date_reservation->format('d/m/Y')} de {$reservation->heure_debut} à {$reservation->heure_fin}.",
            $reservation
        );

        return $reservation;

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

    if (! empty($filters['from_date'])) {

        $query->where(
            'date_reservation',
            '>=',
            $filters['from_date']
        );

    }

    if (! empty($filters['date'])) {

        $query->whereDate(
            'date_reservation',
            $filters['date']
        );

    }

    if (! empty($filters['room_id'])) {

        $query->where(
            'room_id',
            $filters['room_id']
        );

    }

    if (! empty($filters['status'])) {

        $this->applyStatusFilter(
            $query,
            $filters['status']
        );

    }

    if (! empty($filters['user_id'])) {

        $query->where(
            'user_id',
            $filters['user_id']
        );

    }

    $direction = $filters['direction'] ?? 'desc';

    return $query

        ->orderBy(
            $filters['sort'] ?? 'date_reservation',
            $direction
        )

        ->orderBy(
            'heure_debut',
            $direction
        )

        ->paginate(
            $filters['per_page'] ?? 20
        );

}

/**
 * Applique le filtre de statut, y compris les statuts
 * dérivés du temps ("EN_COURS"/"PASSEE") qui ne sont pas
 * stockés en base mais calculés à partir de la date/heure.
 */
private function applyStatusFilter(
    Builder $query,
    string $status
): void {

    $now = Carbon::now();

    if ($status === 'EN_COURS') {

        $query
            ->where('statut', ReservationStatus::CONFIRMEE)
            ->whereDate('date_reservation', $now->toDateString())
            ->where('heure_debut', '<=', $now->format('H:i:s'))
            ->where('heure_fin', '>', $now->format('H:i:s'));

        return;

    }

    if ($status === 'PASSEE') {

        $query
            ->where('statut', ReservationStatus::CONFIRMEE)
            ->where(function (Builder $query) use ($now) {

                $query
                    ->where('date_reservation', '<', $now->toDateString())
                    ->orWhere(function (Builder $query) use ($now) {

                        $query
                            ->whereDate('date_reservation', $now->toDateString())
                            ->where('heure_fin', '<=', $now->format('H:i:s'));

                    });

            });

        return;

    }

    if ($status === ReservationStatus::CONFIRMEE->value) {

        $query
            ->where('statut', ReservationStatus::CONFIRMEE)
            ->where(function (Builder $query) use ($now) {

                $query
                    ->where('date_reservation', '>', $now->toDateString())
                    ->orWhere(function (Builder $query) use ($now) {

                        $query
                            ->whereDate('date_reservation', $now->toDateString())
                            ->where('heure_debut', '>', $now->format('H:i:s'));

                    });

            });

        return;

    }

    $query->where('statut', $status);

}

/**
 * Retourne le nombre de réservations
 * par jour sur une période.
 */
public function getReservationCounts(
    array $filters,
    User $connectedUser
): array {

    $rows = Reservation::query()

        ->where(
            'entreprise_id',
            $connectedUser->entreprise_id
        )

        ->when(
            ! $connectedUser->hasPermission('consulter_toutes_reservations'),
            fn (Builder $query) => $query->where('user_id', $connectedUser->id)
        )

        ->whereBetween(
            'date_reservation',
            [
                $filters['from'],
                $filters['to'],
            ]
        )

        ->selectRaw(
            'date_reservation, count(*) as total'
        )

        ->groupBy(
            'date_reservation'
        )

        ->get();

    $counts = [];

    foreach ($rows as $row) {

        $counts[
            Carbon::parse($row->date_reservation)->toDateString()
        ] = (int) $row->total;

    }

    return $counts;

}

/**
 * Retourne le résumé des réservations
 * d'une journée.
 */
public function getDailySummary(
    string $date,
    User $connectedUser
): array {

    $entrepriseId = $connectedUser->entreprise_id;

    $reservations = Reservation::query()

        ->where(
            'entreprise_id',
            $entrepriseId
        )

        ->when(
            ! $connectedUser->hasPermission('consulter_toutes_reservations'),
            fn (Builder $query) => $query->where('user_id', $connectedUser->id)
        )

        ->where(
            'date_reservation',
            $date
        )

        ->withCount('equipments')

        ->get();

    $total = $reservations->count();

    $cancelled = $reservations

        ->where(
            'statut',
            ReservationStatus::ANNULEE
        )

        ->count();

    $confirmed = $total - $cancelled;

    $confirmedReservations = $reservations

        ->where(
            'statut',
            ReservationStatus::CONFIRMEE
        );

    $roomsOccupied = $confirmedReservations

        ->pluck('room_id')

        ->unique()

        ->count();

    $equipmentsReserved = (int) $confirmedReservations

        ->sum('equipments_count');

    $equipmentsTotal = Equipment::query()

        ->where(
            'entreprise_id',
            $entrepriseId
        )

        ->count();

    return [

        'date' => $date,

        'reservations' => [
            'total' => $total,
            'confirmed' => $confirmed,
            'cancelled' => $cancelled,
        ],

        'rooms_occupied' => $roomsOccupied,

        'equipments' => [
            'reserved' => $equipmentsReserved,
            'total' => $equipmentsTotal,
        ],

    ];

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

    $this->ensureUserCanEditReservation(
        $reservation,
        $connectedUser
    );

    $this->ensureReservationIsEditable(
        $reservation
    );

    return DB::transaction(function () use (
        $reservation,
        $data,
        $connectedUser
    ) {

        // Cf. createReservation() : la validation doit s'exécuter dans la transaction
        // pour que le verrou pessimiste posé par reservationQuery() soit effectif.
        $this->validateReservation(
            $data,
            $connectedUser,
            $reservation
        );

        $this->updateReservationRecord(
            $reservation,
            $data
        );

        $this->syncEquipments(
            $reservation,
            $data
        );

        $reservation = $this->loadReservation(
            $reservation
        );

        $this->activityLogService->log(
            $connectedUser,
            ActivityModule::RESERVATION,
            'reservation.updated',
            "A modifié la réservation de la salle {$reservation->room->nom} du {$reservation->date_reservation->format('d/m/Y')}.",
            $reservation
        );

        return $reservation;

    });

}

/**
 * Requête de base des réservations.
 */
private function baseQuery(
    User $connectedUser
): Builder {

    $query = Reservation::query()

        ->with([
            'user',
            'room',
            'equipments',
            'cancelledBy',
        ])

        ->where(
            'entreprise_id',
            $connectedUser->entreprise_id
        );

    if (
        ! $connectedUser->hasPermission(
            'consulter_toutes_reservations'
        )
    ) {

        $query->where(
            'user_id',
            $connectedUser->id
        );

    }

    return $query;

}

/**
 * Charge les relations
 * d'une réservation.
 */
private function loadReservation(
    Reservation $reservation
): Reservation {

    return $reservation->load([

        'user',

        'room',

        'equipments',

        'cancelledBy',

    ]);

}

/**
 * Requête de base des réservations, utilisée uniquement pour les vérifications de
 * chevauchement (salle/utilisateur/matériel). lockForUpdate() : pose un verrou
 * pessimiste sur les lignes lues, tenu jusqu'à la fin de la transaction englobante
 * (createReservation()/updateReservation()) pour empêcher deux requêtes concurrentes
 * de passer toutes deux la vérification avant qu'aucune n'ait encore inséré sa
 * réservation.
 */
private function reservationQuery(
    array $data,
    ?Reservation $reservation = null
): Builder {

    $query = Reservation::query()

        // whereDate() (et non where()) : le format réellement stocké pour une colonne
        // "date" est "Y-m-d H:i:s" (Eloquent sérialise toujours via $dateFormat à
        // l'écriture, quel que soit le cast de lecture) — une égalité stricte contre
        // "Y-m-d" ne matche que parce que MySQL tronque une colonne DATE ; whereDate()
        // compare explicitement la partie date, correct quel que soit le SGBD.
        ->whereDate(
            'date_reservation',
            $data['date_reservation']
        )

        ->where(
            'statut',
            ReservationStatus::CONFIRMEE
        )

        ->lockForUpdate();

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

    $this->ensureRoomIsAvailableForReservation(
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
 * Retourne la salle concernée. lockForUpdate() : verrouille la ligne pour toute la
 * durée de la transaction englobante (create/updateReservation), ce que le verrou sur
 * les réservations existantes (reservationQuery()) ne suffit pas à garantir seul —
 * s'il n'existe encore aucune réservation conflictuelle en base, il n'y a alors rien à
 * verrouiller de ce côté et deux requêtes concurrentes pourraient toutes deux insérer
 * la "première" réservation d'un même créneau. Verrouiller la salle elle-même sérialise
 * systématiquement toute tentative de réservation sur cette salle, y compris ce cas.
 * Uniquement appelé depuis validateReservation() (donc toujours dans une transaction).
 */
private function room(
    int $roomId
): Room {

    if ($this->room === null) {

        $this->room = Room::lockForUpdate()->findOrFail(
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
 * Vérifie que la salle est disponible
 * (ni en maintenance, ni hors service, ni occupée).
 */
private function ensureRoomIsAvailableForReservation(
    array $data
): void {

    $room = $this->room(
        $data['room_id']
    );

    if ($room->statut !== RoomStatus::DISPONIBLE) {

        throw ValidationException::withMessages([

            'room_id' => [

                "Cette salle n'est pas disponible (statut : {$room->statut->label()})."

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
            ! EmpruntableEquipmentStatus::from($equipment->etat)->isAvailable()
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

    if (! $reservation->user) {
        return;
    }

    $this->notificationService->notify(
        $reservation->user,
        'reservations',
        new ReservationCancelledNotification($reservation)
    );
}
}