<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'receiver',
    'message_type',
    'message',
    'size',
    'message_id',
])]
class SentMessage extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'sender' => 'integer',
            'size' => 'integer',
            'businessId' => 'integer',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'businessId');
    }

    public function senderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender');
    }

    public function scopeForBusiness(Builder $query, Business|int $business): Builder
    {
        return $query->where('businessId', $business instanceof Business ? $business->getKey() : $business);
    }
}
