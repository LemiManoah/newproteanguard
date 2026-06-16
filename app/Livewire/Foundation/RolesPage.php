<?php

namespace App\Livewire\Foundation;

use App\Models\Permission;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Roles')]
class RolesPage extends Component
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
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_permission'), Response::HTTP_FORBIDDEN);

        return view('livewire.foundation.roles-page', [
            'roles' => Permission::query()
                ->where('businessId', $this->tenant->businessId())
                ->latest()
                ->get(),
        ]);
    }
}
