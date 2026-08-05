<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\SearchEquipmentRequest;
use App\Http\Requests\Equipment\StoreEquipmentRequest;
use App\Http\Requests\Equipment\UpdateEquipmentRequest;
use App\Http\Requests\Equipment\UpdateEquipmentStatusRequest;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use App\Services\EquipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function __construct(
    private EquipmentService $equipmentService
) {
}

/**
 * Crée un matériel.
 */
public function store(
    StoreEquipmentRequest $request
): JsonResponse {

    $equipment = $this->equipmentService
        ->createEquipment(
            $request->validated(),
            $request->user()
        );

    return response()->json([
        'message' => 'Matériel créé avec succès.',
        'equipment' => new EquipmentResource(
            $equipment
        ),
    ], 201);

}

/**
 * Liste les matériels.
 */
public function index(
    SearchEquipmentRequest $request
): JsonResponse {

    $equipments = $this->equipmentService
        ->getAllEquipments(
            $request->validated(),
            $request->user()
        );

    return response()->json([
        'equipments' => EquipmentResource::collection(
            $equipments
        ),
        'pagination' => [
            'current_page' => $equipments->currentPage(),
            'last_page' => $equipments->lastPage(),
            'per_page' => $equipments->perPage(),
            'total' => $equipments->total(),
        ],
    ]);

}

/**
 * Statistiques des matériels.
 */
public function stats(
    Request $request
): JsonResponse {

    return response()->json([

        'data' => $this->equipmentService->getStats(
            $request->user()
        ),

    ]);

}

/**
 * Retourne un matériel.
 */
public function show(
    Equipment $equipment,
    Request $request
): JsonResponse {

    $equipment = $this->equipmentService
        ->getEquipment(
            $equipment,
            $request->user()
        );

    return response()->json([
        'equipment' => new EquipmentResource(
            $equipment
        ),
    ]);

}

/**
 * Met à jour un matériel.
 */
public function update(
    UpdateEquipmentRequest $request,
    Equipment $equipment
): JsonResponse {

    $equipment = $this->equipmentService
        ->updateEquipment(
            $equipment,
            $request->validated(),
            $request->user()
        );

    return response()->json([
        'message' => 'Matériel mis à jour avec succès.',
        'equipment' => new EquipmentResource(
            $equipment
        ),
    ]);

}

/**
 * Met à jour uniquement l'état d'un matériel.
 */
public function updateStatus(
    UpdateEquipmentStatusRequest $request,
    Equipment $equipment
): JsonResponse {

    $equipment = $this->equipmentService
        ->updateEquipmentStatus(
            $equipment,
            $request->validated('etat'),
            $request->user()
        );

    return response()->json([
        'message' => "État du matériel mis à jour avec succès.",
        'equipment' => new EquipmentResource(
            $equipment
        ),
    ]);

}

/**
 * Supprime un matériel.
 */
public function destroy(
    Equipment $equipment,
    Request $request
): JsonResponse {

    $this->equipmentService
        ->deleteEquipment(
            $equipment,
            $request->user()
        );

    return response()->json([
        'message' => 'Matériel supprimé avec succès.',
    ]);

}
}
