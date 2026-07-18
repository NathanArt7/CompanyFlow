<?php

namespace App\Enums;

enum RoomType: string
{
    case MEETING = 'MEETING';
    case STORAGE = 'STORAGE';

    /**
     * Retourne toutes les valeurs de l'enum.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Retourne le libellé affiché à l'utilisateur.
     */
    public function label(): string
    {
        return match ($this) {
            self::MEETING => 'Salle de réunion',
            self::STORAGE => 'Salle de stockage',
        };
    }

    public function isMeetingRoom(): bool
{
    return $this === self::MEETING;
}

public function isStorageRoom(): bool
{
    return $this === self::STORAGE;
}
}