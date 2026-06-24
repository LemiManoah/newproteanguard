# Client Module

## Old System Menu Order

The old Clients sidebar contained these items in this exact order:

1. New Client
2. Clients
3. Assign Guard
4. Client Schedule
5. Guard Cycle
6. Categories
7. Former Clients

The migrated sidebar now follows this order and each item routes to a real migrated page.

## What The Module Does

The client module manages customer accounts that need security guard coverage. A client record stores contact details, billing setup, requested guard count, schedule preference, documents, and the active guard deployments attached to the client.

The important workflow is:

1. Create a client with billing and requested guard details.
2. Assign available guards to the client.
3. Review current deployments/schedules.
4. Track attendance daily from those deployments.
5. Move inactive clients into Former Clients instead of hard-deleting them.

## Old System Client List Fields

The old `Clients` index table showed:

- S/N
- Name
- Contact 1
- Contact 2
- Category
- Requested Guards
- Allocated Guards
- Action

The action opened the client profile and allowed marking the client inactive.

## Old System New/Edit Client Fields

Basic Info:

- Name
- Contact 1
- Contact 2
- Email
- ID Number
- TIN
- Address
- Category
- Schedule: Day, Night, Full time

Billing Information:

- Billing Cycle: Monthly, One time
- Number of Guards
- Billing Amount
- Billing Start Date

The profile edit screen also exposed quarterly and annual billing options.

## Old System Client Profile Areas

The old profile page had:

- Profile summary card: name, category, email, contacts, address, recorded date.
- Profile tab: bio data and billing information.
- Guards tab: assigned guards table with S/N, Name, Contact 1, Schedule Type.
- Edit Info tab: full client edit form.
- Documents tab: documents table with S/N, Category, File, plus an upload modal.

## Old System Client Schedule Fields

The old `Client Schedule` table showed:

- S/N
- Client
- Guard
- Start Date
- Period
- Category
- Added On
- Action

The action removed the guard from the client.

## Old System Guard Cycle

`Guard Cycle` filtered deployments by client and showed the guard assignment history for that client. This is related to `client_guards`, not daily attendance.

## Implemented In The Migrated App

- Client list route: `/view_clients`
- New client route: `/new_client`
- Client profile/edit route: `/client/profile/{client}`
- Assign guard route: `/assign_guard`
- Client categories route: `/client_categories`
- Client documents route: `/client_documents`
- Client schedule route: `/client_schedule`
- Guard cycle route: `/client_cycle`
- Former clients route: `/former_clients`
- Client model relationships: category, guards, activeGuards, attendances, documents, activeDocuments.
- Client create/edit form using Flux inputs/selects.
- Create-client page is organized like the old system, with `Basic Info` and `Billing information` sections.
- Client list uses the old system columns with Flux table, filters, pagination, and Profile/Remove actions.
- Assignment screen already uses Flux form components and Flux table.
- Client profile follows the old system tab order: Profile, Guards, Edit Info, Documents.
- Client profile uses Flux tables for assigned guards and documents.
- Client document upload is available from the profile Documents tab for users who can edit clients.
- Client create/update, document upload, and client deactivation use global Flux toast notifications.
- Required dropdowns now start empty with placeholder text before the user makes a choice.
- Client index filter dropdowns use explicit `All ...` options for the empty state.
- Database-backed dropdowns use the William searchable select component (`x-searchable-select`) instead of `flux:select`.
- Former Clients page is implemented with Flux table, filters, left date, left reason, and restore action.
- Client Schedule page is implemented with the old schedule fields and remove-guard action.
- Guard Cycle page is implemented with a required client selector and deployment history table.
- Moving a client to Former Clients now captures left date and left reason, closes active guard deployments, and releases assigned guards.

## Partially Implemented

- The standalone Client Documents page still exists, but the old-system-style document workflow now also lives inside the client profile Documents tab.
- Client Schedule currently removes guards with a confirmation prompt, not a richer modal.
- Guard Cycle currently focuses on deployment history. It does not yet include attendance-derived replacement cycles.

## Left To Build

1. Review all client-related dropdowns:
   - Required creation/edit dropdowns must start empty.
   - Use placeholder text such as `Select Category`, `Select Schedule`, or `Select Billing Cycle`.
   - Filter dropdowns must use explicit all-state labels such as `All categories` or `All clients`.
   - Avoid defaulting to the first real database record.
   - Use `x-searchable-select` for database-backed options such as clients, guards, categories, and deployments.
   - Use `flux:select` only for static/enumerated options such as schedule type, billing cycle, duty type, and status.

2. Decide whether Guard Cycle should include daily attendance replacements as well as deployment history.

3. Consider upgrading Client Schedule remove action from `wire:confirm` to a Flux modal if the business needs removal reason/date beyond today's date.

4. Add focused feature tests for:
   - Creating a client.
   - Marking a client former.
   - Restoring a former client.
   - Removing a guard from a client schedule.
   - Viewing guard cycle records by client.

## Flux UI Plan

Use Flux components wherever available:

- `flux:table` for all client, schedule, guard-cycle, document, and profile tables.
- `flux:input` for text, number, date, and search fields.
- `flux:select` for category, schedule, billing cycle, status, and client filters.
- `x-searchable-select` for all database-backed selections such as category, client, guard, and deployment.
- `flux:select` for static/enumerated selections such as schedule, billing cycle, status, and duty type.
- Required `flux:select` controls should include an empty option first so the placeholder is visible and validation is honest.
- Filter controls should include an explicit all-state label such as `All categories` or `All clients`.
- `flux:button` for actions.
- `flux:modal` for destructive confirmations, document upload, and mark-former-client flow.
- `flux:badge` for active/inactive, assigned/unassigned, and allocation states.
- `Flux::toast()` for global success/error notifications.

## Suggested Build Order

1. Run migrations and route tests locally.
2. Add focused Livewire feature tests for client workflows.
3. Review Guard Cycle scope against real business usage.
4. Polish Client Schedule removal into a full modal if needed.

## Local Verification Commands

```powershell
cd C:\Users\Manoah\Desktop\projects\work\guard\newproteanguard

php artisan migrate

php vendor\bin\pint app\Livewire\Operations\ClientsPage.php app\Livewire\Operations\ClientFormPage.php app\Livewire\Operations\FormerClientsPage.php app\Livewire\Operations\ClientSchedulePage.php app\Livewire\Operations\GuardCyclePage.php app\Livewire\Operations\AssignmentsPage.php routes\web.php resources\views\layouts\app\sidebar.blade.php --format agent

php -l app\Livewire\Operations\ClientsPage.php
php -l app\Livewire\Operations\ClientFormPage.php
php -l app\Livewire\Operations\FormerClientsPage.php
php -l app\Livewire\Operations\ClientSchedulePage.php
php -l app\Livewire\Operations\GuardCyclePage.php
php -l app\Livewire\Operations\AssignmentsPage.php

php artisan route:list --name=clients
php artisan route:list --name=assignments
php artisan test --compact tests\Feature\OperationsRoutesTest.php
```
