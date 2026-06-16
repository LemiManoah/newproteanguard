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
    'amount',
    'description',
])]
class SalaryDeduction extends Model
{
    use BelongsToBusiness, HasFactory;

    protected function casts(): array
    {
        return [
            'staffId' => 'integer',
            'month' => 'integer',
            'year' => 'integer',
            'amount' => 'decimal:2',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staffId');
    }
}
