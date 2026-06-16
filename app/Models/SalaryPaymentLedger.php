<?php

namespace App\Models;

use App\Enums\SalaryPaymentChannel;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'staffId',
    'amount',
    'date',
    'mode',
    'salaryId',
    'channel',
])]
class SalaryPaymentLedger extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'channel' => 0,
    ];

    protected function casts(): array
    {
        return [
            'staffId' => 'integer',
            'amount' => 'decimal:2',
            'date' => 'date',
            'mode' => 'integer',
            'salaryId' => 'integer',
            'channel' => SalaryPaymentChannel::class,
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staffId');
    }

    public function mode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class, 'mode');
    }

    public function salary(): BelongsTo
    {
        return $this->belongsTo(SalaryPayment::class, 'salaryId');
    }
}
