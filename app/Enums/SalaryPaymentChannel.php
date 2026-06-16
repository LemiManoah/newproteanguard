<?php

namespace App\Enums;

enum SalaryPaymentChannel: int
{
    case Default = 0;

    public function label(): string
    {
        return match ($this) {
            self::Default => 'Default',
        };
    }
}
