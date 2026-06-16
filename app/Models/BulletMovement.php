<?php

namespace App\Models;

use App\Enums\BulletMovementType;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'gunId',
    'date',
    'quantity_in',
    'quantity_out',
    'description',
    'type',
    'tid',
])]
class BulletMovement extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'quantity_in' => 0,
        'quantity_out' => 0,
        'type' => 0,
    ];

    protected function casts(): array
    {
        return [
            'gunId' => 'integer',
            'date' => 'date',
            'quantity_in' => 'decimal:2',
            'quantity_out' => 'decimal:2',
            'type' => BulletMovementType::class,
            'tid' => 'integer',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function gun(): BelongsTo
    {
        return $this->belongsTo(Gun::class, 'gunId');
    }
}
