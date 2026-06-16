<?php

namespace App\Enums;

enum GunOwnerType: int
{
    case Owned = 0;
    case Hired = 1;

    public function label(): string
    {
        return match ($this) {
            self::Owned => 'Owned',
            self::Hired => 'Hired',
        };
    }
}
