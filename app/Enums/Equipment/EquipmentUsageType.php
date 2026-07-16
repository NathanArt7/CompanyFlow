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
}