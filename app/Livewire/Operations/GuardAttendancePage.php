<?php

namespace App\Livewire\Operations;

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

#[Title('Attendance By Guard')]
class GuardAttendancePage extends Component
{
    public ?int $guardId = null;

    public ?string $from = null;

    public ?string $to = null;

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
        $this->from = now()->startOfMonth()->toDateString();
        $this->to = now()->toDateString();
    }

    public function delete(int $id): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_attendance'), Response::HTTP_FORBIDDEN);

        $attendance = ClientGuardAttendance::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($id);

        $guardName = $attendance->securityGuard
            ? $attendance->securityGuard->fname.' '.$attendance->securityGuard->lname
            : 'Unknown Guard';
        $dateStr = $attendance->date ? $attendance->date->toDateString() : 'Unknown Date';

        $attendance->delete();

        $this->audit->record("Deleted attendance record for {$guardName} on {$dateStr}", $this->tenant->user());

        Flux::toast(variant: 'success', text: __('Attendance record deleted successfully.'));
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_attendance'), Response::HTTP_FORBIDDEN);

        $attendanceRows = collect();

        if ($this->guardId && $this->from && $this->to) {
            $attendanceRows = ClientGuardAttendance::query()
                ->with(['client', 'securityGuard'])
                ->where('businessId', $this->tenant->businessId())
                ->where('guardId', $this->guardId)
                ->whereBetween('date', [$this->from, $this->to])
                ->orderBy('date')
                ->get();
        }

        return view('livewire.operations.guard-attendance-page', [
            'guards' => SecurityGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('fname')
                ->get(),
            'attendanceRows' => $attendanceRows,
        ]);
    }
}
