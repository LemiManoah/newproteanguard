<?php

namespace App\Enums;

enum AvailabilityStatus: int
{
    case Unavailable = 0;
    case Available = 1;

    public function label(): string
    {
        return match ($this) {
            self::Unavailable => 'Unavailable',
            self::Available => 'Available',
        };
    }
}
