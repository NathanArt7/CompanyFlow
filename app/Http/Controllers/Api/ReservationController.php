<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelReservationRequest;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService $reservationService
    ) {
    }

    /**
     * Liste des réservations.
     */
    public function index(
        Request $request
    ): JsonResponse {

        $reservations =
            $this->reservationService
                ->getReservations(
                    $request->all(),
                    $request->user()
                );

        return response()->json(
            $reservations
        );

    }

    /**
     * Crée une réservation.
     */
    public function store(
        StoreReservationRequest $request
    ): JsonResponse {

        $reservation =
            $this->reservationService
                ->createReservation(
                    $request->validated(),
                    $request->user()
                );

        return response()->json([

            'message' =>
                'Réservation créée avec succès.',

            'data' =>
                $reservation,

        ], 201);

    }

    /**
     * Affiche une réservation.
     */
    public function show(
         Request $request,
        Reservation $reservation
    ): JsonResponse {

        $reservation =
            $this->reservationService
                ->getReservation(
                    $reservation,
                    $request->user()
                );

        return response()->json([

            'data' => $reservation,

        ]);

    }

    /**
     * Met à jour une réservation.
     */
    public function update(
        UpdateReservationRequest $request,
        Reservation $reservation
    ): JsonResponse {

        $reservation =
            $this->reservationService
                ->updateReservation(
                    $reservation,
                    $request->validated(),
                    $request->user()
                );

        return response()->json([

            'message' =>
                'Réservation mise à jour avec succès.',

            'data' =>
                $reservation,

        ]);

    }

    /**
     * Annule une réservation.
     */
    public function cancel(
        CancelReservationRequest $request,
        Reservation $reservation
    ): JsonResponse {

        $reservation =
            $this->reservationService
                ->cancelReservation(
                    $reservation,
                    $request->validated(),
                    $request->user()
                );

        return response()->json([

            'message' =>
                'Réservation annulée avec succès.',

            'data' =>
                $reservation,

        ]);

    }
}