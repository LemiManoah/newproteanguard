<?php

namespace App\Enums;

enum ScheduleType: int
{
    case Day = 0;
    case Night = 1;
    case FullTime = 2;

    public function label(): string
    {
        return match ($this) {
            self::Day => 'Day',
            self::Night => 'Night',
            self::FullTime => 'Full time',
        };
    }
}
