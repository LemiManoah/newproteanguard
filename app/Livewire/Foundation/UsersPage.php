<?php

namespace App\Livewire\Foundation;

use App\Models\User;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Users')]
class UsersPage extends Component
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
        abort_unless($this->permissions->can($this->tenant->user(), 'view_users'), Response::HTTP_FORBIDDEN);

        return view('livewire.foundation.users-page', [
            'users' => User::query()
                ->with('role')
                ->where('businessId', $this->tenant->businessId())
                ->latest()
                ->get(),
        ]);
    }
}
