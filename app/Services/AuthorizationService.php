<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use App\Enums\RoleName;

class AuthorizationService
{
    /**
     * Vérifie qu'un utilisateur peut attribuer un rôle.
     *
     * @throws AuthorizationException
     */
    public function ensureCanAssignRole(User $creator, Role $role): void
    {
        if ($creator->role->nom === RoleName::SUPER_ADMIN_GLOBAL->value) {
            return;
        }

        if ($creator->role->nom === RoleName::SUPER_ADMIN->value) {
            return;
        }

        if ($creator->role->nom === RoleName::ADMIN->value) {

            if (in_array($role->nom, [
                RoleName::SUPER_ADMIN->value,
                RoleName::ADMIN->value,
            ])) {

                throw new AuthorizationException(
                    "Vous ne pouvez pas attribuer ce rôle."
                );
            }

            return;
        }

        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à attribuer des rôles."
        );
    }
}