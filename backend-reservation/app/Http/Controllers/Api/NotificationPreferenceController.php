<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateNotificationPreferencesRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {
    }

    /**
     * Retourne les préférences de notification
     * de l'utilisateur connecté.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->userService->getNotificationPreferences(
                $request->user()
            ),
        ]);
    }

    /**
     * Met à jour les préférences de notification
     * de l'utilisateur connecté.
     */
    public function update(UpdateNotificationPreferencesRequest $request): JsonResponse
    {
        return response()->json([
            'message' => 'Préférences de notification mises à jour avec succès.',
            'data' => $this->userService->updateNotificationPreferences(
                $request->validated(),
                $request->user()
            ),
        ]);
    }
}
