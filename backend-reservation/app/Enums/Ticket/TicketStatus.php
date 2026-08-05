<?php

namespace App\Enums\Ticket;

enum TicketStatus: string
{
    case OUVERT = 'OUVERT';

    case EN_COURS = 'EN_COURS';

    case RESOLU = 'RESOLU';

    case FERME = 'FERME';

    /**
     * Retourne toutes les valeurs de l'enum.
     */
    public static function values(): array
    {
        return array_column(
            self::cases(),
            'value'
        );
    }

    /**
     * Retourne le libellé affiché à l'utilisateur.
     */
    public function label(): string
    {
        return match ($this) {

            self::OUVERT => 'Ouvert',

            self::EN_COURS => 'En cours',

            self::RESOLU => 'Résolu',

            self::FERME => 'Fermé',

        };
    }

    /**
     * Statuts considérés comme "restants" (pas encore résolus).
     */
    public static function remainingValues(): array
    {
        return [
            self::OUVERT->value,
            self::EN_COURS->value,
        ];
    }

    /**
     * Statuts considérés comme "résolus".
     */
    public static function resolvedValues(): array
    {
        return [
            self::RESOLU->value,
            self::FERME->value,
        ];
    }
}
