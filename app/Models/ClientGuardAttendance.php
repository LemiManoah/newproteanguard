<?php

namespace App\Models;

use App\Enums\AbsenceCategory;
use App\Enums\AttendanceStatus;
use App\Enums\ScheduleType;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $deploymentId
 * @property int|null $clientId
 * @property int|null $guardId
 * @property int|null $replacedBy
 * @property Carbon|null $date
 * @property ScheduleType $schedule_type
 * @property bool $over_time
 * @property AttendanceStatus $attended
 * @property AbsenceCategory|null $absentCategory
 * @property string|null $reason
 * @property int|null $userId
 * @property int $businessId
 */
#[Fillable([
    'deploymentId',
    'clientId',
    'guardId',
    'replacedBy',
    'date',
    'schedule_type',
    'over_time',
    'attended',
    'absentCategory',
    'reason',
    'userId',
    'businessId',
])]
class ClientGuardAttendance extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'schedule_type' => 2,
        'over_time' => false,
        'attended' => 1,
    ];

    protected function casts(): array
    {
        return [
            'deploymentId' => 'integer',
            'clientId' => 'integer',
            'guardId' => 'integer',
            'replacedBy' => 'integer',
            'date' => 'date',
            'schedule_type' => ScheduleType::class,
            'over_time' => 'boolean',
            'attended' => AttendanceStatus::class,
            'absentCategory' => AbsenceCategory::class,
            'userId' => 'integer',
            'businessId' => 'integer',
        ];
    }

    public function deployment(): BelongsTo
    {
        return $this->belongsTo(ClientGuard::class, 'deploymentId');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'clientId');
    }

    public function securityGuard(): BelongsTo
    {
        return $this->belongsTo(SecurityGuard::class, 'guardId');
    }

    public function replaced(): BelongsTo
    {
        return $this->belongsTo(SecurityGuard::class, 'replacedBy');
    }
}
