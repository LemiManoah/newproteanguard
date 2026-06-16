<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'staffId',
    'balance',
    'amount',
    'percent',
    'salaryId',
])]
class TempSalary extends Model
{
    use BelongsToBusiness, HasFactory;

    protected function casts(): array
    {
        return [
            'staffId' => 'integer',
            'balance' => 'decimal:2',
            'amount' => 'decimal:2',
            'percent' => 'decimal:2',
            'salaryId' => 'integer',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staffId');
    }

    public function salary(): BelongsTo
    {
        return $this->belongsTo(SalaryPayment::class, 'salaryId');
    }
}
