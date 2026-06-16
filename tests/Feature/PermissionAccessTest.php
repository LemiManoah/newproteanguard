<?php

use App\Models\AuditLog;
use App\Models\Business;
use App\Models\Permission;
use App\Models\User;
use App\Services\AuditService;
use App\Services\PermissionService;

function userWithRole(array $permissionValues = [], ?Business $business = null): array
{
    $business ??= Business::query()->create([
        'name' => 'Policy Test Business',
    ]);

    $role = new Permission;
    $role->forceFill(array_merge([
        'name' => 'Operator',
        'businessId' => $business->getKey(),
        'status' => true,
    ], $permissionValues));
    $role->save();

    $user = User::factory()->create([
        'businessId' => $business->getKey(),
        'roleId' => $role->getKey(),
        'status' => true,
    ]);

    return [$user, $role, $business];
}

test('permission service reads legacy role columns safely', function () {
    [$user] = userWithRole([
        'view_dashboard' => true,
        'view_clients' => false,
    ]);

    $service = app(PermissionService::class);

    expect($service->can($user, 'view_dashboard'))->toBeTrue()
        ->and($service->can($user, 'view_clients'))->toBeFalse()
        ->and($service->can($user, 'not_a_real_permission'))->toBeFalse();
});

test('permission toggles require whitelist, permission, and same business', function () {
    [$manager, $managedRole, $business] = userWithRole([
        'manage_permission' => true,
    ]);

    [, $otherRole] = userWithRole([
        'manage_permission' => true,
    ], Business::query()->create([
        'name' => 'Other Business',
    ]));

    $service = app(PermissionService::class);

    expect($service->canToggle($manager, $managedRole, 'view_clients'))->toBeTrue()
        ->and($service->canToggle($manager, $managedRole, 'not_a_real_permission'))->toBeFalse()
        ->and($service->canToggle($manager, $otherRole, 'view_clients'))->toBeFalse();

    [$viewer] = userWithRole([
        'manage_permission' => false,
    ], $business);

    expect($service->canToggle($viewer, $managedRole, 'view_clients'))->toBeFalse();
});

test('audit service writes tenant scoped audit records', function () {
    [$user] = userWithRole();

    $log = app(AuditService::class)->record('Opened dashboard', $user);

    expect($log)->toBeInstanceOf(AuditLog::class)
        ->and($log->action)->toBe('Opened dashboard')
        ->and($log->userId)->toBe($user->getKey())
        ->and($log->businessId)->toBe($user->businessId)
        ->and($log->status)->toBeTrue();
});
