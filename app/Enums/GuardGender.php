<?php

namespace App\Enums;

enum GuardGender: int
{
    case Male = 0;
    case Female = 1;

    public function label(): string
    {
        return match ($this) {
            self::Male => 'Male',
            self::Female => 'Female',
        };
    }
}
