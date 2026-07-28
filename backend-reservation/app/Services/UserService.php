<?php

namespace App\Services;

use App\Enums\EntrepriseStatus;
use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Models\Entreprise;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        private ActivationService $activationService,
        private MailService $mailService
    ) {
    }

    /**
     * Création d'un utilisateur.
     */
    public function createUser(array $data, User $creator): User
    {
        $role = Role::findOrFail($data['role_id']);

        $this->validateBusinessRules(
            $creator,
            $role
        );

        return $this->createUserAccount(
            $data,
            $creator
        );
    }

    /**
     * Vérifie les règles métier avant la création d'un utilisateur.
     */
    private function validateBusinessRules(
        User $creator,
        Role $role
    ): void {

        $requiredPermission = $this->getRequiredPermission($role);

        if (! $creator->hasPermission($requiredPermission)) {
            throw new AuthorizationException(
                "Vous ne possédez pas la permission nécessaire."
            );
        }

        if (! $this->canCreateRole($creator, $role)) {
            throw new AuthorizationException(
                "Vous n'êtes pas autorisé à créer ce type d'utilisateur."
            );
        }

        $this->ensureEntrepriseIsActive($creator);
    }

    /**
     * Crée le compte utilisateur.
     */
    private function createUserAccount(array $data, ?User $creator = null): User {

        return DB::transaction(function () use ($data, $creator) {

            $user = User::create([

                'entreprise_id' => $creator
                    ? $creator->entreprise_id
                    : $data['entreprise_id'],

                'nom' => $data['nom'],

                'prenom' => $data['prenom'],

                'email' => $data['email'],

                'password' => Str::password(32),

                'role_id' => $data['role_id'],

                'actif' => false,

                'password_changed' => false,

            ]);

           $this->sendActivationLink(
                $user,
                $creator
            );
            return $user;
        });
    }

    /**
     * Vérifie si le créateur est autorisé à créer le rôle demandé.
     */
    private function canCreateRole(
        User $creator,
        Role $role
    ): bool {

       $roleHierarchy = [

    RoleName::SUPER_ADMIN->value => [
        RoleName::ADMIN->value,
        RoleName::SUPER_EMPLOYE->value,
        RoleName::EMPLOYE->value,
        RoleName::TECHNICIEN->value,
    ],

    RoleName::ADMIN->value => [
        RoleName::SUPER_EMPLOYE->value,
        RoleName::EMPLOYE->value,
        RoleName::TECHNICIEN->value,
    ],
];
        foreach ($roleHierarchy as $creatorRole => $allowedRoles) {

            if ($creator->hasRole($creatorRole)) {
                return in_array($role->nom, $allowedRoles);
            }
        }

        return false;
    }

    /**
     * Création du premier Super Administrateur d'une entreprise.
     */
    public function createFirstSuperAdmin(
        Entreprise $entreprise,
        array $data,
    ): User {

        $role = Role::findByName(
            RoleName::SUPER_ADMIN
        );

        return $this->createUserAccount([

            'entreprise_id' => $entreprise->id,

            'nom' => $data['nom'],

            'prenom' => $data['prenom'],

            'email' => $data['email'],

            'role_id' => $role->id,

        ]);
    }

    /**
     * Vérifie que l'entreprise est active.
     */
    private function ensureEntrepriseIsActive(
        User $creator
    ): void {

        if (! $creator->entreprise) {
            return;
        }

        if (
            $creator->entreprise->statut !==
            EntrepriseStatus::ACTIVE
        ) {
            throw new AuthorizationException(
                "Votre entreprise doit être configurée avant de pouvoir créer des utilisateurs."
            );
        }
    }

    /**
     * Retourne la permission nécessaire selon le rôle à créer.
     */
    private function getRequiredPermission(
        Role $role
    ): string {

        return match ($role->nom) {

            RoleName::ADMIN->value
                => PermissionName::CREER_ADMIN->value,

            RoleName::SUPER_EMPLOYE->value
                => PermissionName::CREER_SUPER_EMPLOYE->value,

            RoleName::EMPLOYE->value
                => PermissionName::CREER_EMPLOYE->value,

            RoleName::TECHNICIEN->value
                => PermissionName::CREER_TECHNICIEN->value,

            default => throw new AuthorizationException(
                "Rôle non pris en charge."
            ),
        };
    }

    /**
 * Retourne les utilisateurs de l'entreprise.
 */
public function getUsers( array $filters, User $user)
{
    if (
        ! $user->hasRole(RoleName::SUPER_ADMIN->value)
        && ! $user->hasRole(RoleName::ADMIN->value)
    ) {
        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à consulter les utilisateurs."
        );
    }

   $query = User::with('role')
    ->where('entreprise_id', $user->entreprise_id);

    if (!empty($filters['search'])) {

    $query->where(function ($query) use ($filters) {

        $query
            ->where('nom', 'like', "%{$filters['search']}%")
            ->orWhere('prenom', 'like', "%{$filters['search']}%")
            ->orWhere('email', 'like', "%{$filters['search']}%");

    });

}

if (!empty($filters['role_id'])) {

    $query->where(
        'role_id',
        $filters['role_id']
    );

}

if (array_key_exists('actif', $filters)) {

    $query->where(
        'actif',
        $filters['actif']
    );

}

$query->orderBy(
    $filters['sort'] ?? 'created_at',
    $filters['direction'] ?? 'desc'
);

return $query->paginate(
    $filters['per_page'] ?? 20
);
}

/**
 * Retourne un utilisateur.
 */
public function getUser(
    User $connectedUser,
    User $targetUser
): User {

    if (
        ! $connectedUser->hasRole(RoleName::SUPER_ADMIN->value)
        && ! $connectedUser->hasRole(RoleName::ADMIN->value)
    ) {
        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à consulter cet utilisateur."
        );
    }

    if (
        $connectedUser->entreprise_id !==
        $targetUser->entreprise_id
    ) {
        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à consulter cet utilisateur."
        );
    }

    return $targetUser->load('role');
}

/**
 * Modifie un utilisateur.
 */
public function updateUser(
    array $data,
    User $connectedUser,
    User $targetUser
): User {

    $this->validateUpdateRules(
        $connectedUser,
        $targetUser,
        $data
    );

    $this->ensureEmailIsUnique(
    $targetUser,
    $data['email']
);

    return DB::transaction(function () use (
        $data,
        $targetUser
    ) {

        $this->updatePhoto(
            $data,
            $targetUser
        );

        $this->updateUserInformations(
            $data,
            $targetUser
        );

        return $targetUser->fresh()->load('role');
    });
}

/**
 * Vérifie les règles métier avant modification.
 */
private function validateUpdateRules(
    User $connectedUser,
    User $targetUser,
    array $data
): void
{
    $this->ensureCanManageUsers($connectedUser);

    $this->ensureSameEntreprise(
        $connectedUser,
        $targetUser
    );

    $this->ensureTargetCanBeUpdated(
        $connectedUser,
        $targetUser
    );

    $this->ensureRoleCanBeAssigned(
        $connectedUser,
        $targetUser,
        $data['role_id']
    );
}

/**
 * Met à jour la photo de l'utilisateur.
 */
private function updatePhoto(
    array &$data,
    User $targetUser
): void
{
     if (
        ! isset($data['photo'])
        || ! $data['photo'] instanceof UploadedFile
    ) {
        return;
    }

    if ($targetUser->photo) {

        Storage::disk('public')
            ->delete($targetUser->photo);

    }

    $data['photo'] = $data['photo']->store(
        'users',
        'public'
    );
}

/**
 * Met à jour les informations de l'utilisateur.
 */
private function updateUserInformations(
    array $data,
    User $targetUser
): void
{
     $targetUser->update([

        'nom' => $data['nom'],

        'prenom' => $data['prenom'],

        'email' => $data['email'],

        'role_id' => $data['role_id'],

        'photo' => $data['photo'] ?? $targetUser->photo,

    ]);
}

/**
 * Vérifie que l'utilisateur peut gérer les utilisateurs.
 */
private function ensureCanManageUsers(
    User $connectedUser
): void {

    if (
        ! $connectedUser->hasRole(RoleName::SUPER_ADMIN->value)
        && ! $connectedUser->hasRole(RoleName::ADMIN->value)
    ) {
        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à modifier un utilisateur."
        );
    }
}

/**
 * Vérifie que les deux utilisateurs appartiennent
 * à la même entreprise.
 */
private function ensureSameEntreprise(
    User $connectedUser,
    User $targetUser
): void {

    if (
        $connectedUser->entreprise_id !==
        $targetUser->entreprise_id
    ) {
        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à modifier cet utilisateur."
        );
    }
}

/**
 * Vérifie que la cible peut être modifiée.
 */
private function ensureTargetCanBeUpdated(
    User $connectedUser,
    User $targetUser
): void {

    if (
        $connectedUser->hasRole(RoleName::ADMIN->value)
    ) {

        if (
            $targetUser->hasRole(RoleName::ADMIN->value)
            || $targetUser->hasRole(RoleName::SUPER_ADMIN->value)
        ) {

            throw new AuthorizationException(
                "Vous ne pouvez pas modifier cet utilisateur."
            );

        }

    }

}

/**
 * Vérifie que le rôle demandé est autorisé.
 */
/**
 * Vérifie que le rôle demandé est autorisé.
 */
private function ensureRoleCanBeAssigned(
    User $connectedUser,
    User $targetUser,
    int $roleId
): void {

    $role = Role::findOrFail($roleId);

    /*
    |--------------------------------------------------------------------------
    | Cas n°1 : la cible est le Super Administrateur
    |--------------------------------------------------------------------------
    |
    | Son rôle ne peut jamais être modifié.
    | En revanche, il peut modifier son nom, prénom, email ou photo.
    |
    */

    if ($targetUser->hasRole(RoleName::SUPER_ADMIN->value)) {

        if ($role->nom !== RoleName::SUPER_ADMIN->value) {

            throw new AuthorizationException(
                "Le rôle du Super Administrateur ne peut pas être modifié."
            );

        }

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Cas n°2 : personne ne peut promouvoir quelqu'un
    | en Super Administrateur.
    |--------------------------------------------------------------------------
    */

    if ($role->nom === RoleName::SUPER_ADMIN->value) {

        throw new AuthorizationException(
            "Le rôle Super Administrateur ne peut pas être attribué."
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Cas n°3 : vérification de la hiérarchie.
    |--------------------------------------------------------------------------
    */

    if (! $this->canCreateRole($connectedUser, $role)) {

        throw new AuthorizationException(
            "Vous ne pouvez pas attribuer ce rôle."
        );

    }
}

/**
 * Vérifie que l'adresse email est disponible.
 */
private function ensureEmailIsUnique(
    User $targetUser,
    string $email
): void {

    $emailExists = User::query()
        ->where('email', $email)
        ->where('id', '!=', $targetUser->id)
        ->exists();

    if ($emailExists) {

        throw new AuthorizationException(
            "Cette adresse email est déjà utilisée."
        );

    }
}

/**
 * Active ou désactive un utilisateur.
 */
public function updateStatus(
    array $data,
    User $connectedUser,
    User $targetUser
): User {

    $this->validateStatusUpdateRules(
        $connectedUser,
        $targetUser,
        $data['actif']
    );

    $targetUser->update([
        'actif' => $data['actif'],
    ]);

    return $targetUser->fresh()->load('role');
}

/**
 * Vérifie les règles métier avant le changement de statut.
 */
private function validateStatusUpdateRules(
    User $connectedUser,
    User $targetUser,
    bool $newStatus
): void {

    $this->ensureCanManageUsers(
        $connectedUser
    );

    $this->ensureSameEntreprise(
        $connectedUser,
        $targetUser
    );

    $this->ensureTargetStatusCanBeUpdated(
        $connectedUser,
        $targetUser
    );

    $this->ensureStatusIsDifferent(
        $targetUser,
        $newStatus
    );
}

/**
 * Vérifie que la cible peut être activée ou désactivée.
 */
private function ensureTargetStatusCanBeUpdated(
    User $connectedUser,
    User $targetUser
): void {

    if ($connectedUser->id === $targetUser->id) {

        throw new AuthorizationException(
            "Vous ne pouvez pas modifier le statut de votre propre compte."
        );

    }

    if ($connectedUser->hasRole(RoleName::ADMIN->value)) {

        if (
            $targetUser->hasRole(RoleName::ADMIN->value)
            || $targetUser->hasRole(RoleName::SUPER_ADMIN->value)
        ) {

            throw new AuthorizationException(
                "Vous ne pouvez pas modifier le statut de cet utilisateur."
            );

        }

    }

}

/**
 * Vérifie que le statut demandé est différent.
 */
private function ensureStatusIsDifferent(
    User $targetUser,
    bool $newStatus
): void {

    if ($targetUser->actif === $newStatus) {

        throw ValidationException::withMessages([
            'actif' => [
                $newStatus
                    ? 'Le compte est déjà actif.'
                    : 'Le compte est déjà désactivé.',
            ],
        ]);

    }

}

private function validateResendActivationRules(
    User $connectedUser,
    User $targetUser
): void {

    $this->ensureCanManageUsers(
        $connectedUser
    );

    $this->ensureSameEntreprise(
        $connectedUser,
        $targetUser
    );

    $this->ensureAccountIsNotActivated(
        $targetUser
    );
}

/**
 * Vérifie que le compte n'est pas déjà activé.
 */
private function ensureAccountIsNotActivated(
    User $targetUser
): void {

    if (
        $targetUser->actif
        || $targetUser->password_changed
    ) {

        throw new AuthorizationException(
            "Ce compte est déjà activé."
        );

    }
}

/**
 * Renvoie un nouveau lien d'activation.
 */
public function resendActivationLink(
    User $connectedUser,
    User $targetUser
): void
{
    $this->validateResendActivationRules(
        $connectedUser,
        $targetUser
    );

    $this->sendActivationLink(
        $targetUser,
        $connectedUser
    );
}

/**
 * Génère un lien d'activation et l'envoie par e-mail.
 */
private function sendActivationLink(
    User $user,
    ?User $creator = null
): void {

    $token = $this->activationService
        ->generateActivationLink(
            $user,
            $creator
        );

    $activationLink = rtrim(config('app.frontend_url'), '/')
        . '/activate/' . $token;

    $this->mailService
        ->sendActivationMail(
            $user,
            $activationLink
        );
}

}