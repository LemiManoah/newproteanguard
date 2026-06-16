<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'qty',
])]
class SmsBalance extends Model
{
    use HasFactory;

    protected $attributes = [
        'qty' => 0,
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'businessId' => 'integer',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'businessId');
    }

    public function scopeForBusiness(Builder $query, Business|int $business): Builder
    {
        return $query->where('businessId', $business instanceof Business ? $business->getKey() : $business);
    }
}
