<?php

namespace App\Enums;

enum InventoryMovementType: int
{
    case Opening = 0;
    case Stocking = 1;
    case Usage = 2;

    public function label(): string
    {
        return match ($this) {
            self::Opening => 'Opening',
            self::Stocking => 'Stocking',
            self::Usage => 'Usage',
        };
    }
}
