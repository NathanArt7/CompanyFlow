<?php

namespace App\Enums\Equipment;

enum EmpruntableEquipmentStatus: string
{
    case DISPONIBLE = 'DISPONIBLE';

    case EN_MAINTENANCE = 'EN_MAINTENANCE';

    case HORS_SERVICE = 'HORS_SERVICE';

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
 * est disponible.
 */
public function isAvailable(): bool
{
    return $this === self::DISPONIBLE;
}
}