<?php

namespace App\Livewire\Operations;

use App\Models\Client;
use App\Models\ClientGuard;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Guard Cycle')]
class GuardCyclePage extends Component
{
    use WithPagination;

    public ?int $clientId = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    public function boot(TenantContext $tenant, PermissionService $permissions): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
    }

    public function updatedClientId(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'view_clients'), Response::HTTP_FORBIDDEN);

        $cycles = ClientGuard::query()
            ->with(['client', 'securityGuard'])
            ->where('businessId', $this->tenant->businessId())
            ->when($this->clientId, fn ($query) => $query->where('clientId', $this->clientId))
            ->when(! $this->clientId, fn ($query) => $query->whereRaw('1 = 0'))
            ->latest('from')
            ->paginate(15);

        return view('livewire.operations.guard-cycle-page', [
            'clients' => Client::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('name')
                ->get(),
            'cycles' => $cycles,
        ]);
    }
}
