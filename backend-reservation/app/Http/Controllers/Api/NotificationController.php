<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Liste paginée des notifications de l'utilisateur connecté.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()
                ->notifications()
                ->paginate(15)
        );
    }

    /**
     * Nombre de notifications non lues.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([

            'count' => $request->user()
                ->unreadNotifications()
                ->count(),

        ]);
    }

    /**
     * Marque une notification comme lue.
     */
    public function markAsRead(
        Request $request,
        DatabaseNotification $notification
    ): JsonResponse {

        abort_unless(
            $notification->notifiable_id === $request->user()->id
                && $notification->notifiable_type === get_class($request->user()),
            404
        );

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marquée comme lue.',
        ]);
    }

    /**
     * Marque toutes les notifications de l'utilisateur connecté comme lues.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Toutes les notifications ont été marquées comme lues.',
        ]);
    }
}
