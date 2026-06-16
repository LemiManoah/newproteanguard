<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'date',
    'itemId',
    'guardId',
    'quantity',
    'description',
])]
class InventoryStockUsage extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'quantity' => 0,
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'itemId' => 'integer',
            'guardId' => 'integer',
            'quantity' => 'decimal:2',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'itemId');
    }

    public function guard(): BelongsTo
    {
        return $this->belongsTo(SecurityGuard::class, 'guardId');
    }
}
