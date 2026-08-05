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
use App\Enums\Equipment\EquipmentUsageType;
use App\Enums\Equipment\EmpruntableEquipmentStatus;
use App\Enums\Equipment\NonEmpruntableEquipmentStatus;
use App\Enums\ActivityLog\ActivityModule;
use App\Notifications\EquipmentStateChangedNotification;

class EquipmentService
{
    public function __construct(
        private ActivityLogService $activityLogService,
        private NotificationService $notificationService
    ) {
    }

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

        $equipment = $this->createEquipmentRecord(
            $data,
            $connectedUser
        );

        $this->activityLogService->log(
            $connectedUser,
            ActivityModule::EQUIPEMENT,
            'equipment.created',
            "A ajouté le matériel {$equipment->nom}.",
            $equipment
        );

        return $equipment;

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

    if (!empty($filters['category_id'])) {

        $query->where(
            'category_id',
            $filters['category_id']
        );

    }

    if (!empty($filters['usage_type'])) {

        $query->where(
            'usage_type',
            $filters['usage_type']
        );

    }

    if (!empty($filters['etat'])) {

        $query->where(
            'etat',
            $filters['etat']
        );

    }

    return $query
        ->orderBy(
            $filters['sort'] ?? 'nom',
            $filters['direction'] ?? 'asc'
        )
        ->paginate(
            $filters['per_page'] ?? 20
        );

}

/**
 * Retourne les statistiques des matériels.
 */
public function getStats(
    User $connectedUser
): array {

    $entrepriseId = $connectedUser->entreprise_id;

    $total = Equipment::where(
        'entreprise_id',
        $entrepriseId
    )->count();

    $borrowable = Equipment::where(
        'entreprise_id',
        $entrepriseId
    )
        ->where('usage_type', EquipmentUsageType::EMPRUNTABLE)
        ->count();

    $nonBorrowable = Equipment::where(
        'entreprise_id',
        $entrepriseId
    )
        ->where('usage_type', EquipmentUsageType::NON_EMPRUNTABLE)
        ->count();

    $borrowableOccupied = Equipment::where(
        'entreprise_id',
        $entrepriseId
    )
        ->where('usage_type', EquipmentUsageType::EMPRUNTABLE)
        ->where('etat', 'OCCUPE')
        ->count();

    $broken = Equipment::where(
        'entreprise_id',
        $entrepriseId
    )
        ->where('etat', 'EN_PANNE')
        ->count();

    $inMaintenance = Equipment::where(
        'entreprise_id',
        $entrepriseId
    )
        ->where('etat', 'EN_MAINTENANCE')
        ->count();

    return [

        'total' => $total,

        'borrowable' => $borrowable,

        'non_borrowable' => $nonBorrowable,

        'borrowable_occupied' => $borrowableOccupied,

        'broken' => $broken,

        'in_maintenance' => $inMaintenance,

    ];

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
        $data,
        $connectedUser
    ) {

        $before = [
            'le nom' => $equipment->nom,
            'le code' => $equipment->code,
            'la marque' => $equipment->marque,
            'le modèle' => $equipment->modele,
            'le numéro de série' => $equipment->numero_serie ?? '—',
            'la catégorie' => $equipment->category?->nom ?? '—',
            "le type d'utilisation" => $this->equipmentUsageTypeLabel($equipment->usage_type->value),
            'l\'état' => $this->equipmentStateLabel($equipment->etat),
            'la salle de stockage' => $equipment->storageRoom?->nom ?? '—',
            'la localisation' => $equipment->localisation ?? '—',
            'l\'utilisateur affecté' => $equipment->assignedUser ? "{$equipment->assignedUser->prenom} {$equipment->assignedUser->nom}" : '—',
        ];

        $equipment->fill($data);

        $equipment->save();

        $equipment = $equipment->load([
            'category',
            'storageRoom',
            'assignedUser',
        ]);

        $after = [
            'le nom' => $equipment->nom,
            'le code' => $equipment->code,
            'la marque' => $equipment->marque,
            'le modèle' => $equipment->modele,
            'le numéro de série' => $equipment->numero_serie ?? '—',
            'la catégorie' => $equipment->category?->nom ?? '—',
            "le type d'utilisation" => $this->equipmentUsageTypeLabel($equipment->usage_type->value),
            'l\'état' => $this->equipmentStateLabel($equipment->etat),
            'la salle de stockage' => $equipment->storageRoom?->nom ?? '—',
            'la localisation' => $equipment->localisation ?? '—',
            'l\'utilisateur affecté' => $equipment->assignedUser ? "{$equipment->assignedUser->prenom} {$equipment->assignedUser->nom}" : '—',
        ];

        $changes = $this->activityLogService->describeChanges($before, $after);

        $this->activityLogService->log(
            $connectedUser,
            ActivityModule::EQUIPEMENT,
            'equipment.updated',
            $changes
                ? "A modifié le matériel {$equipment->nom} : " . implode(', ', $changes) . '.'
                : "A modifié le matériel {$equipment->nom}.",
            $equipment
        );

        if ($before["l'état"] !== $after["l'état"]) {

            $this->notificationService->notifyMany(
                $this->notificationService->technicienRecipients(
                    $connectedUser->entreprise_id,
                    $connectedUser->id
                ),
                'systeme',
                new EquipmentStateChangedNotification(
                    $equipment,
                    $before["l'état"],
                    $after["l'état"]
                )
            );

        }

        return $equipment;

    });

}

/**
 * Retourne le libellé affiché pour un type d'utilisation de matériel.
 */
private function equipmentUsageTypeLabel(string $usageType): string
{
    return $usageType === EquipmentUsageType::EMPRUNTABLE->value
        ? 'Empruntable'
        : 'Non empruntable';
}

/**
 * Retourne le libellé affiché pour un état de matériel (empruntable ou non).
 */
private function equipmentStateLabel(string $etat): string
{
    return match ($etat) {
        'DISPONIBLE' => 'Disponible',
        'FONCTIONNEL' => 'Fonctionnel',
        'OCCUPE' => 'Occupé',
        'EN_MAINTENANCE' => 'En maintenance',
        'EN_PANNE' => 'En panne',
        'HORS_SERVICE' => 'Hors service',
        default => $etat,
    };
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
 * Met à jour uniquement l'état d'un matériel (sans passer par la
 * modification complète, gated par "modifier_materiel") : réservé aux
 * profils qui n'ont que la permission "modifier_etat_materiel" (ex.
 * Technicien fermant un ticket).
 */
public function updateEquipmentStatus(
    Equipment $equipment,
    string $newState,
    User $connectedUser
): Equipment {

    $equipment = $this->baseQuery(
        $connectedUser
    )
    ->whereKey($equipment->id)
    ->firstOrFail();

    $this->ensureCanUpdateEquipmentStatus(
        $connectedUser
    );

    $this->ensureEquipmentStateIsValid(
        $equipment,
        $newState
    );

    $previousState = $equipment->etat;

    $equipment->update([
        'etat' => $newState,
    ]);

    $equipment = $equipment->load([
        'category',
        'storageRoom',
        'assignedUser',
    ]);

    $this->activityLogService->log(
        $connectedUser,
        ActivityModule::EQUIPEMENT,
        'equipment.status_updated',
        "A changé l'état du matériel {$equipment->nom} de « {$this->equipmentStateLabel($previousState)} » à « {$this->equipmentStateLabel($newState)} ».",
        $equipment
    );

    if ($previousState !== $newState) {

        $this->notificationService->notifyMany(
            $this->notificationService->technicienRecipients(
                $connectedUser->entreprise_id,
                $connectedUser->id
            ),
            'systeme',
            new EquipmentStateChangedNotification(
                $equipment,
                $this->equipmentStateLabel($previousState),
                $this->equipmentStateLabel($newState)
            )
        );

    }

    return $equipment;

}

/**
 * Vérifie que l'utilisateur peut modifier
 * l'état d'un matériel.
 */
private function ensureCanUpdateEquipmentStatus(
    User $connectedUser
): void {

    if (
        ! $connectedUser->hasPermission(
            'modifier_etat_materiel'
        )
    ) {

        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à modifier l'état de ce matériel."
        );

    }

}

/**
 * Vérifie que l'état demandé est valide pour le type
 * d'utilisation du matériel.
 */
private function ensureEquipmentStateIsValid(
    Equipment $equipment,
    string $newState
): void {

    $allowedStates = $equipment->usage_type === EquipmentUsageType::EMPRUNTABLE
        ? EmpruntableEquipmentStatus::values()
        : NonEmpruntableEquipmentStatus::values();

    if (! in_array($newState, $allowedStates, true)) {

        throw ValidationException::withMessages([
            'etat' => [
                "L'état sélectionné est invalide pour ce matériel."
            ],
        ]);

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

    $nom = $equipment->nom;

    $equipment->delete();

    $this->activityLogService->log(
        $connectedUser,
        ActivityModule::EQUIPEMENT,
        'equipment.deleted',
        "A supprimé le matériel {$nom}.",
        $equipment
    );

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