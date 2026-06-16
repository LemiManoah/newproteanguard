<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'gunId',
    'date',
    'quantity',
    'brought_by',
    'description',
])]
class BulletAddition extends Model
{
    use BelongsToBusiness, HasFactory;

    protected function casts(): array
    {
        return [
            'gunId' => 'integer',
            'date' => 'date',
            'quantity' => 'decimal:2',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function gun(): BelongsTo
    {
        return $this->belongsTo(Gun::class, 'gunId');
    }
}
