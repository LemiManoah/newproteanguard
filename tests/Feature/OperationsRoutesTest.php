<?php

use App\Enums\AttendanceStatus;
use App\Enums\GuardGender;
use App\Enums\ScheduleType;
use App\Livewire\Operations\AssignmentsPage;
use App\Livewire\Operations\AttendancePage;
use App\Models\Business;
use App\Models\Client;
use App\Models\ClientGuard;
use App\Models\ClientGuardAttendance;
use App\Models\Permission;
use App\Models\SecurityGuard;
use App\Models\User;
use Database\Seeders\LionGuardSeeder;
use Livewire\Livewire;

test('lionguard admin can access operations livewire pages', function (string $routeName, string $text) {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route($routeName))
        ->assertOk()
        ->assertSee($text);
})->with([
    ['clients.index', 'Acacia Mall'],
    ['guards.index', 'LG-001'],
    ['assignments.index', 'Guard Assignments'],
    ['attendance.index', 'Client Attendance'],
]);

test('operations pages require their legacy permissions', function () {
    $business = Business::query()->create([
        'name' => 'Restricted Operations Business',
    ]);

    $role = new Permission;
    $role->forceFill([
        'name' => 'Restricted',
        'businessId' => $business->getKey(),
        'status' => true,
        'view_dashboard' => true,
        'view_clients' => false,
        'view_guards' => false,
        'assign_guards' => false,
        'manage_attendance' => false,
    ]);
    $role->save();

    $user = User::factory()->create([
        'businessId' => $business->getKey(),
        'roleId' => $role->getKey(),
        'status' => true,
    ]);

    $this->actingAs($user)->get(route('clients.index'))->assertForbidden();
    $this->actingAs($user)->get(route('guards.index'))->assertForbidden();
    $this->actingAs($user)->get(route('assignments.index'))->assertForbidden();
    $this->actingAs($user)->get(route('attendance.index'))->assertForbidden();
});

test('admin can assign an available guard to a client', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();
    $client = Client::query()->where('businessId', $user->businessId)->where('name', 'Acacia Mall')->firstOrFail();

    $guard = new SecurityGuard;
    $guard->forceFill([
        'businessId' => $user->businessId,
        'userId' => $user->getKey(),
        'code_number' => 2,
        'code' => 'LG-002',
        'fname' => 'Amina',
        'lname' => 'Nabwire',
        'contact1' => '0701000005',
        'gender' => GuardGender::Female->value,
        'join_date' => '2026-06-01',
        'assigned' => false,
        'status' => true,
    ]);
    $guard->save();

    $this->actingAs($user);

    Livewire::test(AssignmentsPage::class)
        ->set('clientId', $client->getKey())
        ->set('guardId', $guard->getKey())
        ->set('from', '2026-06-17')
        ->set('scheduleType', ScheduleType::Night->value)
        ->call('assign')
        ->assertHasNoErrors();

    expect(ClientGuard::query()
        ->where('businessId', $user->businessId)
        ->where('clientId', $client->getKey())
        ->where('guardId', $guard->getKey())
        ->where('status', true)
        ->exists())->toBeTrue()
        ->and($guard->refresh()->assigned)->toBeTrue();
});

test('admin can record attendance for an active deployment', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();
    $deployment = ClientGuard::query()->where('businessId', $user->businessId)->firstOrFail();

    $this->actingAs($user);

    Livewire::test(AttendancePage::class)
        ->set('deploymentId', $deployment->getKey())
        ->set('date', '2026-06-17')
        ->set('attended', AttendanceStatus::Absent->value)
        ->set('reason', 'Sick leave')
        ->call('record')
        ->assertHasNoErrors();

    expect(ClientGuardAttendance::query()
        ->where('businessId', $user->businessId)
        ->where('deploymentId', $deployment->getKey())
        ->whereDate('date', '2026-06-17')
        ->where('attended', AttendanceStatus::Absent->value)
        ->where('reason', 'Sick leave')
        ->exists())->toBeTrue();
});
