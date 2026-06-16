<?php

namespace App\Livewire\Operations;

use App\Models\SecurityGuard;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Security Guards')]
class SecurityGuardsPage extends Component
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

        return view('livewire.operations.security-guards-page', [
            'guards' => SecurityGuard::query()
                ->withCount('activeClients')
                ->where('businessId', $this->tenant->businessId())
                ->latest()
                ->get(),
        ]);
    }
}
