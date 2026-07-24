<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Http\Resources\UserDetailResource;
use App\Models\User;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Http\Requests\FilterUserRequest;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {
    }

    /**
     * Création d'un utilisateur.
     */
   public function store(StoreUserRequest $request): JsonResponse
{
    $user = $this->userService->createUser(
        $request->validated(),
        $request->user()
    );

    return response()->json([
        'message' => 'Utilisateur créé avec succès. Un e-mail d’activation a été envoyé.',
    ], 201);
}

/**
 * Liste des utilisateurs.
 */
public function index(FilterUserRequest $request)
{
    $users = $this->userService->getUsers(
        $request->validated(),
        $request->user()
    );

    return UserResource::collection($users);
}
/**
 * Affiche un utilisateur.
 */
public function show(
    Request $request,
    User $user
): UserDetailResource {

    $user = $this->userService
        ->getUser(
            $request->user(),
            $user
        );

    return new UserDetailResource($user);
}

public function update(
    UpdateUserRequest $request,
    User $user
): UserDetailResource
{
    $user = $this->userService->updateUser(
        $request->validated(),
        $request->user(),
        $user
    );

    return new UserDetailResource($user);
}

/**
 * Active ou désactive un utilisateur.
 */
public function updateStatus(
    UpdateUserStatusRequest $request,
    User $user
): UserDetailResource {

    $user = $this->userService->updateStatus(
        $request->validated(),
        $request->user(),
        $user
    );

    return new UserDetailResource($user);
}

/**
 * Renvoie un nouveau lien d'activation.
 */
public function resendActivation(
    Request $request,
    User $user
): JsonResponse
{
    $this->userService->resendActivationLink(
        $request->user(),
        $user
    );

    return response()->json([
        'message' => 'Un nouveau lien d’activation a été envoyé.'
    ]);
}
}