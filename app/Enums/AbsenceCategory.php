<?php

namespace App\Enums;

enum AbsenceCategory: int
{
    case Sick = 0;
    case Leave = 1;
    case SpecialDuty = 2;
    case Unknown = 3;

    public function label(): string
    {
        return match ($this) {
            self::Sick => 'Sick',
            self::Leave => 'Leave',
            self::SpecialDuty => 'Special duty',
            self::Unknown => 'Unknown',
        };
    }
}
