<?php

use App\Http\Controllers\ClientDocumentDownloadController;
use App\Http\Controllers\GuardDocumentDownloadController;
use App\Livewire\Dashboard;
use App\Livewire\Foundation\AuditLogsPage;
use App\Livewire\Foundation\PaymentModesPage;
use App\Livewire\Foundation\PermissionsPage;
use App\Livewire\Foundation\RolesPage;
use App\Livewire\Foundation\UsersPage;
use App\Livewire\Operations\AssignmentsPage;
use App\Livewire\Operations\AttendanceSummaryPage;
use App\Livewire\Operations\AttendancePage;
use App\Livewire\Operations\ClientCategoriesPage;
use App\Livewire\Operations\ClientDocumentsPage;
use App\Livewire\Operations\ClientFormPage;
use App\Livewire\Operations\ClientsPage;
use App\Livewire\Operations\GuardDocumentsPage;
use App\Livewire\Operations\GuardRefereesPage;
use App\Livewire\Operations\SecurityGuardFormPage;
use App\Livewire\Operations\SecurityGuardsPage;
use App\Livewire\Operations\UndeployedGuardsPage;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::livewire('/', Dashboard::class)->name('dashboard');
    Route::redirect('/dashboard', '/');
    Route::redirect('/home', '/')->name('home');

    Route::livewire('/users', UsersPage::class)->name('users.index');
    Route::livewire('/roles', RolesPage::class)->name('roles.index');
    Route::livewire('/permissions', PermissionsPage::class)->name('permissions.index');
    Route::livewire('/modes', PaymentModesPage::class)->name('modes.index');
    Route::livewire('/audits', AuditLogsPage::class)->name('audits.index');

    Route::livewire('/view_clients', ClientsPage::class)->name('clients.index');
    Route::get('/new_client', ClientFormPage::class)->name('clients.create');
    Route::get('/client/profile/{client}', ClientFormPage::class)->name('clients.edit');
    Route::livewire('/client_documents', ClientDocumentsPage::class)->name('client-documents.index');
    Route::get('/client_documents/{document}/download', ClientDocumentDownloadController::class)->name('client-documents.download');
    Route::livewire('/client_categories', ClientCategoriesPage::class)->name('client-categories.index');
    Route::livewire('/view_guards', SecurityGuardsPage::class)->name('guards.index');
    Route::get('/new_guard', SecurityGuardFormPage::class)->name('guards.create');
    Route::get('/guard/profile/{guard}', SecurityGuardFormPage::class)->name('guards.edit');
    Route::livewire('/guard_documents', GuardDocumentsPage::class)->name('guard-documents.index');
    Route::get('/guard_documents/{document}/download', GuardDocumentDownloadController::class)->name('guard-documents.download');
    Route::livewire('/guard_referees', GuardRefereesPage::class)->name('guard-referees.index');
    Route::livewire('/guards/undeployed', UndeployedGuardsPage::class)->name('guards.undeployed');
    Route::livewire('/assign_guard', AssignmentsPage::class)->name('assignments.index');
    Route::livewire('/client_attendance', AttendancePage::class)->name('attendance.index');
    Route::livewire('/attendance_summary', AttendanceSummaryPage::class)->name('attendance.summary');
    Route::redirect('/reports/attendance', '/attendance_summary');

    Route::redirect('/clients', '/view_clients');
    Route::redirect('/security_guards', '/view_guards');
    Route::redirect('/attendance', '/client_attendance');
});

require __DIR__.'/settings.php';
