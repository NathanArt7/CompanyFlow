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
public function index()
{
    return $this->roomService->getAllRooms();
}
public function show(Room $room)
{
    return $this->roomService->getRoom($room);
}
}