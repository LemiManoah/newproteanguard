<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'receipt',
    'receipt_number',
    'clientId',
    'modeId',
    'amount',
    'payment_date',
    'ref_number',
    'description',
])]
class ClientPayment extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'status' => true,
    ];

    protected function casts(): array
    {
        return [
            'receipt' => 'integer',
            'receipt_number' => 'integer',
            'clientId' => 'integer',
            'modeId' => 'integer',
            'amount' => 'decimal:2',
            'payment_date' => 'date',
            'status' => 'boolean',
            'userId' => 'integer',
            'businessId' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'clientId');
    }

    public function mode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class, 'modeId');
    }
}
