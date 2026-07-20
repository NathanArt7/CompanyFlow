<?php

namespace App\Enums\Reservation;
use Carbon\Carbon;

enum DayOfWeek: string
{
    case MONDAY = 'MONDAY';
    case TUESDAY = 'TUESDAY';
    case WEDNESDAY = 'WEDNESDAY';
    case THURSDAY = 'THURSDAY';
    case FRIDAY = 'FRIDAY';
    case SATURDAY = 'SATURDAY';
    case SUNDAY = 'SUNDAY';

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

            self::MONDAY => 'Lundi',
            self::TUESDAY => 'Mardi',
            self::WEDNESDAY => 'Mercredi',
            self::THURSDAY => 'Jeudi',
            self::FRIDAY => 'Vendredi',
            self::SATURDAY => 'Samedi',
            self::SUNDAY => 'Dimanche',

        };
    }

    /**
 * Retourne le jour de la semaine
 * correspondant à une date.
 */
public static function fromCarbon(
    Carbon $date
): self {

    return match ($date->dayOfWeek) {

        Carbon::MONDAY => self::MONDAY,

        Carbon::TUESDAY => self::TUESDAY,

        Carbon::WEDNESDAY => self::WEDNESDAY,

        Carbon::THURSDAY => self::THURSDAY,

        Carbon::FRIDAY => self::FRIDAY,

        Carbon::SATURDAY => self::SATURDAY,

        Carbon::SUNDAY => self::SUNDAY,

    };

}
}