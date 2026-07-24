<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EquipmentCategory\StoreEquipmentCategoryRequest;
use App\Http\Requests\EquipmentCategory\UpdateEquipmentCategoryRequest;
use App\Http\Resources\EquipmentCategoryResource;
use App\Models\EquipmentCategory;
use App\Services\EquipmentCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\EquipmentCategory\SearchEquipmentCategoryRequest;

class EquipmentCategoryController extends Controller
{
    public function __construct(
        private EquipmentCategoryService $equipmentCategoryService
    ) {
    }

    /**
 * Crée une catégorie de matériel.
 */
public function store(StoreEquipmentCategoryRequest $request): JsonResponse {

    $category = $this->equipmentCategoryService
        ->createCategory(
            $request->validated(),
            $request->user()
        );

    return response()->json([
        'message' => 'Catégorie créée avec succès.',
        'category' => new EquipmentCategoryResource(
            $category
        ),
    ], 201);

}

/**
 * Liste des catégories de matériel.
 */
public function index(SearchEquipmentCategoryRequest $request): JsonResponse {

    $categories = $this->equipmentCategoryService
        ->getAllCategories(
            $request->validated(),
            $request->user()
        );

    return response()->json([
        'categories' => EquipmentCategoryResource::collection(
            $categories
        ),
        'pagination' => [
            'current_page' => $categories->currentPage(),
            'last_page' => $categories->lastPage(),
            'per_page' => $categories->perPage(),
            'total' => $categories->total(),
        ],
    ]);

}

/**
 * Retourne une catégorie de matériel.
 */
public function show(EquipmentCategory $category, Request $request): JsonResponse {

    $category = $this->equipmentCategoryService
        ->getCategory(
            $category,
            $request->user()
        );

    return response()->json([
        'category' => new EquipmentCategoryResource(
            $category
        ),
    ]);

}

/**
 * Met à jour une catégorie de matériel.
 */
public function update(UpdateEquipmentCategoryRequest $request, EquipmentCategory $category): JsonResponse {

    $category = $this->equipmentCategoryService
        ->updateCategory(
            $category,
            $request->validated(),
            $request->user()
        );

    return response()->json([
        'message' => 'Catégorie mise à jour avec succès.',
        'category' => new EquipmentCategoryResource(
            $category
        ),
    ]);

}

/**
 * Supprime une catégorie de matériel.
 */
public function destroy(EquipmentCategory $category, Request $request): JsonResponse {

    $this->equipmentCategoryService
        ->deleteCategory(
            $category,
            $request->user()
        );

    return response()->json([
        'message' => 'Catégorie supprimée avec succès.',
    ]);

}

}