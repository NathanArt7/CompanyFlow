<?php

namespace App\Enums;

enum RoleName: string
{
    case SUPER_ADMIN = 'Super_Administrateur';

    case ADMIN = 'Administrateur';

    case SUPER_EMPLOYE = 'Super_Employe';

    case EMPLOYE = 'Employe';

    case TECHNICIEN = 'Technicien';
}