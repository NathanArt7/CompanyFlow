<?php

namespace App\Services;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\RoomResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    $room = new Room($data);
    $room->entreprise_id = $creator->entreprise_id;
    $room->save();

    return response()->json([
        'message' => 'Salle créée avec succès.',
        'room' => new RoomResource($room),
    ], 201);

} catch (\Throwable $e) {
    Log::error($e);
    return response()->json([
    'message' => 'Une erreur est survenue lors de la création de la salle.'
    ], 500);
}
    }

    /**
 * Liste toutes les salles.
 */
public function getAllRooms(User $user): AnonymousResourceCollection
{
    $rooms = Room::where('entreprise_id', $user->entreprise_id)
        ->orderBy('nom')
        ->paginate(20);

    return RoomResource::collection($rooms);
}

/**
 * Affiche une salle.
 */
public function getRoom(Room $room, User $user): RoomResource
{
    if ($room->entreprise_id !== $user->entreprise_id) {
    throw new NotFoundHttpException('Salle introuvable.');
}

return new RoomResource($room);
}
}