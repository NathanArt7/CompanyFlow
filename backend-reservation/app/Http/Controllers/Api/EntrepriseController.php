<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreEntrepriseRequest;
use App\Services\EntrepriseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateEntrepriseRequest;

class EntrepriseController extends Controller
{
    public function __construct(
        private EntrepriseService $entrepriseService
    ) {
    }

    /**
     * Crée une nouvelle entreprise.
     */
    public function store(StoreEntrepriseRequest $request): JsonResponse
    {
        $entreprise = $this->entrepriseService->createEntreprise(
            $request->validated()
        );

        return response()->json([
            'message' => 'Entreprise créée avec succès. Consultez votre boîte mail pour activer votre compte.',
            'data' => $entreprise,
        ], 201);
    }

    /**
 * Configure l'entreprise après la première connexion.
 */
public function updateConfiguration(
    UpdateEntrepriseRequest $request
): JsonResponse
{
    $entreprise = $this->entrepriseService
        ->configureEntreprise(
            $request->user()->entreprise,
            $request->validated(),
            $request->file('logo')
        );

    return response()->json([
        'message' => 'Entreprise configurée avec succès.',
        'data' => $entreprise,
    ]);
}

/**
 * Retourne les informations de l'entreprise connectée.
 */
public function show(
    Request $request
): JsonResponse {

    return response()->json([
        'data' => $request->user()->entreprise,
    ]);

}

/**
 * Met à jour les informations de l'entreprise
 * depuis la page Paramètres.
 */
public function update(
    UpdateEntrepriseRequest $request
): JsonResponse {

    $entreprise = $this->entrepriseService
        ->updateEntrepriseProfile(
            $request->user()->entreprise,
            $request->validated(),
            $request->file('logo')
        );

    return response()->json([
        'message' => 'Informations de l\'entreprise mises à jour avec succès.',
        'data' => $entreprise,
    ]);

}

}