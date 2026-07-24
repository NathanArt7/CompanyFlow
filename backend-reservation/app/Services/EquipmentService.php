<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Enums\RoomType;

class EquipmentService
{
/**
 * Crée un matériel.
 */
public function createEquipment(
    array $data,
    User $connectedUser
): Equipment {

    $this->validateCreateRules(
        $connectedUser,
        $data
    );

    return DB::transaction(function () use (
        $data,
        $connectedUser
    ) {

        return $this->createEquipmentRecord(
            $data,
            $connectedUser
        );

    });

}

/**
 * Vérifie les règles métier avant la création.
 */
private function validateCreateRules(
    User $connectedUser,
    array $data
): void {

    $this->ensureCanCreateEquipment(
        $connectedUser
    );

    $this->ensureEquipmentCodeIsUnique(
        $connectedUser,
        $data['code']
    );

    $this->ensureCategoryBelongsToEntreprise(
    $connectedUser,
    $data['category_id']
);

   $this->ensureStorageRoomIsValid(
    $connectedUser,
    $data
);

    $this->ensureAssignedUserBelongsToEntreprise(
        $connectedUser,
        $data
    );

}

/**
 * Vérifie que l'utilisateur peut créer un matériel.
 */
private function ensureCanCreateEquipment(
    User $connectedUser
): void {

    if (
        ! $connectedUser->hasPermission(
            'creer_materiel'
        )
    ) {

        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à créer un matériel."
        );

    }

}

/**
 * Vérifie que le code du matériel est unique
 * dans l'entreprise.
 */
private function ensureEquipmentCodeIsUnique(
    User $connectedUser,
    string $code,
    ?int $ignoreEquipmentId = null
): void {

    $query = Equipment::query()
        ->where(
            'entreprise_id',
            $connectedUser->entreprise_id
        )
        ->where(
            'code',
            $code
        );

    if ($ignoreEquipmentId !== null) {

        $query->where(
            'id',
            '!=',
            $ignoreEquipmentId
        );

    }

    if ($query->exists()) {

        throw ValidationException::withMessages([
            'code' => [
                'Un matériel portant ce code existe déjà.'
            ],
        ]);

    }

}

/**
 * Vérifie que la catégorie appartient
 * à l'entreprise de l'utilisateur.
 */
private function ensureCategoryBelongsToEntreprise(
    User $connectedUser,
    int $categoryId
): void {

    $category = EquipmentCategory::findOrFail(
        $categoryId
    );

    if (
        $category->entreprise_id !==
        $connectedUser->entreprise_id
    ) {

        throw ValidationException::withMessages([
            'category_id' => [
                "La catégorie sélectionnée n'appartient pas à votre entreprise."
            ],
        ]);

    }

}



/**
 * Vérifie que l'utilisateur affecté appartient
 * à l'entreprise de l'utilisateur connecté.
 */
private function ensureAssignedUserBelongsToEntreprise(
    User $connectedUser,
    array $data
): void {

    if (empty($data['assigned_to'])) {
        return;
    }

    $user = User::findOrFail(
        $data['assigned_to']
    );

    if (
        $user->entreprise_id !==
        $connectedUser->entreprise_id
    ) {

        throw ValidationException::withMessages([
            'assigned_to' => [
                "L'utilisateur sélectionné n'appartient pas à votre entreprise."
            ],
        ]);

    }

}

/**
 * Vérifie que la salle de stockage est valide.
 */
private function ensureStorageRoomIsValid(
    User $connectedUser,
    array $data
): void {

    if (empty($data['storage_room_id'])) {
        return;
    }

    $room = Room::findOrFail(
        $data['storage_room_id']
    );

    if (
        $room->entreprise_id !==
        $connectedUser->entreprise_id
    ) {

        throw ValidationException::withMessages([
            'storage_room_id' => [
                "La salle sélectionnée n'appartient pas à votre entreprise."
            ],
        ]);

    }

    if (
        $room->type !==
        RoomType::STORAGE
    ) {

        throw ValidationException::withMessages([
            'storage_room_id' => [
                "La salle sélectionnée n'est pas une salle de stockage."
            ],
        ]);

    }

}

/**
 * Enregistre un nouveau matériel.
 */
private function createEquipmentRecord(
    array $data,
    User $connectedUser
): Equipment {

    $equipment = new Equipment(
        $data
    );

    $equipment->entreprise_id =
        $connectedUser->entreprise_id;

    $equipment->save();

    return $equipment->load([
        'category',
        'storageRoom',
        'assignedUser',
    ]);

}

/**
 * Requête de base des matériels.
 */
private function baseQuery(
    User $connectedUser
): Builder {

    return Equipment::query()

        ->with([
            'category',
            'storageRoom',
            'assignedUser',
        ])

        ->where(
            'entreprise_id',
            $connectedUser->entreprise_id
        );

}

/**
 * Liste les matériels.
 */
public function getAllEquipments(
    array $filters,
    User $connectedUser
): LengthAwarePaginator {

    $query = $this->baseQuery(
        $connectedUser
    );

    if (!empty($filters['search'])) {

        $query->where(function (Builder $query) use ($filters) {

            $query
                ->where(
                    'nom',
                    'like',
                    "%{$filters['search']}%"
                )
                ->orWhere(
                    'code',
                    'like',
                    "%{$filters['search']}%"
                );

        });

    }

    return $query
        ->orderBy('nom')
        ->paginate(20);

}

/**
 * Retourne un matériel.
 */
public function getEquipment(
    Equipment $equipment,
    User $connectedUser
): Equipment {

    return $this->baseQuery(
    $connectedUser
    )
    ->whereKey($equipment->id)
    ->firstOrFail();

}

/**
 * Met à jour un matériel.
 */
public function updateEquipment(
    Equipment $equipment,
    array $data,
    User $connectedUser
): Equipment {

    $equipment = $this->baseQuery(
        $connectedUser
    )
    ->whereKey($equipment->id)
    ->firstOrFail();

    $this->validateUpdateRules(
        $equipment,
        $connectedUser,
        $data
    );

    return DB::transaction(function () use (
        $equipment,
        $data
    ) {

        $equipment->fill($data);

        $equipment->save();

        return $equipment->load([
            'category',
            'storageRoom',
            'assignedUser',
        ]);

    });

}

/**
 * Vérifie les règles métier avant la mise à jour.
 */
private function validateUpdateRules(
    Equipment $equipment,
    User $connectedUser,
    array $data
): void {

    $this->ensureCanUpdateEquipment(
    $connectedUser
    );

    $this->ensureEquipmentCodeIsUnique(
        $connectedUser,
        $data['code'],
        $equipment->id
    );

    $this->ensureCategoryBelongsToEntreprise(
        $connectedUser,
        $data['category_id']
    );

    $this->ensureStorageRoomIsValid(
        $connectedUser,
        $data
    );

    $this->ensureAssignedUserBelongsToEntreprise(
        $connectedUser,
        $data
    );

}

private function ensureCanUpdateEquipment(
    User $connectedUser
): void {

    if (
        ! $connectedUser->hasPermission(
            'modifier_materiel'
        )
    ) {

        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à modifier un matériel."
        );

    }

}

/**
 * Supprime un matériel.
 */
public function deleteEquipment(
    Equipment $equipment,
    User $connectedUser
): void {

    $equipment = $this->baseQuery(
        $connectedUser
    )
    ->whereKey($equipment->id)
    ->firstOrFail();

    $this->ensureCanDeleteEquipment(
        $connectedUser
    );

    $equipment->delete();

}

/**
 * Vérifie que l'utilisateur peut supprimer
 * un matériel.
 */
private function ensureCanDeleteEquipment(
    User $connectedUser
): void {

    if (
        ! $connectedUser->hasPermission(
            'supprimer_materiel'
        )
    ) {

        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à supprimer un matériel."
        );

    }

}

}