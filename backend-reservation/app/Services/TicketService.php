<?php

namespace App\Services;

use App\Enums\Equipment\EmpruntableEquipmentStatus;
use App\Enums\Equipment\EquipmentUsageType;
use App\Enums\Equipment\NonEmpruntableEquipmentStatus;
use App\Enums\RoleName;
use App\Enums\Ticket\TicketStatus;
use App\Models\Equipment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Enums\ActivityLog\ActivityModule;
use App\Notifications\TicketAcceptedNotification;
use App\Notifications\TicketClosedNotification;
use App\Notifications\TicketCreatedNotification;

class TicketService
{
    public function __construct(
        private ActivityLogService $activityLogService,
        private NotificationService $notificationService
    ) {
    }

/**
 * Crée un ticket.
 */
public function createTicket(
    array $data,
    User $connectedUser
): Ticket {

    $equipment = Equipment::query()
        ->where(
            'entreprise_id',
            $connectedUser->entreprise_id
        )
        ->findOrFail(
            $data['equipment_id']
        );

    $this->validateCreateRules(
        $connectedUser,
        $equipment
    );

    return DB::transaction(function () use (
        $data,
        $equipment,
        $connectedUser
    ) {

        $ticket = Ticket::create([

            'entreprise_id' => $connectedUser->entreprise_id,

            'equipment_id' => $equipment->id,

            'user_id' => $connectedUser->id,

            'description' => $data['description'],

            'statut' => TicketStatus::OUVERT,

        ]);

        $this->markEquipmentAsBroken(
            $equipment
        );

        $ticket = $this->loadTicket(
            $ticket
        );

        $this->activityLogService->log(
            $connectedUser,
            ActivityModule::TICKET,
            'ticket.created',
            "A créé un ticket pour le matériel {$equipment->nom}.",
            $ticket
        );

        $this->notificationService->notifyMany(
            $this->notificationService->technicienRecipients(
                $connectedUser->entreprise_id,
                $connectedUser->id
            ),
            'systeme',
            new TicketCreatedNotification($ticket)
        );

        return $ticket;

    });

}

/**
 * Vérifie les règles métier avant la création d'un ticket.
 */
private function validateCreateRules(
    User $connectedUser,
    Equipment $equipment
): void {

    $this->ensureCanCreateTicket(
        $connectedUser
    );

    $this->ensureEquipmentIsEligibleForTicket(
        $equipment,
        $connectedUser
    );

    $this->ensureEquipmentHasNoOpenTicket(
        $equipment
    );

}

/**
 * Vérifie qu'aucun ticket ouvert ou en cours n'existe déjà sur ce matériel :
 * un second ticket ne peut être créé qu'une fois le précédent résolu/fermé.
 */
private function ensureEquipmentHasNoOpenTicket(
    Equipment $equipment
): void {

    $hasOpenTicket = Ticket::query()
        ->where(
            'equipment_id',
            $equipment->id
        )
        ->whereIn(
            'statut',
            TicketStatus::remainingValues()
        )
        ->exists();

    if ($hasOpenTicket) {

        throw ValidationException::withMessages([
            'equipment_id' => [
                "Un ticket est déjà ouvert ou en cours pour ce matériel."
            ],
        ]);

    }

}

/**
 * Vérifie que l'utilisateur peut créer un ticket.
 */
private function ensureCanCreateTicket(
    User $connectedUser
): void {

    if (
        ! $connectedUser->hasPermission(
            'creer_ticket'
        )
    ) {

        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à créer un ticket."
        );

    }

}

/**
 * Vérifie que le matériel concerné peut faire l'objet
 * d'un ticket créé par cet utilisateur :
 *
 * - Super Administrateur / Administrateur : tout matériel.
 * - Super Employé : tout matériel empruntable, ainsi que le matériel
 *   non empruntable qui lui est assigné ou qui n'est assigné à personne.
 * - Employé : uniquement le matériel non empruntable qui lui est
 *   assigné ou qui n'est assigné à personne.
 */
private function ensureEquipmentIsEligibleForTicket(
    Equipment $equipment,
    User $connectedUser
): void {

    if (
        $connectedUser->hasRole(RoleName::SUPER_ADMIN->value)
        || $connectedUser->hasRole(RoleName::ADMIN->value)
    ) {
        return;
    }

    if (
        $connectedUser->hasRole(RoleName::SUPER_EMPLOYE->value)
        && $equipment->usage_type === EquipmentUsageType::EMPRUNTABLE
    ) {
        return;
    }

    if (
        $equipment->usage_type === EquipmentUsageType::NON_EMPRUNTABLE
        && (
            $equipment->assigned_to === null
            || $equipment->assigned_to === $connectedUser->id
        )
    ) {
        return;
    }

    throw new AuthorizationException(
        "Vous n'êtes pas autorisé à créer un ticket sur ce matériel."
    );

}

/**
 * Passe le matériel concerné en panne.
 */
private function markEquipmentAsBroken(
    Equipment $equipment
): void {

    $equipment->update([
        'etat' => EmpruntableEquipmentStatus::EN_PANNE->value,
    ]);

}

/**
 * Prend en charge un ticket : passe son statut à "En cours"
 * et le matériel concerné "En maintenance".
 */
public function acceptTicket(
    Ticket $ticket,
    User $connectedUser
): Ticket {

    $ticket = $this->baseQuery(
        $connectedUser
    )
    ->whereKey(
        $ticket->id
    )
    ->firstOrFail();

    $this->ensureCanAcceptTicket(
        $connectedUser
    );

    DB::transaction(function () use (
        $ticket,
        $connectedUser
    ) {

        $ticket->update([
            'statut' => TicketStatus::EN_COURS,
        ]);

        $ticket->equipment->update([
            'etat' => EmpruntableEquipmentStatus::EN_MAINTENANCE->value,
        ]);

        $this->activityLogService->log(
            $connectedUser,
            ActivityModule::TICKET,
            'ticket.accepted',
            "A pris en charge le ticket #{$ticket->id} ({$ticket->equipment->nom}).",
            $ticket
        );

        if ($ticket->user) {

            $this->notificationService->notify(
                $ticket->user,
                'systeme',
                new TicketAcceptedNotification($ticket)
            );

        }

    });

    return $this->loadTicket(
        $ticket->fresh()
    );

}

/**
 * Vérifie que l'utilisateur peut prendre en charge un ticket.
 */
private function ensureCanAcceptTicket(
    User $connectedUser
): void {

    if (
        ! $connectedUser->hasPermission(
            'accepter_ticket'
        )
    ) {

        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à prendre en charge ce ticket."
        );

    }

}

/**
 * Ferme un ticket et applique l'état choisi par le technicien
 * au matériel concerné (sauf s'il reste d'autres tickets ouverts
 * ou en cours sur ce même matériel).
 */
public function closeTicket(
    Ticket $ticket,
    string $equipmentState,
    User $connectedUser
): Ticket {

    $ticket = $this->baseQuery(
        $connectedUser
    )
    ->whereKey(
        $ticket->id
    )
    ->firstOrFail();

    $this->ensureCanCloseTicket(
        $connectedUser
    );

    DB::transaction(function () use (
        $ticket,
        $equipmentState,
        $connectedUser
    ) {

        $ticket->update([
            'statut' => TicketStatus::FERME,
        ]);

        $this->applyEquipmentStateAfterClose(
            $ticket->equipment,
            $equipmentState
        );

        $this->activityLogService->log(
            $connectedUser,
            ActivityModule::TICKET,
            'ticket.closed',
            "A fermé le ticket #{$ticket->id} (matériel : {$ticket->equipment->nom}).",
            $ticket
        );

        if ($ticket->user) {

            $this->notificationService->notify(
                $ticket->user,
                'systeme',
                new TicketClosedNotification(
                    $ticket,
                    $this->equipmentStateLabel($ticket->equipment->fresh()->etat)
                )
            );

        }

    });

    return $this->loadTicket(
        $ticket->fresh()
    );

}

/**
 * Vérifie que l'utilisateur peut fermer un ticket.
 */
private function ensureCanCloseTicket(
    User $connectedUser
): void {

    if (
        ! $connectedUser->hasPermission(
            'cloturer_ticket'
        )
    ) {

        throw new AuthorizationException(
            "Vous n'êtes pas autorisé à fermer ce ticket."
        );

    }

}

/**
 * Applique au matériel l'état choisi par le technicien à la
 * fermeture du ticket, sauf s'il reste d'autres tickets ouverts
 * ou en cours sur ce même matériel.
 */
private function applyEquipmentStateAfterClose(
    Equipment $equipment,
    string $equipmentState
): void {

    $hasRemainingTickets = Ticket::query()
        ->where(
            'equipment_id',
            $equipment->id
        )
        ->whereIn(
            'statut',
            TicketStatus::remainingValues()
        )
        ->exists();

    if ($hasRemainingTickets) {
        return;
    }

    $isEmpruntable = $equipment->usage_type === EquipmentUsageType::EMPRUNTABLE;

    if ($equipmentState === 'HORS_SERVICE') {

        $equipment->update([

            'etat' => $isEmpruntable
                ? EmpruntableEquipmentStatus::HORS_SERVICE->value
                : NonEmpruntableEquipmentStatus::HORS_SERVICE->value,

        ]);

        return;

    }

    $equipment->update([

        'etat' => $isEmpruntable
            ? EmpruntableEquipmentStatus::DISPONIBLE->value
            : NonEmpruntableEquipmentStatus::FONCTIONNEL->value,

    ]);

}

/**
 * Retourne la liste des tickets visibles par l'utilisateur :
 * tous les tickets de l'entreprise s'il a la permission
 * "consulter_tous_tickets", uniquement les siens sinon.
 */
public function getTickets(
    array $filters,
    User $connectedUser
): LengthAwarePaginator {

    $query = $this->baseQuery(
        $connectedUser
    );

    if (! empty($filters['statut'])) {

        $query->where(
            'statut',
            $filters['statut']
        );

    }

    if (! empty($filters['user_id'])) {

        $query->where(
            'user_id',
            $filters['user_id']
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

/**
 * Retourne les statistiques de tickets visibles par l'utilisateur.
 */
public function getStats(
    User $connectedUser
): array {

    $query = $this->baseQuery(
        $connectedUser
    );

    $totalThisMonth = (clone $query)
        ->whereYear(
            'created_at',
            now()->year
        )
        ->whereMonth(
            'created_at',
            now()->month
        )
        ->count();

    $enCours = (clone $query)
        ->where(
            'statut',
            TicketStatus::EN_COURS
        )
        ->count();

    $resolus = (clone $query)
        ->whereIn(
            'statut',
            TicketStatus::resolvedValues()
        )
        ->count();

    $restants = (clone $query)
        ->whereIn(
            'statut',
            TicketStatus::remainingValues()
        )
        ->count();

    return [

        'total_ce_mois' => $totalThisMonth,

        'en_cours' => $enCours,

        'resolus' => $resolus,

        'restants' => $restants,

    ];

}

/**
 * Requête de base des tickets, scopée à l'entreprise et,
 * si l'utilisateur n'a pas la permission de tout consulter,
 * à ses propres tickets.
 */
private function baseQuery(
    User $connectedUser
): Builder {

    $query = Ticket::query()

        ->with([
            'equipment.category',
            'equipment.storageRoom',
            'user',
        ])

        ->where(
            'entreprise_id',
            $connectedUser->entreprise_id
        );

    if (
        ! $connectedUser->hasPermission(
            'consulter_tous_tickets'
        )
    ) {

        if (
            ! $connectedUser->hasPermission(
                'consulter_ses_tickets'
            )
        ) {

            throw new AuthorizationException(
                "Vous n'êtes pas autorisé à consulter les tickets."
            );

        }

        $query->where(
            'user_id',
            $connectedUser->id
        );

    }

    return $query;

}

/**
 * Charge les relations d'un ticket.
 */
private function loadTicket(
    Ticket $ticket
): Ticket {

    return $ticket->load([
        'equipment.category',
        'equipment.storageRoom',
        'user',
    ]);

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

}
