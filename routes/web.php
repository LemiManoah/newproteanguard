<?php

use App\Livewire\Dashboard;
use App\Livewire\Foundation\AuditLogsPage;
use App\Livewire\Foundation\PaymentModesPage;
use App\Livewire\Foundation\PermissionsPage;
use App\Livewire\Foundation\RolesPage;
use App\Livewire\Foundation\UsersPage;
use App\Livewire\Operations\AssignmentsPage;
use App\Livewire\Operations\AttendancePage;
use App\Livewire\Operations\ClientsPage;
use App\Livewire\Operations\SecurityGuardsPage;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::livewire('/', Dashboard::class)->name('dashboard');
    Route::redirect('/dashboard', '/');

    Route::livewire('/users', UsersPage::class)->name('users.index');
    Route::livewire('/roles', RolesPage::class)->name('roles.index');
    Route::livewire('/permissions', PermissionsPage::class)->name('permissions.index');
    Route::livewire('/modes', PaymentModesPage::class)->name('modes.index');
    Route::livewire('/audits', AuditLogsPage::class)->name('audits.index');

    Route::livewire('/view_clients', ClientsPage::class)->name('clients.index');
    Route::livewire('/view_guards', SecurityGuardsPage::class)->name('guards.index');
    Route::livewire('/assign_guard', AssignmentsPage::class)->name('assignments.index');
    Route::livewire('/client_attendance', AttendancePage::class)->name('attendance.index');

    Route::redirect('/clients', '/view_clients');
    Route::redirect('/security_guards', '/view_guards');
    Route::redirect('/attendance', '/client_attendance');
});

require __DIR__.'/settings.php';
