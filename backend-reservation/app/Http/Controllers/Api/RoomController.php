<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Services\RoomService;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Requests\UpdateRoomStatusRequest;
use App\Enums\RoomStatus;
use App\Http\Requests\FilterRoomRequest;

class RoomController extends Controller
{
    public function __construct(
        private RoomService $roomService
    ) {
    }

/**
 * Création d'une salle
 */
  public function store(StoreRoomRequest $request)
{
    return $this->roomService->createRoom(
        $request->validated(),
        $request->user()
    );
}

/**
 * Liste des salles.
 */
public function index(FilterRoomRequest $request)
{
   return $this->roomService->getAllRooms(
        $request->validated(),
        $request->user()
    );
}

/**
 * Statistiques des salles.
 */
public function stats(Request $request)
{
    return response()->json([

        'data' => $this->roomService->getStats(
            $request->user()
        ),

    ]);
}

/**
* Détail d'une salle.
*/
public function show(Request $request, Room $room)
{
    return $this->roomService->getRoom(
        $room,
        $request->user()
    );
}

public function update(UpdateRoomRequest $request, Room $room)
{
    return $this->roomService->updateRoom(
        $room,
        $request->validated(),
        $request->user()
    );
}
public function updateStatus(
    UpdateRoomStatusRequest $request,
    Room $room
)
{
    return $this->roomService->updateStatus(
        $room,
        RoomStatus::from($request->validated('statut')),
        $request->user()
    );
}

public function destroy(Room $room, Request $request)
{
    return $this->roomService->deleteRoom(
        $room,
        $request->user()
    );
}
}