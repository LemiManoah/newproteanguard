<?php

namespace App\Livewire\Operations;

use App\Models\SecurityGuard;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Undeployed Guards')]
class UndeployedGuardsPage extends Component
{
    protected TenantContext $tenant;

    protected PermissionService $permissions;

    public function boot(TenantContext $tenant, PermissionService $permissions): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'view_guards'), Response::HTTP_FORBIDDEN);

        return view('livewire.operations.undeployed-guards-page', [
            'guards' => SecurityGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->whereDoesntHave('activeClients')
                ->orderBy('code')
                ->get(),
        ]);
    }
}
