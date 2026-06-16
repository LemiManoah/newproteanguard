<?php

namespace App\Livewire\Foundation;

use App\Models\Permission;
use App\Services\AuditService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Permissions')]
class PermissionsPage extends Component
{
    protected TenantContext $tenant;

    protected PermissionService $permissions;

    protected AuditService $audit;

    public function boot(TenantContext $tenant, PermissionService $permissions, AuditService $audit): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
        $this->audit = $audit;
    }

    public function toggle(int $roleId, string $column): void
    {
        $role = Permission::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($roleId);

        abort_unless($this->permissions->canToggle($this->tenant->user(), $role, $column), Response::HTTP_FORBIDDEN);

        $role->forceFill([
            $column => ! (bool) $role->getAttribute($column),
        ]);
        $role->save();

        $this->audit->record("Updated {$role->name} permission {$column}", $this->tenant->user());
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_permission'), Response::HTTP_FORBIDDEN);

        return view('livewire.foundation.permissions-page', [
            'permissionColumns' => Permission::PERMISSION_COLUMNS,
            'roles' => Permission::query()
                ->where('businessId', $this->tenant->businessId())
                ->latest()
                ->get(),
        ]);
    }
}
