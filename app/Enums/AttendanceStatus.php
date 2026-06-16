<?php

namespace App\Enums;

enum AttendanceStatus: int
{
    case Absent = 0;
    case Present = 1;
    case Replaced = 2;

    public function label(): string
    {
        return match ($this) {
            self::Absent => 'Absent',
            self::Present => 'Present',
            self::Replaced => 'Replaced',
        };
    }
}
