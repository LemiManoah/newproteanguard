<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'amount',
    'date',
    'modeId',
    'description',
    'categoryId',
    'yearId',
])]
class Expense extends Model
{
    use BelongsToBusiness, HasFactory;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
            'modeId' => 'integer',
            'categoryId' => 'integer',
            'yearId' => 'integer',
            'userId' => 'integer',
            'businessId' => 'integer',
        ];
    }

    public function mode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class, 'modeId');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'categoryId');
    }

    public function financialYear(): BelongsTo
    {
        return $this->belongsTo(FinancialYear::class, 'yearId');
    }
}
