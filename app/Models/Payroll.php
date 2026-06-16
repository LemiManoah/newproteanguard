<?php

namespace App\Models;

use App\Enums\PayrollCategory;
use App\Enums\PayrollStatus;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category',
    'month',
    'year',
    'approval_date',
    'review_date',
    'review_comment',
    'approval_comment',
    'guard_overtime',
    'savings',
])]
class Payroll extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'category' => 0,
        'status' => 0,
        'guard_overtime' => 0,
        'savings' => 0,
    ];

    protected function casts(): array
    {
        return [
            'category' => PayrollCategory::class,
            'month' => 'integer',
            'year' => 'integer',
            'approval_date' => 'date',
            'review_date' => 'date',
            'status' => PayrollStatus::class,
            'guard_overtime' => 'decimal:2',
            'savings' => 'decimal:2',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function salaryPayments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class, 'payrollId');
    }
}
