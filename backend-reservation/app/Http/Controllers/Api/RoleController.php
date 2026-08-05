<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Services\UserService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {
    }

    /**
     * Liste des rôles que l'utilisateur connecté
     * peut attribuer à un autre utilisateur.
     */
    public function index(Request $request)
    {
        $roles = $this->userService->getAssignableRoles(
            $request->user()
        );

        return RoleResource::collection($roles);
    }
}
