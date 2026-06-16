<?php

namespace App\Models;

use App\Enums\GuardGender;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'email',
    'gender',
    'contact1',
    'contact2',
    'nin',
    'address',
    'dob',
    'positionId',
    'guardId',
    'salaryCategoryId',
    'salary',
    'dop',
])]
class Staff extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $table = 'staff';

    protected $attributes = [
        'status' => true,
    ];

    protected function casts(): array
    {
        return [
            'gender' => GuardGender::class,
            'dob' => 'date',
            'positionId' => 'integer',
            'guardId' => 'integer',
            'salaryCategoryId' => 'integer',
            'salary' => 'decimal:2',
            'dop' => 'date',
            'status' => 'boolean',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(StaffPosition::class, 'positionId');
    }

    public function guard(): BelongsTo
    {
        return $this->belongsTo(SecurityGuard::class, 'guardId');
    }

    public function salaryCategory(): BelongsTo
    {
        return $this->belongsTo(SalaryCategory::class, 'salaryCategoryId');
    }

    public function salaryPayments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class, 'staffId');
    }
}
