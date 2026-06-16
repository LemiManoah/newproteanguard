<?php

namespace App\Enums;

enum BillingCycle: int
{
    case Monthly = 0;
    case Quarterly = 1;
    case Annual = 2;
    case OneTime = 3;

    public function label(): string
    {
        return match ($this) {
            self::Monthly => 'Monthly',
            self::Quarterly => 'Quarterly',
            self::Annual => 'Annual',
            self::OneTime => 'One time',
        };
    }
}
