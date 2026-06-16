<?php

use App\Livewire\Foundation\PaymentModesPage;
use App\Livewire\Foundation\PermissionsPage;
use App\Models\Business;
use App\Models\PaymentMode;
use App\Models\Permission;
use App\Models\User;
use Database\Seeders\LionGuardSeeder;
use Livewire\Livewire;

test('lionguard admin can access foundation livewire pages', function (string $routeName, string $text) {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route($routeName))
        ->assertOk()
        ->assertSee($text);
})->with([
    ['users.index', 'LionGuard Admin'],
    ['roles.index', 'Administrator'],
    ['permissions.index', 'Dashboard'],
    ['modes.index', 'Main Cash'],
    ['audits.index', 'Audit Logs'],
]);

test('foundation pages require their legacy permissions', function () {
    $business = Business::query()->create([
        'name' => 'Restricted Foundation Business',
    ]);

    $role = new Permission;
    $role->forceFill([
        'name' => 'Restricted',
        'businessId' => $business->getKey(),
        'status' => true,
        'view_dashboard' => true,
        'view_users' => false,
        'manage_permission' => false,
        'view_paymodes' => false,
        'view_logs' => false,
    ]);
    $role->save();

    $user = User::factory()->create([
        'businessId' => $business->getKey(),
        'roleId' => $role->getKey(),
        'status' => true,
    ]);

    $this->actingAs($user)->get(route('users.index'))->assertForbidden();
    $this->actingAs($user)->get(route('roles.index'))->assertForbidden();
    $this->actingAs($user)->get(route('permissions.index'))->assertForbidden();
    $this->actingAs($user)->get(route('modes.index'))->assertForbidden();
    $this->actingAs($user)->get(route('audits.index'))->assertForbidden();
});

test('permission page toggles whitelisted permissions for same tenant roles', function () {
    $business = Business::query()->create([
        'name' => 'Toggle Business',
    ]);

    $managerRole = new Permission;
    $managerRole->forceFill([
        'name' => 'Manager',
        'businessId' => $business->getKey(),
        'status' => true,
        'view_dashboard' => true,
        'manage_permission' => true,
    ]);
    $managerRole->save();

    $operatorRole = new Permission;
    $operatorRole->forceFill([
        'name' => 'Operator',
        'businessId' => $business->getKey(),
        'status' => true,
        'view_clients' => false,
    ]);
    $operatorRole->save();

    $manager = User::factory()->create([
        'businessId' => $business->getKey(),
        'roleId' => $managerRole->getKey(),
        'status' => true,
    ]);

    $this->actingAs($manager);

    Livewire::test(PermissionsPage::class)
        ->call('toggle', $operatorRole->getKey(), 'view_clients');

    expect($operatorRole->refresh()->view_clients)->toBeTrue();
});

test('payment modes page can set one default mode for the tenant', function () {
    $business = Business::query()->create([
        'name' => 'Modes Business',
    ]);

    $role = new Permission;
    $role->forceFill([
        'name' => 'Modes Manager',
        'businessId' => $business->getKey(),
        'status' => true,
        'view_dashboard' => true,
        'view_paymodes' => true,
        'edit_paymodes' => true,
    ]);
    $role->save();

    $user = User::factory()->create([
        'businessId' => $business->getKey(),
        'roleId' => $role->getKey(),
        'status' => true,
    ]);

    $cash = new PaymentMode;
    $cash->forceFill([
        'businessId' => $business->getKey(),
        'userId' => $user->getKey(),
        'name' => 'Cash',
        'type' => 'Cash',
        'is_default' => true,
    ]);
    $cash->save();

    $bank = new PaymentMode;
    $bank->forceFill([
        'businessId' => $business->getKey(),
        'userId' => $user->getKey(),
        'name' => 'Bank',
        'type' => 'Bank',
        'is_default' => false,
    ]);
    $bank->save();

    $this->actingAs($user);

    Livewire::test(PaymentModesPage::class)
        ->call('setDefault', $bank->getKey());

    expect($cash->refresh()->is_default)->toBeFalse()
        ->and($bank->refresh()->is_default)->toBeTrue();
});
