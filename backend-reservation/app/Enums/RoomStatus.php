<?php

namespace App\Enums;

enum RoomStatus: string
{
    case DISPONIBLE = 'Disponible';
    case OCCUPEE = 'Occupée';
    case MAINTENANCE = 'En maintenance';
    case HORS_SERVICE = 'Hors service';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::DISPONIBLE => 'Disponible',
            self::OCCUPEE => 'Occupée',
            self::MAINTENANCE => 'En maintenance',
            self::HORS_SERVICE => 'Hors service',
        };
    }
}