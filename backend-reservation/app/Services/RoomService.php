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
use App\Enums\ActivityLog\ActivityModule;

class RoomService
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {
    }

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

    $this->activityLogService->log(
        $user,
        ActivityModule::SALLE,
        'room.created',
        "A créé la salle {$room->nom}.",
        $room
    );

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
 * Retourne les statistiques des salles.
 */
public function getStats(User $user): array
{
    $entrepriseId = $user->entreprise_id;

    $total = Room::where(
        'entreprise_id',
        $entrepriseId
    )->count();

    $meetingRooms = Room::where(
        'entreprise_id',
        $entrepriseId
    )
        ->where('type', RoomType::MEETING)
        ->count();

    $storageRooms = Room::where(
        'entreprise_id',
        $entrepriseId
    )
        ->where('type', RoomType::STORAGE)
        ->count();

    $meetingRoomsOccupied = Room::where(
        'entreprise_id',
        $entrepriseId
    )
        ->where('type', RoomType::MEETING)
        ->where('statut', RoomStatus::OCCUPEE)
        ->count();

    $inMaintenance = Room::where(
        'entreprise_id',
        $entrepriseId
    )
        ->where('statut', RoomStatus::MAINTENANCE)
        ->count();

    return [

        'total' => $total,

        'meeting_rooms' => $meetingRooms,

        'storage_rooms' => $storageRooms,

        'meeting_rooms_occupied' => $meetingRoomsOccupied,

        'in_maintenance' => $inMaintenance,

    ];
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

    $before = [
        'le nom' => $room->nom,
        'le type' => $room->type->label(),
        'la capacité' => $room->capacite ?? '—',
        'la localisation' => $room->localisation,
        'la description' => $room->description ?? '—',
        'le statut' => $room->statut->label(),
    ];

    $room->fill($data);
    $room->save();

    $after = [
        'le nom' => $room->nom,
        'le type' => $room->type->label(),
        'la capacité' => $room->capacite ?? '—',
        'la localisation' => $room->localisation,
        'la description' => $room->description ?? '—',
        'le statut' => $room->statut->label(),
    ];

    $changes = $this->activityLogService->describeChanges($before, $after);

    $this->activityLogService->log(
        $user,
        ActivityModule::SALLE,
        'room.updated',
        $changes
            ? "A modifié la salle {$room->nom} : " . implode(', ', $changes) . '.'
            : "A modifié la salle {$room->nom}.",
        $room
    );

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

        $previousStatus = $room->statut;

        $room->statut = $status;
        $room->save();

        $this->activityLogService->log(
            $user,
            ActivityModule::SALLE,
            'room.status_updated',
            "A changé le statut de la salle {$room->nom} de « {$previousStatus->label()} » à « {$status->label()} ».",
            $room
        );

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

        $nom = $room->nom;

        $room->delete();

        $this->activityLogService->log(
            $user,
            ActivityModule::SALLE,
            'room.deleted',
            "A supprimé la salle {$nom}.",
            $room
        );

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