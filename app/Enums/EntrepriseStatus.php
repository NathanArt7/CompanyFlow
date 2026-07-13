<?php

namespace App\Enums;

enum EntrepriseStatus: string
{
    case EN_ATTENTE = 'En attente';

    case ACTIVE = 'Active';

    case SUSPENDUE = 'Suspendue';
}
