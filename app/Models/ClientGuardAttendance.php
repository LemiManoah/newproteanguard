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

#[Fillable([
    'deploymentId',
    'clientId',
    'guardId',
    'replacedBy',
    'date',
    'schedule_type',
    'attended',
    'absentCategory',
    'reason',
])]
class ClientGuardAttendance extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'schedule_type' => 2,
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
