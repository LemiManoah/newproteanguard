<?php

namespace App\Enums;

enum MaritalStatus: int
{
    case Single = 0;
    case Married = 1;

    public function label(): string
    {
        return match ($this) {
            self::Single => 'Single',
            self::Married => 'Married',
        };
    }
}
