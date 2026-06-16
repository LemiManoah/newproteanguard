<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use App\Services\PermissionService;

class PermissionPolicy
{
    public function __construct(private PermissionService $permissions) {}

    public function viewAny(User $user): bool
    {
        return $this->permissions->can($user, 'manage_permission');
    }

    public function update(User $user, Permission $permission): bool
    {
        return $this->permissions->can($user, 'manage_permission')
            && $user->businessId === $permission->businessId;
    }
}
