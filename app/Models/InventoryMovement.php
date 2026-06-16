<?php

namespace App\Models;

use App\Enums\InventoryMovementType;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'date',
    'itemId',
    'quantity_in',
    'quantity_out',
    'description',
    'type',
    'tid',
])]
class InventoryMovement extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'quantity_in' => 0,
        'quantity_out' => 0,
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'itemId' => 'integer',
            'quantity_in' => 'decimal:2',
            'quantity_out' => 'decimal:2',
            'type' => InventoryMovementType::class,
            'tid' => 'integer',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'itemId');
    }
}
