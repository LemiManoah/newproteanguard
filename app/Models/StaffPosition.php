<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
])]
class StaffPosition extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'status' => true,
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class, 'positionId');
    }
}
