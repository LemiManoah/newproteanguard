<?php

namespace App\Livewire\Operations;

use App\Enums\AbsenceCategory;
use App\Enums\AttendanceStatus;
use App\Enums\ScheduleType;
use App\Models\ClientGuard;
use App\Models\ClientGuardAttendance;
use App\Services\AuditService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Client Attendance')]
class AttendancePage extends Component
{
    public ?int $deploymentId = null;

    public ?string $date = null;

    public int $attended = 1;

    public ?int $absentCategory = null;

    public ?string $reason = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    protected AuditService $audit;

    public function boot(TenantContext $tenant, PermissionService $permissions, AuditService $audit): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
        $this->audit = $audit;
    }

    public function mount(): void
    {
        $this->date = now()->toDateString();
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'deploymentId' => ['required', 'integer'],
            'date' => ['required', 'date'],
            'attended' => ['required', 'integer', Rule::in(array_column(AttendanceStatus::cases(), 'value'))],
            'absentCategory' => ['nullable', 'integer', Rule::in(array_column(AbsenceCategory::cases(), 'value'))],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function record(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_attendance'), Response::HTTP_FORBIDDEN);

        $validated = $this->validate();

        $deployment = ClientGuard::query()
            ->where('businessId', $this->tenant->businessId())
            ->where('status', true)
            ->findOrFail($validated['deploymentId']);

        $scheduleType = $deployment->schedule_type;
        $scheduleTypeValue = $scheduleType instanceof ScheduleType ? $scheduleType->value : (int) $scheduleType;

        $attendance = ClientGuardAttendance::query()
            ->firstOrNew([
                'businessId' => $this->tenant->businessId(),
                'deploymentId' => $deployment->getKey(),
                'date' => $validated['date'],
            ]);

        $attendance->forceFill([
            'clientId' => $deployment->clientId,
            'guardId' => $deployment->guardId,
            'schedule_type' => $scheduleTypeValue,
            'attended' => $validated['attended'],
            'absentCategory' => $validated['attended'] === AttendanceStatus::Absent->value ? $validated['absentCategory'] : null,
            'reason' => $validated['reason'],
            'userId' => $this->tenant->user()->getKey(),
        ]);
        $attendance->save();

        $this->audit->record("Recorded guard attendance for deployment {$deployment->getKey()}", $this->tenant->user());
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_attendance'), Response::HTTP_FORBIDDEN);

        return view('livewire.operations.attendance-page', [
            'deployments' => ClientGuard::query()
                ->with(['client', 'securityGuard'])
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->latest()
                ->get(),
            'attendanceRows' => ClientGuardAttendance::query()
                ->with(['client', 'securityGuard'])
                ->where('businessId', $this->tenant->businessId())
                ->latest('date')
                ->limit(25)
                ->get(),
            'attendanceStatuses' => AttendanceStatus::cases(),
            'absenceCategories' => AbsenceCategory::cases(),
        ]);
    }
}
