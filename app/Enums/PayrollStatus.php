<?php

namespace App\Enums;

enum PayrollStatus: int
{
    case Pending = 0;
    case Reviewed = 1;
    case Approved = 2;
    case Rejected = 3;

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Reviewed => 'Reviewed',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }
}
