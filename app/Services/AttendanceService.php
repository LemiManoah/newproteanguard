<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\ClientGuard;
use App\Models\ClientGuardAttendance;

class AttendanceService
{
    public function __construct(protected AttendanceHelperService $helper) {}

    public function checkAttendance(string $date, int $businessId, int $userId): void
    {
        $deployments = ClientGuard::query()
            ->whereDate('from', '<=', $date)
            ->where('status', true)
            ->where('businessId', $businessId)
            ->get();

        foreach ($deployments as $deployment) {
            // Check if a record for the same date already exists
            $existingRecord = ClientGuardAttendance::query()
                ->where([
                    'businessId' => $businessId,
                    'guardId' => $deployment->guardId,
                    'date' => $date,
                    'schedule_type' => $deployment->schedule_type->value,
                    'over_time' => $deployment->over_time,
                ])
                ->first();

            if (! $existingRecord) {
                // Insert initial attendance record
                ClientGuardAttendance::create([
                    'deploymentId' => $deployment->id,
                    'clientId' => $deployment->clientId,
                    'guardId' => $deployment->guardId,
                    'schedule_type' => $deployment->schedule_type->value,
                    'over_time' => $deployment->over_time,
                    'date' => $date,
                    'attended' => AttendanceStatus::Present->value,
                    'userId' => $userId,
                    'businessId' => $businessId,
                ]);
            }
        }
    }

    public function checkAttendanceRange(string $from, string $to, int $businessId, int $userId): void
    {
        foreach ($this->helper->datesBetween($from, $to) as $date) {
            $this->checkAttendance($date, $businessId, $userId);
        }
    }
}
