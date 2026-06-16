<?php

namespace App\Enums;

enum LifeStatus: int
{
    case Alive = 0;
    case Deceased = 1;

    public function label(): string
    {
        return match ($this) {
            self::Alive => 'Alive',
            self::Deceased => 'Deceased',
        };
    }
}
