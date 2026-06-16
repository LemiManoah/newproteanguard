<?php

namespace App\Enums;

enum BulletMovementType: int
{
    case Opening = 0;
    case Addition = 1;
    case Usage = 2;

    public function label(): string
    {
        return match ($this) {
            self::Opening => 'Opening',
            self::Addition => 'Addition',
            self::Usage => 'Usage',
        };
    }
}
