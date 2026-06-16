<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'categoryId',
    'unitId',
    'name',
    'quantity',
    'opening_stock',
    'buying_price',
    'last_buying_price',
])]
class InventoryItem extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'quantity' => 0,
        'opening_stock' => 0,
        'buying_price' => 0,
        'last_buying_price' => 0,
        'status' => true,
    ];

    protected function casts(): array
    {
        return [
            'categoryId' => 'integer',
            'unitId' => 'integer',
            'quantity' => 'decimal:2',
            'opening_stock' => 'decimal:2',
            'buying_price' => 'decimal:2',
            'last_buying_price' => 'decimal:2',
            'status' => 'boolean',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'categoryId');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unitId');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'itemId');
    }
}
