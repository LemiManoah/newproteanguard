<?php

namespace App\Models\Concerns;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Model
 */
trait BelongsToBusiness
{
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'businessId');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function scopeForBusiness(Builder $query, Business|int $business): Builder
    {
        return $query->where('businessId', $business instanceof Business ? $business->getKey() : $business);
    }
}
