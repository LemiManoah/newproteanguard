<?php

namespace App\Livewire\Operations;

use App\Enums\AbsenceCategory;
use App\Enums\AttendanceStatus;
use App\Enums\ScheduleType;
use App\Models\Client;
use App\Models\ClientGuard;
use App\Models\ClientGuardAttendance;
use App\Models\SecurityGuard;
use App\Services\AttendanceHelperService;
use App\Services\AttendanceService;
use App\Services\AuditService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Client Attendance')]
class AttendancePage extends Component
{
    use WithPagination;

    public ?int $deploymentId = null;

    public ?string $date = null;

    public int $attended = 1;

    public ?int $absentCategory = null;

    public ?string $reason = null;

    public ?string $search = null;

    public ?int $clientId = null;

    public ?int $status = null;

    public ?int $scheduleType = null;

    public ?string $duty = null;

    public string $dateRange = 'today';

    public ?string $from = null;

    public ?string $to = null;

    public string $sortBy = 'date';

    public string $sortDirection = 'desc';

    public bool $showRecordModal = false;

    public ?int $editingAttendanceId = null;

    public ?string $recordGuardName = null;

    public ?string $recordClientName = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    protected AuditService $audit;

    protected AttendanceService $attendanceService;

    protected AttendanceHelperService $attendanceHelper;

    public function boot(
        TenantContext $tenant,
        PermissionService $permissions,
        AuditService $audit,
        AttendanceService $attendanceService,
        AttendanceHelperService $attendanceHelper
    ): void {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
        $this->audit = $audit;
        $this->attendanceService = $attendanceService;
        $this->attendanceHelper = $attendanceHelper;
    }

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->from = now()->toDateString();
        $this->to = now()->toDateString();
        $this->generateAttendance();
    }

    public function updatedDateRange(string $value): void
    {
        [$this->from, $this->to] = $this->attendanceHelper->resolveDateRange($value, $this->from, $this->to);
        $this->date = $this->from;
        $this->resetPage();
        $this->generateAttendance();
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'clientId', 'status', 'scheduleType', 'duty', 'from', 'to'], true)) {
            $this->dateRange = in_array($property, ['from', 'to'], true) ? 'custom' : $this->dateRange;
            $this->resetPage();

            if (in_array($property, ['from', 'to'], true)) {
                $this->generateAttendance();
            }
        }
    }

    protected function generateAttendance(): void
    {
        [$from, $to] = $this->attendanceHelper->resolveDateRange($this->dateRange, $this->from, $this->to);

        if ($this->permissions->can($this->tenant->user(), 'manage_attendance') && $from && $to) {
            $this->attendanceService->checkAttendanceRange(
                $from,
                $to,
                $this->tenant->businessId(),
                $this->tenant->user()->getKey()
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'deploymentId' => [$this->editingAttendanceId ? 'nullable' : 'required', 'integer'],
            'date' => ['required', 'date'],
            'attended' => ['required', 'integer', Rule::in(array_column(AttendanceStatus::cases(), 'value'))],
            'absentCategory' => [
                Rule::requiredIf((int) $this->attended === AttendanceStatus::Absent->value),
                'nullable',
                'integer',
                Rule::in(array_column(AbsenceCategory::cases(), 'value')),
            ],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortBy = $column;
        $this->sortDirection = 'asc';
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'clientId', 'status', 'scheduleType', 'duty']);
        $this->dateRange = 'today';
        $this->from = now()->toDateString();
        $this->to = now()->toDateString();
        $this->date = $this->from;
        $this->resetPage();
        $this->generateAttendance();
    }

    public function create(): void
    {
        $this->resetRecordForm();
        $this->date = $this->from ?: now()->toDateString();
        $this->showRecordModal = true;
    }

    public function edit(int $id): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_attendance'), Response::HTTP_FORBIDDEN);

        $attendance = ClientGuardAttendance::query()
            ->with(['client', 'securityGuard'])
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($id);

        $this->editingAttendanceId = $attendance->getKey();
        $this->deploymentId = $attendance->deploymentId;
        $this->date = $attendance->date?->toDateString() ?: now()->toDateString();
        $this->attended = $attendance->attended->value;
        $this->absentCategory = $attendance->absentCategory?->value;
        $this->reason = $attendance->reason;
        $this->recordGuardName = $attendance->securityGuard
            ? trim(($attendance->securityGuard->fname ?? '').' '.($attendance->securityGuard->lname ?? ''))
            : null;
        $this->recordClientName = $attendance->client?->name;
        $this->showRecordModal = true;
    }

    public function record(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_attendance'), Response::HTTP_FORBIDDEN);

        $validated = $this->validate();

        $attendance = $this->editingAttendanceId
            ? ClientGuardAttendance::query()
                ->where('businessId', $this->tenant->businessId())
                ->findOrFail($this->editingAttendanceId)
            : null;

        $deployment = null;

        if (! $attendance || $validated['deploymentId']) {
            $deployment = ClientGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->findOrFail($validated['deploymentId']);
        }

        if (! $attendance && $deployment) {
            $attendance = ClientGuardAttendance::query()
                ->firstOrNew([
                    'businessId' => $this->tenant->businessId(),
                    'deploymentId' => $deployment->getKey(),
                    'date' => $validated['date'],
                ]);
        }

        $attendance->forceFill([
            'deploymentId' => $deployment?->getKey() ?? $attendance->deploymentId,
            'clientId' => $deployment?->clientId ?? $attendance->clientId,
            'guardId' => $deployment?->guardId ?? $attendance->guardId,
            'schedule_type' => $deployment?->schedule_type->value ?? $attendance->schedule_type->value,
            'over_time' => $deployment?->over_time ?? $attendance->over_time,
            'date' => $validated['date'],
            'attended' => $validated['attended'],
            'absentCategory' => $validated['attended'] === AttendanceStatus::Absent->value ? $validated['absentCategory'] : null,
            'reason' => $validated['reason'],
            'userId' => $this->tenant->user()->getKey(),
            'businessId' => $this->tenant->businessId(),
        ]);
        $attendance->save();

        $this->audit->record("Recorded guard attendance for deployment {$attendance->deploymentId}", $this->tenant->user());

        $this->showRecordModal = false;
        $this->resetRecordForm();

        Flux::toast(variant: 'success', text: __('Attendance recorded successfully.'));
    }

    protected function resetRecordForm(): void
    {
        $this->reset([
            'deploymentId',
            'absentCategory',
            'reason',
            'editingAttendanceId',
            'recordGuardName',
            'recordClientName',
        ]);
        $this->attended = AttendanceStatus::Present->value;
        $this->date = $this->from ?: now()->toDateString();
        $this->resetValidation();
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_attendance'), Response::HTTP_FORBIDDEN);

        [$from, $to] = $this->attendanceHelper->resolveDateRange($this->dateRange, $this->from, $this->to);
        $this->from = $from;
        $this->to = $to;

        $attendanceRows = ClientGuardAttendance::query()
            ->with(['client', 'securityGuard'])
            ->where('businessId', $this->tenant->businessId())
            ->whereBetween('date', [$from, $to])
            ->when($this->clientId, fn ($query) => $query->where('clientId', $this->clientId))
            ->when($this->status !== null, fn ($query) => $query->where('attended', $this->status))
            ->when($this->scheduleType !== null, fn ($query) => $query->where('schedule_type', $this->scheduleType))
            ->when($this->duty !== null && $this->duty !== '', fn ($query) => $query->where('over_time', $this->duty === 'overtime'))
            ->when($this->search, function ($query): void {
                $search = '%'.trim((string) $this->search).'%';

                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('reason', 'like', $search)
                        ->orWhereHas('client', fn ($query) => $query->where('name', 'like', $search))
                        ->orWhereHas('securityGuard', function ($query) use ($search): void {
                            $query
                                ->where('code', 'like', $search)
                                ->orWhere('fname', 'like', $search)
                                ->orWhere('lname', 'like', $search);
                        });
                });
            })
            ->tap(function ($query): void {
                match ($this->sortBy) {
                    'client' => $query->orderBy(
                        Client::query()
                            ->select('name')
                            ->whereColumn('clients.id', 'client_guard_attendances.clientId'),
                        $this->sortDirection
                    ),
                    'guard' => $query->orderBy(
                        SecurityGuard::query()
                            ->select('fname')
                            ->whereColumn('security_guards.id', 'client_guard_attendances.guardId'),
                        $this->sortDirection
                    ),
                    'schedule_type', 'attended', 'over_time' => $query->orderBy($this->sortBy, $this->sortDirection),
                    default => $query->orderBy('date', $this->sortDirection)->orderByDesc('id'),
                };
            })
            ->paginate(15);

        return view('livewire.operations.attendance-page', [
            'deployments' => ClientGuard::query()
                ->with(['client', 'securityGuard'])
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->latest()
                ->get(),
            'clients' => Client::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('name')
                ->get(),
            'attendanceRows' => $attendanceRows,
            'attendanceStatuses' => AttendanceStatus::cases(),
            'absenceCategories' => AbsenceCategory::cases(),
            'scheduleTypes' => ScheduleType::cases(),
            'dateRanges' => $this->attendanceHelper->dateRanges(),
            'attendanceHelper' => $this->attendanceHelper,
        ]);
    }
}
