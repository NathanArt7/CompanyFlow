<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterActivityLogRequest;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;

class ActivityLogController extends Controller
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {
    }

    /**
     * Liste des journaux d'activité, filtrés par module.
     */
    public function index(
        FilterActivityLogRequest $request
    ): JsonResponse {

        $logs =
            $this->activityLogService
                ->getLogs(
                    $request->validated(),
                    $request->user()
                );

        return response()->json(
            $logs
        );

    }
}
