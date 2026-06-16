<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'staffId',
    'month',
    'year',
    'mma',
    'rent',
    'uniform',
    'payee',
    'nssf',
])]
class StandardCharge extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'mma' => 0,
        'rent' => 0,
        'uniform' => 0,
        'payee' => 0,
        'nssf' => 0,
    ];

    protected function casts(): array
    {
        return [
            'staffId' => 'integer',
            'month' => 'integer',
            'year' => 'integer',
            'mma' => 'decimal:2',
            'rent' => 'decimal:2',
            'uniform' => 'decimal:2',
            'payee' => 'decimal:2',
            'nssf' => 'decimal:2',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staffId');
    }
}
