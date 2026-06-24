<?php

namespace App\Livewire\Operations;

use App\Models\Client;
use App\Models\ClientGuard;
use App\Models\SecurityGuard;
use App\Services\AuditService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Client Schedule')]
class ClientSchedulePage extends Component
{
    use WithPagination;

    public ?string $search = null;

    public ?int $clientId = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    protected AuditService $audit;

    public function boot(TenantContext $tenant, PermissionService $permissions, AuditService $audit): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
        $this->audit = $audit;
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'clientId'], true)) {
            $this->resetPage();
        }
    }

    public function removeGuard(int $id): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'assign_guards'), Response::HTTP_FORBIDDEN);

        $deployment = ClientGuard::query()
            ->with(['client', 'securityGuard'])
            ->where('businessId', $this->tenant->businessId())
            ->where('status', true)
            ->findOrFail($id);

        DB::transaction(function () use ($deployment): void {
            $deployment->forceFill([
                'status' => false,
                'to' => now()->toDateString(),
            ])->save();

            SecurityGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->whereKey($deployment->guardId)
                ->update(['assigned' => false]);

            $activeCount = ClientGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('clientId', $deployment->clientId)
                ->where('status', true)
                ->count();

            Client::query()
                ->where('businessId', $this->tenant->businessId())
                ->whereKey($deployment->clientId)
                ->update([
                    'actual_guards' => $activeCount,
                    'assigned' => $activeCount > 0,
                ]);
        });

        $this->audit->record("Removed guard {$deployment->securityGuard?->code} from client {$deployment->client?->name}", $this->tenant->user());

        Flux::toast(variant: 'success', text: __('Guard removed from client schedule.'));
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'view_client_schedule'), Response::HTTP_FORBIDDEN);

        return view('livewire.operations.client-schedule-page', [
            'clients' => Client::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('name')
                ->get(),
            'schedules' => ClientGuard::query()
                ->with(['client', 'securityGuard'])
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->when($this->clientId, fn ($query) => $query->where('clientId', $this->clientId))
                ->when($this->search, function ($query): void {
                    $search = '%'.trim((string) $this->search).'%';

                    $query->where(function ($query) use ($search): void {
                        $query
                            ->whereHas('client', fn ($query) => $query->where('name', 'like', $search))
                            ->orWhereHas('securityGuard', function ($query) use ($search): void {
                                $query
                                    ->where('code', 'like', $search)
                                    ->orWhere('fname', 'like', $search)
                                    ->orWhere('lname', 'like', $search);
                            });
                    });
                })
                ->latest('from')
                ->paginate(15),
            'canRemove' => $this->permissions->can($this->tenant->user(), 'assign_guards'),
        ]);
    }
}
