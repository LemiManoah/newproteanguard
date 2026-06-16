<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'invoice',
    'invoice_number',
    'clientId',
    'bill_cycle',
    'cycles',
    'amount',
    'total',
    'start_date',
    'date',
    'end_date',
])]
class ClientBill extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'status' => true,
    ];

    protected function casts(): array
    {
        return [
            'invoice' => 'integer',
            'invoice_number' => 'integer',
            'clientId' => 'integer',
            'bill_cycle' => 'integer',
            'cycles' => 'decimal:2',
            'amount' => 'decimal:2',
            'total' => 'decimal:2',
            'start_date' => 'date',
            'date' => 'date',
            'end_date' => 'date',
            'status' => 'boolean',
            'userId' => 'integer',
            'businessId' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'clientId');
    }
}
