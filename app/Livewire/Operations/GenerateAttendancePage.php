<?php

namespace App\Livewire\Operations;

use App\Services\AttendanceService;
use App\Services\AuditService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Generate Attendance')]
class GenerateAttendancePage extends Component
{
    public ?string $startDate = null;

    public ?string $endDate = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    protected AuditService $audit;

    protected AttendanceService $attendanceService;

    public function boot(
        TenantContext $tenant,
        PermissionService $permissions,
        AuditService $audit,
        AttendanceService $attendanceService
    ): void {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
        $this->audit = $audit;
        $this->attendanceService = $attendanceService;
    }

    public function mount(): void
    {
        $this->startDate = now()->toDateString();
        $this->endDate = now()->toDateString();
    }

    protected function rules(): array
    {
        return [
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
        ];
    }

    public function generate()
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_attendance'), Response::HTTP_FORBIDDEN);

        $validated = $this->validate();

        $businessId = $this->tenant->businessId();
        $userId = $this->tenant->user()->getKey();

        $this->attendanceService->checkAttendanceRange($validated['startDate'], $validated['endDate'], $businessId, $userId);

        $this->audit->record("Generated periodic attendance from {$validated['startDate']} to {$validated['endDate']}", $this->tenant->user());

        Flux::toast(variant: 'success', text: __('Successfully Generated'));
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_attendance'), Response::HTTP_FORBIDDEN);

        return view('livewire.operations.generate-attendance-page');
    }
}
