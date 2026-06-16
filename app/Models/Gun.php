<?php

namespace App\Models;

use App\Enums\AvailabilityStatus;
use App\Enums\GunOwnerType;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'type',
    'serial_number',
    'mark_number',
    'bullets',
    'owner',
    'available',
    'vendor_name',
    'vendor_contact',
])]
class Gun extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'owner' => 0,
        'available' => 1,
        'status' => true,
    ];

    protected function casts(): array
    {
        return [
            'bullets' => 'integer',
            'owner' => GunOwnerType::class,
            'available' => AvailabilityStatus::class,
            'status' => 'boolean',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(GunAssignment::class, 'gunId');
    }

    public function bulletMovements(): HasMany
    {
        return $this->hasMany(BulletMovement::class, 'gunId');
    }
}
