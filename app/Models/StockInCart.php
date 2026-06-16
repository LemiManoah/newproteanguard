<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'itemId',
    'quantity',
    'buying_price',
])]
class StockInCart extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'quantity' => 0,
        'buying_price' => 0,
    ];

    protected function casts(): array
    {
        return [
            'itemId' => 'integer',
            'quantity' => 'decimal:2',
            'buying_price' => 'decimal:2',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'itemId');
    }
}
