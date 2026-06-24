# System Design Suggestions

These are practical improvements to consider as the new Proteanguard app continues moving module by module from the old system.

## User Interface

- Standardize all module index pages around the same structure: page title, short helper text, action button, compact filters, Flux table, and modal or inline workflow where appropriate.
- Keep database-backed dropdowns on the searchable select package everywhere. This avoids blank/default-selected dropdown confusion and handles larger datasets better.
- Make all long modals scroll internally with a capped viewport height, for example `max-h-[85vh]`, so forms never fall below the screen.
- Use inline workflows for cart-style operations such as stock in, billing batches, payroll generation, and inventory reconciliation. Modals work better for single-record actions.
- Keep filter fields compact. Filters should support the table, not dominate the page.
- Add empty states with useful next actions, for example “No stock items found” plus “New Item” where it makes sense.
- Add consistent badges for statuses such as active, former, assigned, available, paid, unpaid, approved, and pending.

## Navigation

- Keep the sidebar in the old-system order, but group related actions under clear module names.
- Add route redirects for legacy URLs where possible so old bookmarks still land on the new pages.
- Consider adding a lightweight global search later for clients, guards, invoices, inventory items, and expenses.

## Data And Workflow

- Prefer service classes for workflows that update more than one table. Good examples are attendance generation, gun/bullet movements, expense summaries, and inventory stock movements.
- Keep stock movement tables as the audit trail and item quantity as the current balance. Whenever quantity changes, a movement record should be written.
- For cart workflows, keep cart rows scoped by `businessId` and `userId`, then clear the cart only after a successful transaction.
- Add validation that prevents destructive deletes where related financial/audit records already exist. Soft-deactivate is safer for setup data like categories, units, guards, and clients.
- For old database compatibility, avoid renaming legacy columns unless migrations and models cover both old and new names clearly.

## Permissions

- Review broad permissions such as `manage_inventory`. Splitting it into view, record stock, issue stock, edit setup, and delete permissions would better match the older module permissions.
- Hide action buttons as well as blocking the routes. A user should not see controls they cannot use.
- Add tests for forbidden actions, not only forbidden page access.

## Tables And Reporting

- Use Flux tables for every module table to keep pagination and layout uniform.
- Add export buttons only after the table filters are stable, so exports match what the user is seeing.
- Add totals to financial and stock pages: filtered totals, current month totals, balance totals, and count summaries.
- Add print-friendly versions for pages the old system likely printed often, such as invoices, attendance summaries, stock movements, payroll, and guard profiles.

## Reliability

- Add transaction wrappers around workflows that create parent/detail records, especially stock in, payroll, billing, expenses with ledger entries, and assignments.
- Add tests for seeded demo access, restricted permissions, and the main create/update/delete workflow for each module as it is ported.
- Add database indexes for common filters: `businessId + date`, `businessId + status`, `businessId + categoryId`, and `businessId + itemId`.
- Add consistent tenant checks on every edit/delete/download route.

## Accounting Integration

- Expenses, client payments, stock purchases, payroll, and billing should eventually write consistent ledger entries.
- Avoid duplicating accounting logic inside Livewire pages. Put posting logic in services so it can be tested independently.
- Add reconciliation views only after money-in and money-out posting rules are stable.

## Mobile Responsiveness

- Keep forms single-column on mobile and avoid dense multi-column layouts below tablet width.
- Tables with many columns should either wrap key fields into stacked cells or prioritize the old-system critical columns first.
- Keep action buttons small and grouped, but avoid cramming too many actions into one row on mobile.

## Technical Cleanup

- Fix legacy spelling mismatches gradually, but preserve compatibility where old database column names are already in use.
- Add small accessor methods where models need display names, for example guards, guns, payment modes, and inventory items.
- Consider reusable table filter components once at least four or five modules share the same shape.
- Add a shared modal form shell for scrollable modals so the fix does not have to be repeated manually.
