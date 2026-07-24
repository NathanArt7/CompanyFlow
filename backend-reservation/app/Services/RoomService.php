<?php

namespace App\Services;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\RoomResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Enums\RoomType;
use App\Enums\RoomStatus;

class RoomService
{
    /**
     * Création d'une salle.
     */
    public function createRoom(array $data, User $user): JsonResponse
    {
        if (!$user->hasPermission('creer_salle')) {

            return response()->json([
                'message' => "Vous n'êtes pas autorisé à créer une salle."
            ], 403);

        }

        try {
    if ($data['type'] === RoomType::STORAGE->value) {
    $data['capacite'] = null;}
    $room = new Room($data);
    $room->entreprise_id = $user->entreprise_id;
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
public function getAllRooms(array $filters, User $user): AnonymousResourceCollection
{
    $query = Room::where('entreprise_id', $user->entreprise_id);

if (!empty($filters['search'])) {

    $query->where(function ($query) use ($filters) {

        $query
            ->where('nom', 'like', "%{$filters['search']}%")
            ->orWhere('code', 'like', "%{$filters['search']}%");

    });

}

if (!empty($filters['statut'])) {

    $query->where(
        'statut',
        $filters['statut']
    );

}

if (!empty($filters['type'])) {

    $query->where(
        'type',
        $filters['type']
    );

}

$query->orderBy(
    $filters['sort'] ?? 'nom',
    $filters['direction'] ?? 'asc'
);

$rooms = $query->paginate(
    $filters['per_page'] ?? 20
);

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

public function updateRoom(Room $room, array $data, User $user): JsonResponse
{
    if (!$user->hasPermission('modifier_salle')) {
    return response()->json([
        'message' => "Vous n'êtes pas autorisé à modifier une salle."
    ], 403);
}

if ($room->entreprise_id !== $user->entreprise_id) {
    throw new NotFoundHttpException('Salle introuvable.');
}

try {

    if ($data['type'] === RoomType::STORAGE->value) {
        $data['capacite'] = null;
    }

    $room->fill($data);
    $room->save();

    return response()->json([
        'message' => 'Salle mise à jour avec succès.',
        'room' => new RoomResource($room),
    ]);

} catch (\Throwable $e) {

    Log::error($e);

    return response()->json([
        'message' => 'Une erreur est survenue lors de la mise à jour de la salle.'
    ], 500);
}
}

public function updateStatus(Room $room, RoomStatus $status, User $user): JsonResponse
{
    if (!$user->hasPermission('bloquer_salle')) {

        return response()->json([
            'message' => "Vous n'êtes pas autorisé à modifier le statut d'une salle."
        ], 403);

    }

    if ($room->entreprise_id !== $user->entreprise_id) {
        throw new NotFoundHttpException('Salle introuvable.');
    }

    try {

        $room->statut = $status;
        $room->save();

        return response()->json([
            'message' => 'Statut de la salle mis à jour avec succès.',
            'room' => new RoomResource($room),
        ]);

    } catch (\Throwable $e) {

        Log::error($e);

        return response()->json([
            'message' => 'Une erreur est survenue lors de la mise à jour du statut.'
        ], 500);

    }
}

public function deleteRoom(Room $room, User $user): JsonResponse
{
    if (!$user->hasPermission('supprimer_salle')) {

        return response()->json([
            'message' => "Vous n'êtes pas autorisé à supprimer une salle."
        ], 403);

    }

    if ($room->entreprise_id !== $user->entreprise_id) {
        throw new NotFoundHttpException('Salle introuvable.');
    }

    try {

        // TODO:
        // Empêcher la suppression si la salle possède des réservations.

        $room->delete();

        return response()->json([
            'message' => 'Salle supprimée avec succès.'
        ]);

    } catch (\Throwable $e) {

        Log::error($e);

        return response()->json([
            'message' => 'Une erreur est survenue lors de la suppression de la salle.'
        ], 500);

    }
}
}