<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;

class PermissionService
{
    public function can(User $user, string $permission): bool
    {
        if (! in_array($permission, Permission::PERMISSION_COLUMNS, true)) {
            return false;
        }

        $role = $user->role;

        if (! $role instanceof Permission || ! $role->status) {
            return false;
        }

        return (bool) $role->getAttribute($permission);
    }

    /**
     * @param  array<int, string>  $permissions
     */
    public function canAny(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->can($user, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, string>  $permissions
     */
    public function canAll(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $this->can($user, $permission)) {
                return false;
            }
        }

        return true;
    }

    public function canToggle(User $user, Permission $role, string $column): bool
    {
        return $this->can($user, 'manage_permission')
            && in_array($column, Permission::PERMISSION_COLUMNS, true)
            && $user->businessId === $role->businessId;
    }
}
