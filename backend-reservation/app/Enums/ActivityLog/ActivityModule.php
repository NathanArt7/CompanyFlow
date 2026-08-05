<?php

namespace App\Enums\ActivityLog;

enum ActivityModule: string
{
    case RESERVATION = 'RESERVATION';

    case SALLE = 'SALLE';

    case EQUIPEMENT = 'EQUIPEMENT';

    case UTILISATEUR = 'UTILISATEUR';

    case TICKET = 'TICKET';

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
}
