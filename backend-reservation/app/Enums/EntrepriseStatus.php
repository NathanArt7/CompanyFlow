<?php

namespace App\Enums;

enum EntrepriseStatus: string
{
    case EN_ATTENTE = 'En attente';

    case ACTIVE = 'Active';

    case SUSPENDUE = 'Suspendue';

    public function isActive(): bool
{
    return $this === self::ACTIVE;
}

public function isPending(): bool
{
    return $this === self::EN_ATTENTE;
}

public function isSuspended(): bool
{
    return $this === self::SUSPENDUE;
}
}


