<?php

use App\Enums\AttendanceStatus;
use App\Enums\BillingCycle;
use App\Enums\GuardGender;
use App\Enums\ScheduleType;
use App\Livewire\Operations\AssignmentsPage;
use App\Livewire\Operations\AttendancePage;
use App\Livewire\Operations\AttendanceSummaryPage;
use App\Livewire\Operations\ClientCategoriesPage;
use App\Livewire\Operations\ClientDocumentsPage;
use App\Livewire\Operations\ClientFormPage;
use App\Livewire\Operations\GuardDocumentsPage;
use App\Livewire\Operations\GuardRefereesPage;
use App\Livewire\Operations\SecurityGuardFormPage;
use App\Models\Business;
use App\Models\Client;
use App\Models\ClientCategory;
use App\Models\ClientDocument;
use App\Models\ClientGuard;
use App\Models\ClientGuardAttendance;
use App\Models\GuardDocument;
use App\Models\GuardReferee;
use App\Models\Permission;
use App\Models\SecurityGuard;
use App\Models\User;
use Database\Seeders\LionGuardSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
    ['client-categories.index', 'Corporate'],
    ['clients.create', 'New Client'],
    ['client-documents.index', 'Service Agreement'],
    ['guards.index', 'LG-001'],
    ['guards.create', 'New Guard'],
    ['guard-documents.index', 'National ID'],
    ['guard-referees.index', 'Moses Akena'],
    ['guards.undeployed', 'Undeployed Guards'],
    ['assignments.index', 'Guard Assignments'],
    ['attendance.index', 'Client Attendance'],
    ['attendance.summary', 'Attendance Summary'],
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
        'mange_client_categories' => false,
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
    $this->actingAs($user)->get(route('client-categories.index'))->assertForbidden();
    $this->actingAs($user)->get(route('client-documents.index'))->assertForbidden();
    $this->actingAs($user)->get(route('guards.index'))->assertForbidden();
    $this->actingAs($user)->get(route('guard-documents.index'))->assertForbidden();
    $this->actingAs($user)->get(route('guard-referees.index'))->assertForbidden();
    $this->actingAs($user)->get(route('guards.undeployed'))->assertForbidden();
    $this->actingAs($user)->get(route('assignments.index'))->assertForbidden();
    $this->actingAs($user)->get(route('attendance.index'))->assertForbidden();
    $this->actingAs($user)->get(route('attendance.summary'))->assertForbidden();
});

test('admin can create client categories in a modal workflow', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();

    $this->actingAs($user);

    Livewire::test(ClientCategoriesPage::class)
        ->call('create')
        ->set('name', 'Government')
        ->call('save')
        ->assertHasNoErrors();

    expect(ClientCategory::query()
        ->where('businessId', $user->businessId)
        ->where('name', 'Government')
        ->exists())->toBeTrue();
});

test('admin can create a client from the full page form', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();
    $category = ClientCategory::query()->where('businessId', $user->businessId)->firstOrFail();

    $this->actingAs($user);

    Livewire::test(ClientFormPage::class)
        ->set('categoryId', $category->getKey())
        ->set('name', 'Garden City')
        ->set('contact1', '0755000099')
        ->set('email', 'security@gardencity.test')
        ->set('billingCycle', BillingCycle::Monthly->value)
        ->set('amount', '1200000')
        ->set('noGuards', '1')
        ->set('billStart', '2026-06-17')
        ->set('scheduleType', ScheduleType::FullTime->value)
        ->call('save')
        ->assertHasNoErrors();

    expect(Client::query()
        ->where('businessId', $user->businessId)
        ->where('name', 'Garden City')
        ->exists())->toBeTrue();
});

test('admin can create a guard from the full page form', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();

    $this->actingAs($user);

    Livewire::test(SecurityGuardFormPage::class)
        ->set('codeNumber', 3)
        ->set('code', 'LG-003')
        ->set('fname', 'Peter')
        ->set('lname', 'Kato')
        ->set('contact1', '0701000007')
        ->set('joinDate', '2026-06-17')
        ->set('gender', GuardGender::Male->value)
        ->call('save')
        ->assertHasNoErrors();

    expect(SecurityGuard::query()
        ->where('businessId', $user->businessId)
        ->where('code', 'LG-003')
        ->exists())->toBeTrue();
});

test('admin can create a guard referee in a modal workflow', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();
    $guard = SecurityGuard::query()->where('businessId', $user->businessId)->where('code', 'LG-001')->firstOrFail();

    $this->actingAs($user);

    Livewire::test(GuardRefereesPage::class)
        ->call('create')
        ->set('guardId', $guard->getKey())
        ->set('name', 'Jane Kusiima')
        ->set('contact', '0702000099')
        ->set('residence', 'Kireka')
        ->call('save')
        ->assertHasNoErrors();

    expect(GuardReferee::query()
        ->where('businessId', $user->businessId)
        ->where('guardId', $guard->getKey())
        ->where('name', 'Jane Kusiima')
        ->exists())->toBeTrue();
});

test('admin can upload private client and guard documents', function () {
    Storage::fake('client_documents');
    Storage::fake('guard_documents');

    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();
    $client = Client::query()->where('businessId', $user->businessId)->where('name', 'Acacia Mall')->firstOrFail();
    $guard = SecurityGuard::query()->where('businessId', $user->businessId)->where('code', 'LG-001')->firstOrFail();

    $this->actingAs($user);

    Livewire::test(ClientDocumentsPage::class)
        ->call('openUpload')
        ->set('clientId', $client->getKey())
        ->set('title', 'Updated Agreement')
        ->set('type', 3)
        ->set('document', UploadedFile::fake()->create('agreement.pdf', 100, 'application/pdf'))
        ->call('save')
        ->assertHasNoErrors();

    Livewire::test(GuardDocumentsPage::class)
        ->call('openUpload')
        ->set('guardId', $guard->getKey())
        ->set('title', 'Guard ID')
        ->set('type', 1)
        ->set('document', UploadedFile::fake()->create('guard-id.pdf', 100, 'application/pdf'))
        ->call('save')
        ->assertHasNoErrors();

    $clientDocument = ClientDocument::query()
        ->where('businessId', $user->businessId)
        ->where('title', 'Updated Agreement')
        ->firstOrFail();

    $guardDocument = GuardDocument::query()
        ->where('businessId', $user->businessId)
        ->where('title', 'Guard ID')
        ->firstOrFail();

    Storage::disk('client_documents')->assertExists((string) $clientDocument->path);
    Storage::disk('guard_documents')->assertExists((string) $guardDocument->path);
});

test('document downloads are tenant protected', function () {
    Storage::fake('client_documents');

    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();

    $otherBusiness = Business::query()->create([
        'name' => 'Other Tenant',
    ]);

    $otherCategory = new ClientCategory;
    $otherCategory->forceFill([
        'name' => 'Other Category',
        'businessId' => $otherBusiness->getKey(),
        'status' => true,
    ]);
    $otherCategory->save();

    $otherClient = new Client;
    $otherClient->forceFill([
        'categoryId' => $otherCategory->getKey(),
        'name' => 'Other Client',
        'businessId' => $otherBusiness->getKey(),
        'status' => true,
    ]);
    $otherClient->save();

    Storage::disk('client_documents')->put('other/agreement.pdf', 'private file');

    $otherDocument = new ClientDocument;
    $otherDocument->forceFill([
        'clientId' => $otherClient->getKey(),
        'title' => 'Other Agreement',
        'type' => 3,
        'disk' => 'client_documents',
        'path' => 'other/agreement.pdf',
        'original_name' => 'agreement.pdf',
        'businessId' => $otherBusiness->getKey(),
        'status' => true,
    ]);
    $otherDocument->save();

    $this->actingAs($user)
        ->get(route('client-documents.download', $otherDocument))
        ->assertNotFound();
});

test('client and guard edit routes are tenant protected', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();

    $otherBusiness = Business::query()->create([
        'name' => 'Profile Other Tenant',
    ]);

    $otherCategory = new ClientCategory;
    $otherCategory->forceFill([
        'name' => 'Profile Category',
        'businessId' => $otherBusiness->getKey(),
        'status' => true,
    ]);
    $otherCategory->save();

    $otherClient = new Client;
    $otherClient->forceFill([
        'categoryId' => $otherCategory->getKey(),
        'name' => 'Profile Other Client',
        'businessId' => $otherBusiness->getKey(),
        'status' => true,
    ]);
    $otherClient->save();

    $otherGuard = new SecurityGuard;
    $otherGuard->forceFill([
        'code' => 'OT-001',
        'fname' => 'Other',
        'lname' => 'Guard',
        'businessId' => $otherBusiness->getKey(),
        'status' => true,
    ]);
    $otherGuard->save();

    $this->actingAs($user)->get(route('clients.edit', $otherClient))->assertNotFound();
    $this->actingAs($user)->get(route('guards.edit', $otherGuard))->assertNotFound();
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
