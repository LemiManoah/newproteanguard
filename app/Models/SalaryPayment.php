<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'staffId',
    'payrollId',
    'month',
    'year',
    'salary',
    'overtime_amount',
    'savings',
    'days_worked',
    'overtime_worked',
])]
class SalaryPayment extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'salary' => 0,
        'overtime_amount' => 0,
        'savings' => 0,
        'days_worked' => 0,
        'overtime_worked' => 0,
    ];

    protected function casts(): array
    {
        return [
            'staffId' => 'integer',
            'payrollId' => 'integer',
            'month' => 'integer',
            'year' => 'integer',
            'salary' => 'decimal:2',
            'overtime_amount' => 'decimal:2',
            'savings' => 'decimal:2',
            'days_worked' => 'decimal:2',
            'overtime_worked' => 'decimal:2',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staffId');
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class, 'payrollId');
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(SalaryPaymentLedger::class, 'salaryId');
    }
}
