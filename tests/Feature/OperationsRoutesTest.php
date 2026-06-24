<?php

use App\Enums\AttendanceStatus;
use App\Enums\BillingCycle;
use App\Enums\GuardGender;
use App\Enums\ScheduleType;
use App\Livewire\Operations\AddAttendancePage;
use App\Livewire\Operations\AssignmentsPage;
use App\Livewire\Operations\AttendancePage;
use App\Livewire\Operations\ClientCategoriesPage;
use App\Livewire\Operations\ClientDocumentsPage;
use App\Livewire\Operations\ClientFormPage;
use App\Livewire\Operations\ExpenseBudgetsPage;
use App\Livewire\Operations\ExpenseCategoriesPage;
use App\Livewire\Operations\ExpensesPage;
use App\Livewire\Operations\GenerateAttendancePage;
use App\Livewire\Operations\GuardAttendancePage;
use App\Livewire\Operations\GuardDocumentsPage;
use App\Livewire\Operations\GuardRefereesPage;
use App\Livewire\Operations\InventoryCategoriesPage;
use App\Livewire\Operations\InventoryItemsPage;
use App\Livewire\Operations\InventoryStockInsPage;
use App\Livewire\Operations\InventoryStockUsagesPage;
use App\Livewire\Operations\InventoryUnitsPage;
use App\Livewire\Operations\SecurityGuardFormPage;
use App\Models\Business;
use App\Models\Client;
use App\Models\ClientCategory;
use App\Models\ClientDocument;
use App\Models\ClientGuard;
use App\Models\ClientGuardAttendance;
use App\Models\Expense;
use App\Models\ExpenseBudget;
use App\Models\ExpenseCategory;
use App\Models\FinancialYear;
use App\Models\GuardDocument;
use App\Models\GuardReferee;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryStockUsage;
use App\Models\PaymentMode;
use App\Models\Permission;
use App\Models\SecurityGuard;
use App\Models\Unit;
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
    ['attendance.add', 'Assign Guard / Add Attendance'],
    ['attendance.summary', 'Attendance Summary'],
    ['attendance.guard', 'Attendance by Single Guard'],
    ['attendance.generate', 'Generate Attendance'],
    ['expenses.index', 'Fuel for patrol route'],
    ['expenses.categories', 'Transport'],
    ['expenses.budgets', 'Transport'],
    ['inventory.items', 'Security Shirt'],
    ['inventory.categories', 'Uniforms'],
    ['inventory.units', 'pcs'],
    ['inventory.stock-ins', 'Security Shirt'],
    ['inventory.stock-usages', 'Issued to guard'],
    ['inventory.movements', 'Issued stock'],
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
        'view_expenses' => false,
        'manage_inventory' => false,
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
    $this->actingAs($user)->get(route('attendance.add'))->assertForbidden();
    $this->actingAs($user)->get(route('attendance.summary'))->assertForbidden();
    $this->actingAs($user)->get(route('attendance.guard'))->assertForbidden();
    $this->actingAs($user)->get(route('attendance.generate'))->assertForbidden();
    $this->actingAs($user)->get(route('expenses.index'))->assertForbidden();
    $this->actingAs($user)->get(route('expenses.categories'))->assertForbidden();
    $this->actingAs($user)->get(route('expenses.budgets'))->assertForbidden();
    $this->actingAs($user)->get(route('inventory.items'))->assertForbidden();
    $this->actingAs($user)->get(route('inventory.categories'))->assertForbidden();
    $this->actingAs($user)->get(route('inventory.units'))->assertForbidden();
    $this->actingAs($user)->get(route('inventory.stock-ins'))->assertForbidden();
    $this->actingAs($user)->get(route('inventory.stock-usages'))->assertForbidden();
    $this->actingAs($user)->get(route('inventory.movements'))->assertForbidden();
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

test('admin can manually add attendance', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();
    $client = Client::query()->where('businessId', $user->businessId)->firstOrFail();
    $guard = SecurityGuard::query()->where('businessId', $user->businessId)->firstOrFail();

    $this->actingAs($user);

    Livewire::test(AddAttendancePage::class)
        ->set('clientId', $client->getKey())
        ->set('guardId', $guard->getKey())
        ->set('scheduleType', ScheduleType::Day->value)
        ->set('date', '2026-06-18')
        ->set('overtime', true)
        ->call('save')
        ->assertHasNoErrors();

    expect(ClientGuardAttendance::query()
        ->where('businessId', $user->businessId)
        ->where('clientId', $client->getKey())
        ->where('guardId', $guard->getKey())
        ->whereDate('date', '2026-06-18')
        ->where('over_time', true)
        ->exists())->toBeTrue();
});

test('admin can record an expense', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();
    $category = ExpenseCategory::query()->where('businessId', $user->businessId)->firstOrFail();
    $mode = PaymentMode::query()->where('businessId', $user->businessId)->firstOrFail();
    $year = FinancialYear::query()->where('businessId', $user->businessId)->firstOrFail();

    $this->actingAs($user);

    Livewire::test(ExpensesPage::class)
        ->call('create')
        ->set('categoryId', $category->getKey())
        ->set('modeId', $mode->getKey())
        ->set('yearId', $year->getKey())
        ->set('date', '2026-06-19')
        ->set('amount', '150000')
        ->set('description', 'Fuel refill')
        ->call('save')
        ->assertHasNoErrors();

    expect(Expense::query()
        ->where('businessId', $user->businessId)
        ->where('description', 'Fuel refill')
        ->exists())->toBeTrue();
});

test('admin can create expense categories and budgets', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();
    $year = FinancialYear::query()->where('businessId', $user->businessId)->firstOrFail();

    $this->actingAs($user);

    Livewire::test(ExpenseCategoriesPage::class)
        ->call('create')
        ->set('name', 'Meals')
        ->call('save')
        ->assertHasNoErrors();

    $category = ExpenseCategory::query()
        ->where('businessId', $user->businessId)
        ->where('name', 'Meals')
        ->firstOrFail();

    Livewire::test(ExpenseBudgetsPage::class)
        ->call('create')
        ->set('categoryId', $category->getKey())
        ->set('yearId', $year->getKey())
        ->set('amount', '2500000')
        ->call('save')
        ->assertHasNoErrors();

    expect(ExpenseBudget::query()
        ->where('businessId', $user->businessId)
        ->where('categoryId', $category->getKey())
        ->where('yearId', $year->getKey())
        ->where('amount', '2500000')
        ->exists())->toBeTrue();
});

test('admin can create inventory categories units and items', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();

    $this->actingAs($user);

    Livewire::test(InventoryCategoriesPage::class)
        ->call('create')
        ->set('name', 'Boots')
        ->call('save')
        ->assertHasNoErrors();

    Livewire::test(InventoryUnitsPage::class)
        ->call('create')
        ->set('symbol', 'pair')
        ->call('save')
        ->assertHasNoErrors();

    $category = InventoryCategory::query()->where('businessId', $user->businessId)->where('name', 'Boots')->firstOrFail();
    $unit = Unit::query()->where('businessId', $user->businessId)->where('symbol', 'pair')->firstOrFail();

    Livewire::test(InventoryItemsPage::class)
        ->call('create')
        ->set('categoryId', $category->getKey())
        ->set('unitId', $unit->getKey())
        ->set('name', 'Guard Boots')
        ->set('openingStock', '10')
        ->set('quantity', '10')
        ->set('buyingPrice', '50000')
        ->call('save')
        ->assertHasNoErrors();

    expect(InventoryItem::query()
        ->where('businessId', $user->businessId)
        ->where('name', 'Guard Boots')
        ->exists())->toBeTrue();
});

test('admin can record stock in and stock usage', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();
    $item = InventoryItem::query()->where('businessId', $user->businessId)->where('name', 'Security Shirt')->firstOrFail();
    $mode = PaymentMode::query()->where('businessId', $user->businessId)->firstOrFail();
    $guard = SecurityGuard::query()->where('businessId', $user->businessId)->firstOrFail();

    $this->actingAs($user);

    Livewire::test(InventoryStockInsPage::class)
        ->call('clearCart')
        ->set('itemId', $item->getKey())
        ->set('quantity', '2')
        ->set('buyingPrice', '36000')
        ->call('addToCart')
        ->assertHasNoErrors()
        ->set('paymentMode', $mode->getKey())
        ->set('date', '2026-06-20')
        ->set('paid', '72000')
        ->call('save')
        ->assertHasNoErrors();

    expect((float) $item->refresh()->quantity)->toBe(22.0);

    Livewire::test(InventoryStockUsagesPage::class)
        ->call('create')
        ->set('itemId', $item->getKey())
        ->set('guardId', $guard->getKey())
        ->set('date', '2026-06-20')
        ->set('quantity', '1')
        ->set('description', 'Issued replacement shirt')
        ->call('save')
        ->assertHasNoErrors();

    expect((float) $item->refresh()->quantity)->toBe(21.0)
        ->and(InventoryStockUsage::query()
            ->where('businessId', $user->businessId)
            ->where('description', 'Issued replacement shirt')
            ->exists())->toBeTrue();
});

test('admin can delete guard attendance', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();
    $attendance = ClientGuardAttendance::query()->where('businessId', $user->businessId)->firstOrFail();

    $this->actingAs($user);

    Livewire::test(GuardAttendancePage::class)
        ->set('guardId', $attendance->guardId)
        ->call('delete', $attendance->getKey())
        ->assertHasNoErrors();

    expect(ClientGuardAttendance::query()->find($attendance->getKey()))->toBeNull();
});

test('admin can generate attendance over date range', function () {
    $this->seed(LionGuardSeeder::class);

    $user = User::query()->where('email', 'admin@lionguard.test')->firstOrFail();

    $this->actingAs($user);

    Livewire::test(GenerateAttendancePage::class)
        ->set('startDate', '2026-06-15')
        ->set('endDate', '2026-06-17')
        ->call('generate')
        ->assertHasNoErrors();
});
