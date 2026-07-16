<?php

namespace App\Services;

use App\Http\Resources\EquipmentCategoryResource;
use App\Models\EquipmentCategory;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EquipmentCategoryService
{
    /**
 * Crée une catégorie de matériel.
 */
public function createCategory(array $data, User $connectedUser): EquipmentCategory {

    $this->validateCreateRules(
        $connectedUser,
        $data
    );

    return DB::transaction(function () use (
        $data,
        $connectedUser
    ) {

        return $this->createEquipmentCategory(
            $data,
            $connectedUser
        );

    });
}

/**
 * Vérifie les règles métier avant la création d'une catégorie.
 */
private function validateCreateRules(User $connectedUser, array $data): void {

    $this->ensureCanCreateCategory(
        $connectedUser
    );

    $this->ensureCategoryNameIsUnique(
        $connectedUser,
        $data['nom']
    );

}

/**
 * Vérifie les règles métier avant la mise à jour
 * d'une catégorie.
 */
private function validateUpdateRules(User $connectedUser, EquipmentCategory $category, array $data): void {

    $this->ensureCanUpdateCategory(
        $connectedUser
    );

    $this->ensureCategoryBelongsToUser(
        $connectedUser,
        $category
    );

    $this->ensureCategoryNameIsUnique(
        $connectedUser,
        $data['nom'],
        $category->id
    );

}

/**
 * Vérifie les règles métier avant la suppression
 * d'une catégorie.
 */
private function validateDeleteRules(User $connectedUser, EquipmentCategory $category): void {

    $this->ensureCanDeleteCategory(
        $connectedUser
    );

    $this->ensureCategoryBelongsToUser(
        $connectedUser,
        $category
    );

    $this->ensureCategoryCanBeDeleted(
        $category
    );

}

/**
 * Vérifie que l'utilisateur peut créer une catégorie.
 */
private function ensureCanCreateCategory(User $connectedUser): void {

    if (
        ! $connectedUser->hasPermission(
            'creer_categorie_materiel'
        )
    ) {

        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à créer une catégorie de matériel."
        );

    }

}

/**
 * Vérifie que l'utilisateur peut modifier
 * une catégorie.
 */
private function ensureCanUpdateCategory(User $connectedUser): void {

    if (
        ! $connectedUser->hasPermission(
            'modifier_categorie_materiel'
        )
    ) {

        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à modifier une catégorie de matériel."
        );

    }

}

/**
 * Vérifie que l'utilisateur peut supprimer
 * une catégorie.
 */
private function ensureCanDeleteCategory(User $connectedUser): void {

    if (
        ! $connectedUser->hasPermission(
            'supprimer_categorie_materiel'
        )
    ) {

        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à supprimer une catégorie de matériel."
        );

    }

}

/**
 * Vérifie que le nom de la catégorie est unique
 * dans l'entreprise.
 */
private function ensureCategoryNameIsUnique(User $connectedUser, string $nom, int $ignoreCategoryId = null): void {

    $query = EquipmentCategory::query()
        ->where(
            'entreprise_id',
            $connectedUser->entreprise_id
        )
        ->where(
            'nom',
            $nom
        );

    if ($ignoreCategoryId !== null) {

        $query->where(
            'id',
            '!=',
            $ignoreCategoryId
        );

    }

    if ($query->exists()) {

        throw ValidationException::withMessages([
            'nom' => [
                'Une catégorie portant ce nom existe déjà.'
            ],
        ]);

    }

}

/**
 * Crée une catégorie de matériel.
 */
private function createEquipmentCategory(array $data, User $connectedUser): EquipmentCategory {

    return EquipmentCategory::create([

        'entreprise_id' => $connectedUser->entreprise_id,

        'nom' => $data['nom'],

        'description' => $data['description'] ?? null,

    ]);

}

/**
 * Met à jour une catégorie de matériel.
 */
private function updateEquipmentCategory(EquipmentCategory $category, array $data): EquipmentCategory {

    $category->update([

        'nom' => $data['nom'],

        'description' => $data['description'] ?? null,

    ]);

    return $category->fresh();

}

/**
 * Construit la requête de récupération des catégories.
 */
private function buildCategoriesQuery(array $filters, User $connectedUser) {

    $query = EquipmentCategory::query()
        ->where(
            'entreprise_id',
            $connectedUser->entreprise_id
        );

    if (!empty($filters['search'])) {

        $query->where(
            'nom',
            'like',
            "%{$filters['search']}%"
        );

    }

    $query->orderBy('nom');

    return $query;

}

/**
 * Vérifie que la catégorie appartient
 * à l'entreprise de l'utilisateur.
 */
private function ensureCategoryBelongsToUser(User $connectedUser, EquipmentCategory $category): void {

    if (
        $connectedUser->entreprise_id !==
        $category->entreprise_id
    ) {

        throw new ModelNotFoundException(
            'Catégorie introuvable.'
        );

    }

}

/**
 * Retourne les catégories de matériel de l'entreprise.
 */
public function getAllCategories(array $filters, User $connectedUser): LengthAwarePaginator {

    return $this->buildCategoriesQuery(
        $filters,
        $connectedUser
    )->paginate(50);

}

/**
 * Retourne une catégorie de matériel.
 */
public function getCategory(EquipmentCategory $category, User $connectedUser): EquipmentCategory {

    $this->ensureCategoryBelongsToUser(
        $connectedUser,
        $category
    );

    return $category;

}

/**
 * Met à jour une catégorie de matériel.
 */
public function updateCategory(EquipmentCategory $category, array $data, User $connectedUser): EquipmentCategory {

    $this->validateUpdateRules(
        $connectedUser,
        $category,
        $data
    );

    return DB::transaction(function () use (
        $category,
        $data
    ) {

        return $this->updateEquipmentCategory(
            $category,
            $data
        );

    });

}

/**
 * Supprime une catégorie de matériel.
 */
public function deleteCategory(EquipmentCategory $category, User $connectedUser): void {

    $this->validateDeleteRules(
        $connectedUser,
        $category
    );

    DB::transaction(function () use (
        $category
    ) {

        $category->delete();

    });

}

/**
 * Vérifie qu'une catégorie peut être supprimée.
 */
private function ensureCategoryCanBeDeleted(EquipmentCategory $category): void {

    if ($category->equipments()->exists()) {

        throw ValidationException::withMessages([
            'categorie' => [
                "Impossible de supprimer cette catégorie car elle est utilisée par un ou plusieurs matériels."
            ],
        ]);

    }

}


}