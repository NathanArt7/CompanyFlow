<?php

namespace App\Services;

use App\Enums\RoomStatus;
use App\Models\Equipment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Retourne les statistiques
     * globales du tableau de bord.
     */
    public function getStats(
        User $connectedUser
    ): array {

        $entrepriseId = $connectedUser->entreprise_id;

        $reservationsToday = Reservation::query()

            ->where(
                'entreprise_id',
                $entrepriseId
            )

            ->where(
                'date_reservation',
                Carbon::today()->toDateString()
            )

            ->count();

        $roomsTotal = Room::query()

            ->where(
                'entreprise_id',
                $entrepriseId
            )

            ->count();

        $roomsAvailable = Room::query()

            ->where(
                'entreprise_id',
                $entrepriseId
            )

            ->where(
                'statut',
                RoomStatus::DISPONIBLE
            )

            ->count();

        $equipmentsTotal = Equipment::query()

            ->where(
                'entreprise_id',
                $entrepriseId
            )

            ->count();

        $equipmentsAvailable = Equipment::query()

            ->where(
                'entreprise_id',
                $entrepriseId
            )

            ->whereIn(
                'etat',
                ['DISPONIBLE', 'FONCTIONNEL']
            )

            ->count();

        $activeUsers = User::query()

            ->where(
                'entreprise_id',
                $entrepriseId
            )

            ->where(
                'actif',
                true
            )

            ->count();

        return [

            'reservations_today' => $reservationsToday,

            'rooms' => [
                'available' => $roomsAvailable,
                'total' => $roomsTotal,
            ],

            'active_users' => $activeUsers,

            'equipments' => [
                'available' => $equipmentsAvailable,
                'total' => $equipmentsTotal,
            ],

        ];

    }

    /**
     * Retourne les alertes
     * du tableau de bord.
     */
    public function getAlerts(
        User $connectedUser
    ): array {

        $entrepriseId = $connectedUser->entreprise_id;

        $equipmentsInMaintenance = Equipment::query()

            ->where(
                'entreprise_id',
                $entrepriseId
            )

            ->where(
                'etat',
                'EN_MAINTENANCE'
            )

            ->count();

        $unactivatedUsers = User::query()

            ->where(
                'entreprise_id',
                $entrepriseId
            )

            ->where(
                'actif',
                false
            )

            ->count();

        $roomsInMaintenance = Room::query()

            ->where(
                'entreprise_id',
                $entrepriseId
            )

            ->where(
                'statut',
                RoomStatus::MAINTENANCE
            )

            ->count();

        return [

            'equipments_in_maintenance' => $equipmentsInMaintenance,

            'unactivated_users' => $unactivatedUsers,

            'rooms_in_maintenance' => $roomsInMaintenance,

        ];

    }
}
