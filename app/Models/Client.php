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
