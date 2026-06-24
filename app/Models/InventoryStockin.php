<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'date',
    'total',
    'paid',
    'paymentMode',
    'supplierId',
])]
class InventoryStockin extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'total' => 0,
        'paid' => 0,
        'supplierId' => 0,
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total' => 'decimal:2',
            'paid' => 'decimal:2',
            'paymentMode' => 'integer',
            'supplierId' => 'integer',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class, 'paymentMode');
    }

    public function mode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class, 'paymentMode');
    }

    public function details(): HasMany
    {
        return $this->hasMany(StockInDetail::class, 'stockInId');
    }
}
