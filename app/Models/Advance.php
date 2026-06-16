<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'staffId',
    'amount',
    'date',
    'deductMonth',
    'deductYear',
    'description',
    'mode',
])]
class Advance extends Model
{
    use BelongsToBusiness, HasFactory;

    protected function casts(): array
    {
        return [
            'staffId' => 'integer',
            'amount' => 'decimal:2',
            'date' => 'date',
            'deductMonth' => 'integer',
            'deductYear' => 'integer',
            'mode' => 'integer',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staffId');
    }

    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class, 'mode');
    }
}
