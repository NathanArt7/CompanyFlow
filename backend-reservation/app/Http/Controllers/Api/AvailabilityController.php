<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Availability\AvailableEquipmentRequest;
use App\Http\Requests\Availability\DayReservationsRequest;
use App\Http\Requests\Availability\WeeklyAvailabilityRequest;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AvailabilityController extends Controller
{
    public function __construct(
        private AvailabilityService $availabilityService
    ) {
    }

    /**
     * Retourne les disponibilités
     * de la semaine.
     */
    public function week(
        WeeklyAvailabilityRequest $request
    ): JsonResponse {

        $availability =
            $this->availabilityService
                ->getWeeklyAvailability(

                    Carbon::parse(
                        $request->validated()['date']
                    ),

                    $request->user()

                );

        return response()->json([

            'data' => $availability,

        ]);

    }

    /**
     * Retourne les matériels
     * disponibles pour un créneau.
     */
    public function availableEquipments(
        AvailableEquipmentRequest $request
    ): JsonResponse {

        $equipments =
            $this->availabilityService
                ->getAvailableEquipments(

                    $request->validated(),

                    $request->user()

                );

        return response()->json([

            'data' => $equipments,

        ]);

    }

    /**
     * Retourne les réservations confirmées
     * de l'entreprise pour une journée.
     */
    public function dayReservations(
        DayReservationsRequest $request
    ): JsonResponse {

        $reservations =
            $this->availabilityService
                ->getDayReservations(

                    $request->validated('date'),

                    $request->user()

                );

        return response()->json([

            'data' => $reservations,

        ]);

    }

}