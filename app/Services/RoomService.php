<?php

namespace App\Services;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RoomService
{
    /**
     * Création d'une salle.
     */
    public function createRoom(array $data, User $creator): JsonResponse
    {
        if (!$creator->hasPermission('creer_salle')) {

            return response()->json([
                'message' => "Vous n'êtes pas autorisé à créer une salle."
            ], 403);

        }

        try {

    $room = Room::create($data);

    return response()->json([
        'message' => 'Salle créée avec succès.',
        'room' => $room,
    ], 201);

} catch (\Throwable $e) {

    return response()->json([
        'message' => $e->getMessage(),
        'file'    => $e->getFile(),
        'line'    => $e->getLine(),
    ], 500);

}
    }

    /**
 * Liste toutes les salles.
 */
public function getAllRooms(): JsonResponse
{
    $rooms = Room::orderBy('nom')->get();

    return response()->json($rooms);
}
/**
 * Affiche une salle.
 */
public function getRoom(Room $room): JsonResponse
{
    return response()->json($room);
}
}