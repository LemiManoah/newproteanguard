<?php

namespace App\Enums;

enum PayrollCategory: int
{
    case All = 0;
    case Staff = 1;
    case SecurityGuards = 2;

    public function label(): string
    {
        return match ($this) {
            self::All => 'All',
            self::Staff => 'Staff',
            self::SecurityGuards => 'Security Guards',
        };
    }
}
