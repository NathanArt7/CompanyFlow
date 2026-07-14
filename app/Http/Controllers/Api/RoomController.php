<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Services\RoomService;
use Illuminate\Http\Request;
use App\Models\Room;

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
public function index(Request $request)
{
    return $this->roomService->getAllRooms(
        $request->user()
    );
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
}