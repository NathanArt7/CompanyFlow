<?php

namespace App\Services;

use App\Enums\ActivityLog\ActivityModule;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    /**
     * Enregistre une action dans le journal d'activité de l'entreprise de l'acteur.
     */
    public function log(
        User $actor,
        ActivityModule $module,
        string $action,
        string $description,
        ?Model $subject = null,
        array $metadata = []
    ): void {

        ActivityLog::create([

            'entreprise_id' => $actor->entreprise_id,

            'user_id' => $actor->id,

            'module' => $module->value,

            'action' => $action,

            'description' => $description,

            'subject_type' => $subject
                ? $subject->getMorphClass()
                : null,

            'subject_id' => $subject?->id,

            'metadata' => $metadata,

        ]);

    }

    /**
     * Compare deux instantanés "libellé => valeur affichable" pris avant/après une mise
     * à jour et retourne un fragment de phrase par champ effectivement modifié, du type
     * "le statut de « En maintenance » à « Disponible »". Les valeurs doivent déjà être
     * formatées pour l'affichage (libellés d'enum résolus, pas les valeurs brutes).
     */
    public function describeChanges(array $before, array $after): array
    {
        $fragments = [];

        foreach ($after as $label => $newValue) {

            if (! array_key_exists($label, $before)) {
                continue;
            }

            $oldValue = $before[$label];

            if ((string) $oldValue === (string) $newValue) {
                continue;
            }

            $fragments[] = "{$label} de « {$oldValue} » à « {$newValue} »";

        }

        return $fragments;

    }

    /**
     * Retourne les journaux d'activité visibles par l'utilisateur, filtrés par module.
     */
    public function getLogs(
        array $filters,
        User $connectedUser
    ): LengthAwarePaginator {

        $query = ActivityLog::query()
            ->with('user')
            ->where(
                'entreprise_id',
                $connectedUser->entreprise_id
            )
            ->where(
                'module',
                $filters['module']
            );

        if (! empty($filters['search'])) {

            // Recherche par nom et prénom de l'auteur de l'action : chaque mot saisi doit
            // matcher le nom OU le prénom, pour couvrir "Nathan LABARBORE" comme
            // "LABARBORE Nathan" quel que soit l'ordre.
            $terms = preg_split(
                '/\s+/',
                trim($filters['search']),
                -1,
                PREG_SPLIT_NO_EMPTY
            );

            $query->whereHas('user', function ($userQuery) use ($terms) {

                foreach ($terms as $term) {

                    $userQuery->where(function ($q) use ($term) {

                        $q->where('nom', 'like', '%' . $term . '%')
                            ->orWhere('prenom', 'like', '%' . $term . '%');

                    });

                }

            });

        }

        if (! empty($filters['from_date'])) {

            $query->whereDate(
                'created_at',
                '>=',
                $filters['from_date']
            );

        }

        if (! empty($filters['to_date'])) {

            $query->whereDate(
                'created_at',
                '<=',
                $filters['to_date']
            );

        }

        return $query
            ->orderBy(
                'created_at',
                'desc'
            )
            ->paginate(
                $filters['per_page'] ?? 20
            );

    }
}
