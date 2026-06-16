<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'contact1',
    'contact2',
    'email',
    'location',
    'website',
    'logo',
    'tin',
    'guard_duty',
    'guard_overtime',
    'savings',
    'payroll_start',
    'payroll_end',
    'currency',
])]
class Business extends Model
{
    use HasFactory;

    protected $attributes = [
        'status' => true,
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'guard_duty' => 'integer',
            'guard_overtime' => 'integer',
            'savings' => 'integer',
            'payroll_start' => 'integer',
            'payroll_end' => 'integer',
            'currency' => 'integer',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'businessId');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'businessId');
    }

    public function paymentModes(): HasMany
    {
        return $this->hasMany(PaymentMode::class, 'businessId');
    }
}
