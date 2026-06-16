<?php

namespace App\Enums;

enum CoaCategoryType: string
{
    case Drawings = 'Drawings';
    case Expenses = 'Expenses';
    case Assets = 'Assets';
    case Liability = 'Liability';
    case Equity = 'Equity';
    case Revenues = 'Revenues';

    public function label(): string
    {
        return $this->value;
    }
}
