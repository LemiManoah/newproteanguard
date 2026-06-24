# Guards Module

## Old System Shape

The old Guards sidebar showed the module in this order:

1. New Guard
2. Guards
3. Former Guards

The old Guards index showed:

- S/N
- First Name
- Last Name
- Contact 1
- Contact 2
- Joining Date
- Deployed
- Action

It also showed dashboard counts for active guards, deployed guards, un deployed guards, and incomplete files, with joining-date filters.

The old guard profile used these tabs:

- Profile
- Clients
- Documents
- Referees
- Update Info

## Implemented

- Sidebar guard menu now follows the old system order: New Guard, Guards, Former Guards.
- Guards index uses Flux table and old-system columns.
- Guards index has useful filters:
  - Search by name, code, contact, or ID number.
  - From and To joining date.
  - Deployment state.
  - File status.
- Guards index has the old summary counters:
  - Active Guards.
  - Deployed Guards.
  - Un deployed Guards.
  - Incomplete Files.
- Moving a guard to Former Guards records leaving date and reason.
- Moving a guard to Former Guards closes active deployments for that guard.
- Former Guards page has been added with Flux table and restore action.
- Create Guard page has been organized into old-system sections:
  - Basic Info.
  - Demographic Info.
  - Parents Info.
  - Other Info.
- Guard profile follows the old tab structure:
  - Profile.
  - Clients.
  - Documents.
  - Referees.
  - Update Info.
- Documents can be uploaded from the guard profile.
- Documents can be verified or marked incomplete from the guard profile.
- Referees can be added or edited from the guard profile.
- Static dropdowns now start empty on create with placeholder text, so the UI does not look like the first item has already been selected.
- All guard tables use Flux table components.

## Database Compatibility

The current implementation is intentionally mapped to the existing database-style fields already present on `security_guards`, including:

- Bio data: name, contacts, email, DOB, height, weight, join date, gender.
- Demographics: nationality, religion, tribe, marital status, address, home contact, home location.
- Parents: father and mother names, contacts, occupations, and life status.
- Next of kin: name, contact, relationship, residence.
- Identification: ID type, ID number, ID expiry date.
- Medical history.
- Status fields: status, assigned, doc_verified, left_date, left_reason.

The module should connect cleanly to migrated existing data as long as these columns exist and enum values match the current Laravel enum values.

## Seeder

`LionGuardSeeder` now contains a small connected demo shape:

- 2 clients.
- 4 guards.
- Active deployments linking the guards to the clients.

This is useful for checking the Guards, Clients, Assign Guard, Client Schedule, Attendance, and Summary flows against related records instead of isolated rows.

## What Is Left

- Run local migrations and seeders against your real local database.
- Run local route/view tests after the Flux UI changes.
- Confirm visual spacing in-browser for desktop and mobile.
- Decide whether the separate Guard Documents, Guard Referees, and Undeployed Guards routes should remain hidden legacy routes or be removed later. They are no longer in the sidebar because the old system exposed documents and referees inside the guard profile.
- Add export behavior if the new system needs the old export button.
- Add deeper tests around marking guards as former, restoring guards, and document verification.

## Local Commands

From `newproteanguard`:

```powershell
php artisan migrate
php artisan db:seed --class=LionGuardSeeder
php artisan route:list --name=guards
php artisan test --compact
vendor\bin\pint --dirty
```

If Herd PHP is not on your PATH, run the same commands through the Herd PHP executable you normally use for this project.
