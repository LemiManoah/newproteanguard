<?php

namespace App\Livewire\Operations;

use App\Enums\AttendanceStatus;
use App\Models\ClientGuardAttendance;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Attendance Summary')]
class AttendanceSummaryPage extends Component
{
    public ?string $from = null;

    public ?string $to = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    public function boot(TenantContext $tenant, PermissionService $permissions): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
    }

    public function mount(): void
    {
        $this->from = now()->startOfMonth()->toDateString();
        $this->to = now()->toDateString();
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_attendance'), Response::HTTP_FORBIDDEN);

        $rows = ClientGuardAttendance::query()
            ->with(['client', 'securityGuard'])
            ->where('businessId', $this->tenant->businessId())
            ->when($this->from, fn ($query) => $query->whereDate('date', '>=', $this->from))
            ->when($this->to, fn ($query) => $query->whereDate('date', '<=', $this->to))
            ->latest('date')
            ->get();

        return view('livewire.operations.attendance-summary-page', [
            'rows' => $rows,
            'presentCount' => $rows->filter(fn (ClientGuardAttendance $row): bool => $row->attended === AttendanceStatus::Present)->count(),
            'absentCount' => $rows->filter(fn (ClientGuardAttendance $row): bool => $row->attended === AttendanceStatus::Absent)->count(),
            'replacedCount' => $rows->filter(fn (ClientGuardAttendance $row): bool => $row->attended === AttendanceStatus::Replaced)->count(),
        ]);
    }
}
