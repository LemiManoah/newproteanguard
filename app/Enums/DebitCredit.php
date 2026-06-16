<?php

namespace App\Enums;

enum DebitCredit: string
{
    case Debit = 'dr';
    case Credit = 'cr';

    public function label(): string
    {
        return match ($this) {
            self::Debit => 'Debit',
            self::Credit => 'Credit',
        };
    }
}
