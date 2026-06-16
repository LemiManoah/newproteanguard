<?php

use App\Models\Business;
use App\Models\Permission;
use App\Models\User;
use Database\Seeders\LionGuardSeeder;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('active lionguard users can visit the dashboard', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();

    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response
        ->assertOk()
        ->assertSee('LionGuard')
        ->assertSee('Clients')
        ->assertSee('Guards');
});

test('inactive users cannot visit the dashboard', function () {
    $business = Business::query()->create([
        'name' => 'Inactive Business',
    ]);

    $role = new Permission;
    $role->forceFill([
        'name' => 'Inactive Role',
        'businessId' => $business->getKey(),
        'status' => true,
        'view_dashboard' => true,
    ]);
    $role->save();

    $user = User::factory()->create([
        'businessId' => $business->getKey(),
        'roleId' => $role->getKey(),
        'status' => false,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();
});

test('users without dashboard permission cannot visit the dashboard', function () {
    $business = Business::query()->create([
        'name' => 'Restricted Business',
    ]);

    $role = new Permission;
    $role->forceFill([
        'name' => 'Restricted Role',
        'businessId' => $business->getKey(),
        'status' => true,
        'view_dashboard' => false,
    ]);
    $role->save();

    $user = User::factory()->create([
        'businessId' => $business->getKey(),
        'roleId' => $role->getKey(),
        'status' => true,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();
});
