<?php

namespace App\Livewire\Operations;

use App\Enums\AttendanceStatus;
use App\Enums\ScheduleType;
use App\Models\Client;
use App\Models\ClientGuardAttendance;
use App\Models\SecurityGuard;
use App\Services\AuditService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Add Attendance')]
class AddAttendancePage extends Component
{
    public ?int $clientId = null;

    public ?int $guardId = null;

    public int $scheduleType = 0;

    public ?string $date = null;

    public bool $overtime = false;

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

    protected function rules(): array
    {
        return [
            'clientId' => ['required', 'integer', 'exists:clients,id'],
            'guardId' => ['required', 'integer', 'exists:security_guards,id'],
            'scheduleType' => ['required', 'integer'],
            'date' => ['required', 'date'],
            'overtime' => ['required', 'boolean'],
        ];
    }

    public function save()
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_attendance'), Response::HTTP_FORBIDDEN);

        $validated = $this->validate();

        // Check if attendance already exists
        $check = ClientGuardAttendance::query()
            ->where([
                'guardId' => $validated['guardId'],
                'date' => $validated['date'],
                'schedule_type' => $validated['scheduleType'],
                'over_time' => $validated['overtime'],
                'businessId' => $this->tenant->businessId(),
            ])
            ->exists();

        if ($check) {
            Flux::toast(variant: 'danger', text: __('Guard Already Assigned to Selected Schedule'));

            return;
        }

        ClientGuardAttendance::create([
            'deploymentId' => 0,
            'clientId' => $validated['clientId'],
            'guardId' => $validated['guardId'],
            'schedule_type' => $validated['scheduleType'],
            'over_time' => $validated['overtime'],
            'date' => $validated['date'],
            'attended' => AttendanceStatus::Present->value,
            'userId' => $this->tenant->user()->getKey(),
            'businessId' => $this->tenant->businessId(),
        ]);

        $this->audit->record("Manually recorded attendance for guard {$validated['guardId']}", $this->tenant->user());

        Flux::toast(variant: 'success', text: __('Successfully Added to attendance'));

        $this->reset(['clientId', 'guardId', 'scheduleType', 'overtime']);
        $this->date = now()->toDateString();
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_attendance'), Response::HTTP_FORBIDDEN);

        return view('livewire.operations.add-attendance-page', [
            'clients' => Client::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('name')
                ->get(),
            'guards' => SecurityGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('fname')
                ->get(),
            'scheduleTypes' => ScheduleType::cases(),
        ]);
    }
}
