<?php

namespace App\Livewire\Operations;

use App\Enums\AttendanceStatus;
use App\Models\SecurityGuard;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Attendance Summary')]
class AttendanceSummaryPage extends Component
{
    use WithPagination;

    public ?string $from = null;

    public ?string $to = null;

    public ?string $search = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    public function boot(TenantContext $tenant, PermissionService $permissions): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
    }

    public function mount(): void
    {
        $this->from = now()->toDateString();
        $this->to = now()->toDateString();
    }

    public function updated($property): void
    {
        if (in_array($property, ['from', 'to', 'search'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_attendance'), Response::HTTP_FORBIDDEN);

        $from = $this->from ?: now()->toDateString();
        $to = $this->to ?: $from;

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        $guards = SecurityGuard::query()
            ->where('businessId', $this->tenant->businessId())
            ->when($this->search, function ($query): void {
                $search = '%'.trim((string) $this->search).'%';

                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('code', 'like', $search)
                        ->orWhere('fname', 'like', $search)
                        ->orWhere('lname', 'like', $search);
                });
            })
            ->whereHas('attendances', function ($query) use ($from, $to): void {
                $query
                    ->whereBetween('date', [$from, $to])
                    ->where('attended', AttendanceStatus::Present->value);
            })
            ->withCount([
                'attendances as worked_count' => function ($query) use ($from, $to): void {
                    $query
                        ->whereBetween('date', [$from, $to])
                        ->where('attended', AttendanceStatus::Present->value)
                        ->where('over_time', false);
                },
                'attendances as overtime_count' => function ($query) use ($from, $to): void {
                    $query
                        ->whereBetween('date', [$from, $to])
                        ->where('attended', AttendanceStatus::Present->value)
                        ->where('over_time', true);
                },
            ])
            ->orderBy('fname')
            ->paginate(15);

        return view('livewire.operations.attendance-summary-page', [
            'guards' => $guards,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
