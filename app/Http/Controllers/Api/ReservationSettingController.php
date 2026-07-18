<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReservationSettingController extends Controller
{
    public function __construct(
    private readonly ReservationSettingService $reservationSettingService
) {}

/**
 * Retourne la configuration des réservations.
 */
public function index(
    Request $request
): JsonResponse {

    $reservationSetting =
        $this->reservationSettingService
            ->getReservationSettings(
                $request->user()
            );

    return response()->json([
        'message' =>
            'Configuration récupérée avec succès.',

        'data' =>
            $reservationSetting,
    ]);

}

/**
 * Met à jour la configuration
 * des réservations.
 */
public function update(
    UpdateReservationSettingRequest $request
): JsonResponse {

    $reservationSetting =
        $this->reservationSettingService
            ->updateReservationSettings(
                $request->validated(),
                $request->user()
            );

    return response()->json([

        'message' =>
            'Configuration mise à jour avec succès.',

        'data' =>
            $reservationSetting,

    ]);

}
}
