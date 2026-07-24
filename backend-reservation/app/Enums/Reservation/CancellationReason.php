<?php

namespace App\Enums\Reservation;

enum CancellationReason: string
{
    /**
     * Annulée par le créateur.
     */
    case USER_REQUEST = 'USER_REQUEST';

    /**
     * Annulée par un administrateur.
     */
    case ADMIN_DECISION = 'ADMIN_DECISION';

    /**
     * La salle a été supprimée.
     */
    case ROOM_DELETED = 'ROOM_DELETED';

    /**
     * Le réservataire a été supprimé.
     */
    case USER_DELETED = 'USER_DELETED';

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

            self::USER_REQUEST =>
                "Annulée par le créateur",

            self::ADMIN_DECISION =>
                "Décision d'un administrateur",

            self::ROOM_DELETED =>
                "Salle supprimée",

            self::USER_DELETED =>
                "Utilisateur supprimé",

        };
    }
}