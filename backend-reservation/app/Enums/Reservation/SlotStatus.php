<?php

namespace App\Enums\Reservation;

enum SlotStatus: string
{
    case LIBRE = 'LIBRE';

    case OCCUPE = 'OCCUPE';
}