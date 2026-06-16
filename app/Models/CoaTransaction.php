<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'date',
    'coa',
    'clientId',
    'staffId',
    'ref_no',
    'dr_amount',
    'cr_amount',
    'description',
    'type',
    'txnId',
])]
class CoaTransaction extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'dr_amount' => 0,
        'cr_amount' => 0,
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'coa' => 'integer',
            'clientId' => 'integer',
            'staffId' => 'integer',
            'dr_amount' => 'decimal:2',
            'cr_amount' => 'decimal:2',
            'txnId' => 'integer',
            'userId' => 'integer',
            'businessId' => 'integer',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa');
    }
}
