<?php

namespace App\Enums;

enum CoaCategoryName: string
{
    case Cash = 'Cash';
    case Bank = 'Bank';
    case PhysicalAssets = 'Physical Assets';
    case AccountsReceivable = 'Accounts Receivable';
    case AccountsPayable = 'Accounts Payable';
    case ClientPayments = 'Client Payments';
    case OtherRevenue = 'Other Revenue';
    case OwnersEquity = 'Owners Equity';
    case Drawings = 'Drawings';

    public function label(): string
    {
        return $this->value;
    }
}
