<?php

namespace App\Enums\Equipment;

enum EquipmentUsageType: string
{
    case EMPRUNTABLE = 'EMPRUNTABLE';
    case NON_EMPRUNTABLE = 'NON_EMPRUNTABLE';

      /**
     * Retourne toutes les valeurs de l'énumération.
     */
    public static function values(): array
    {
        return array_column(
            self::cases(),
            'value'
        );
    }

    /**
 * Indique si le matériel
 * peut être réservé.
 */
public function isBorrowable(): bool
{
    return $this === self::EMPRUNTABLE;
}

/**
 * Indique si le matériel
 * est fixe.
 */
public function isFixed(): bool
{
    return $this === self::NON_EMPRUNTABLE;
}
}