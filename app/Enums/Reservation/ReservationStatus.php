<?php

namespace App\Enums\Reservation;

enum ReservationStatus: string
{
    case CONFIRMEE = 'CONFIRMEE';

    case ANNULEE = 'ANNULEE';

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

            self::CONFIRMEE => 'Confirmée',

            self::ANNULEE => 'Annulée',

        };
    }

    public function isConfirmed(): bool
{
    return $this === self::CONFIRMEE;
}

public function isCancelled(): bool
{
    return $this === self::ANNULEE;
}

public function isFinished(): bool
{
    return $this === self::TERMINEE;
}
}