<?php

namespace App\Models;

use App\Enums\PaymentModeType;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $name
 * @property string|null $account
 * @property bool $status
 * @property bool $is_default
 * @property int|null $businessId
 * @property int|null $userId
 */
#[Fillable([
    'name',
    'type',
    'type_name',
    'account',
    'opening_balance',
    'is_default',
])]
class PaymentMode extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'status' => true,
        'is_default' => false,
    ];

    protected function casts(): array
    {
        return [
            'type' => PaymentModeType::class,
            'opening_balance' => 'decimal:2',
            'status' => 'boolean',
            'is_default' => 'boolean',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }
}
