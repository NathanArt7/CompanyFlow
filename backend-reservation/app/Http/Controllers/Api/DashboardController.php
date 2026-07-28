<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {
    }

    /**
     * Retourne les statistiques
     * du tableau de bord.
     */
    public function stats(
        Request $request
    ): JsonResponse {

        $stats =
            $this->dashboardService
                ->getStats(
                    $request->user()
                );

        return response()->json([

            'data' => $stats,

        ]);

    }

    /**
     * Retourne les alertes
     * du tableau de bord.
     */
    public function alerts(
        Request $request
    ): JsonResponse {

        $alerts =
            $this->dashboardService
                ->getAlerts(
                    $request->user()
                );

        return response()->json([

            'data' => $alerts,

        ]);

    }
}
