<?php

namespace App\Models;

use App\Enums\BillingCycle;
use App\Enums\ScheduleType;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $categoryId
 * @property string|null $name
 * @property string|null $contact1
 * @property string|null $contact2
 * @property string|null $email
 * @property string|null $id_no
 * @property string|null $tin
 * @property string|null $vat_no
 * @property string|null $address
 * @property BillingCycle $billing_cycle
 * @property numeric-string|float|int $amount
 * @property numeric-string|float|int $no_guards
 * @property numeric-string|float|int|null $actual_guards
 * @property Carbon|null $bill_start
 * @property ScheduleType $schedule_type
 * @property bool $status
 * @property bool $assigned
 * @property Carbon|null $left_date
 * @property string|null $left_reason
 * @property int|null $userId
 * @property int $businessId
 */
#[Fillable([
    'categoryId',
    'name',
    'contact1',
    'contact2',
    'email',
    'id_no',
    'tin',
    'vat_no',
    'address',
    'billing_cycle',
    'amount',
    'no_guards',
    'actual_guards',
    'bill_start',
    'schedule_type',
    'left_date',
    'left_reason',
])]
class Client extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'billing_cycle' => 0,
        'amount' => 0,
        'no_guards' => 1,
        'schedule_type' => 2,
        'status' => true,
        'assigned' => false,
    ];

    protected function casts(): array
    {
        return [
            'billing_cycle' => BillingCycle::class,
            'amount' => 'decimal:2',
            'no_guards' => 'decimal:2',
            'actual_guards' => 'decimal:2',
            'bill_start' => 'date',
            'schedule_type' => ScheduleType::class,
            'status' => 'boolean',
            'assigned' => 'boolean',
            'left_date' => 'date',
            'categoryId' => 'integer',
            'userId' => 'integer',
            'businessId' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClientCategory::class, 'categoryId');
    }

    public function guards(): HasMany
    {
        return $this->hasMany(ClientGuard::class, 'clientId');
    }

    public function activeGuards(): HasMany
    {
        return $this->guards()->where('status', true);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(ClientGuardAttendance::class, 'clientId');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClientDocument::class, 'clientId');
    }

    public function activeDocuments(): HasMany
    {
        return $this->documents()->where('status', true);
    }
}
