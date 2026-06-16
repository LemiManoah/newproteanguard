<?php

namespace App\Enums;

enum PaymentModeType: string
{
    case Cash = 'Cash';
    case Bank = 'Bank';
    case Mobile = 'Mobile';

    public function label(): string
    {
        return $this->value;
    }
}
