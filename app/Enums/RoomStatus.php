<?php

namespace App\Enums;

enum RoomStatus: string
{
    case DISPONIBLE = 'Disponible';
    case OCCUPEE = 'Occupée';
    case MAINTENANCE = 'En maintenance';
    case HORS_SERVICE = 'Hors service';
}