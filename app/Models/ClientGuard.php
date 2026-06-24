<?php

namespace App\Models;

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
 * @property int $clientId
 * @property int $guardId
 * @property Carbon|null $from
 * @property Carbon|null $to
 * @property bool $status
 * @property ScheduleType $schedule_type
 * @property bool $over_time
 * @property int|null $userId
 * @property int $businessId
 */
#[Fillable([
    'clientId',
    'guardId',
    'from',
    'to',
    'schedule_type',
    'over_time',
    'userId',
    'businessId',
])]
class ClientGuard extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'status' => true,
        'schedule_type' => 2,
        'over_time' => false,
    ];

    protected function casts(): array
    {
        return [
            'clientId' => 'integer',
            'guardId' => 'integer',
            'from' => 'date',
            'to' => 'date',
            'status' => 'boolean',
            'schedule_type' => ScheduleType::class,
            'over_time' => 'boolean',
            'userId' => 'integer',
            'businessId' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'clientId');
    }

    public function securityGuard(): BelongsTo
    {
        return $this->belongsTo(SecurityGuard::class, 'guardId');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(ClientGuardAttendance::class, 'deploymentId');
    }

    public function getNameAttribute(): string
    {
        $clientName = $this->client?->name ?? 'Unknown Client';
        $guardName = $this->securityGuard
            ? trim(($this->securityGuard->fname ?? '').' '.($this->securityGuard->lname ?? '')).' ('.($this->securityGuard->code ?? '').')'
            : 'Unknown Guard';

        return "{$clientName} · {$guardName}";
    }
}
