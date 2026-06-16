<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'categoryId',
    'code_number',
    'code',
    'related_to',
])]
class ChartOfAccount extends Model
{
    use BelongsToBusiness, HasFactory;

    public const Expenses = 1;

    public const Revenues = 2;

    public const AccountsReceivable = 3;

    public const AccountsPayable = 4;

    public const Cash = 5;

    public const Mobile = 6;

    public const Bank = 7;

    public const Equity = 8;

    public const Drawings = 9;

    protected $attributes = [
        'status' => true,
    ];

    protected function casts(): array
    {
        return [
            'categoryId' => 'integer',
            'code_number' => 'integer',
            'code' => 'integer',
            'related_to' => 'integer',
            'status' => 'boolean',
            'userId' => 'integer',
            'businessId' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CoaCategory::class, 'categoryId');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CoaTransaction::class, 'coa');
    }
}
