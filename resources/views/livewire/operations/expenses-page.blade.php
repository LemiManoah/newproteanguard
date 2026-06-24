<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Expenses') }}</flux:heading>
            <flux:text>{{ __('Record and review company expenses.') }}</flux:text>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('Record Expense') }}</flux:button>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-2xl font-semibold">{{ number_format($totalExpenses, 2) }}</div>
            <div class="text-sm text-zinc-500">{{ __('Total Expenses') }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-2xl font-semibold">{{ number_format($monthExpenses, 2) }}</div>
            <div class="text-sm text-zinc-500">{{ __('This Month') }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-2xl font-semibold">{{ number_format($budgetTotal, 2) }}</div>
            <div class="text-sm text-zinc-500">{{ __('Budgeted') }}</div>
        </div>
    </div>

    <div class="grid gap-3 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700 md:grid-cols-2 xl:grid-cols-7">
        <div class="lg:col-span-2">
            <flux:input wire:model.live.debounce.350ms="search" icon="magnifying-glass" label="{{ __('Search') }}" placeholder="{{ __('Category, mode, description') }}" />
        </div>

        <flux:field>
            <flux:label>{{ __('Category') }}</flux:label>
            <x-searchable-select wire:model="categoryFilter" :options="$categories" placeholder="All categories" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Payment Mode') }}</flux:label>
            <x-searchable-select wire:model="modeFilter" :options="$paymentModes" placeholder="All modes" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Financial Year') }}</flux:label>
            <x-searchable-select wire:model="yearFilter" :options="$financialYears" placeholder="All years" />
        </flux:field>

        <flux:input wire:model.live="startDate" type="date" label="{{ __('From') }}" />
        <flux:input wire:model.live="endDate" type="date" label="{{ __('To') }}" />
    </div>

    <flux:table :paginate="$expenses" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column>{{ __('Category') }}</flux:table.column>
            <flux:table.column>{{ __('Mode') }}</flux:table.column>
            <flux:table.column>{{ __('Description') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Amount') }}</flux:table.column>
            <flux:table.column>{{ __('Financial Year') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($expenses as $expense)
                <flux:table.row :key="$expense->id">
                    <flux:table.cell>{{ $expenses->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell>{{ $expense->date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $expense->category?->name }}</flux:table.cell>
                    <flux:table.cell>{{ $expense->mode?->name }}</flux:table.cell>
                    <flux:table.cell>{{ $expense->description }}</flux:table.cell>
                    <flux:table.cell align="end">{{ number_format((float) $expense->amount, 2) }}</flux:table.cell>
                    <flux:table.cell>{{ $expense->financialYear?->name }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            <flux:button wire:click="edit({{ $expense->id }})" size="sm">{{ __('Edit') }}</flux:button>
                            <flux:button wire:click="delete({{ $expense->id }})" wire:confirm="{{ __('Are you sure you want to delete this expense?') }}" size="sm" variant="danger">{{ __('Delete') }}</flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8">
                        <div class="py-8 text-center text-sm text-zinc-500">{{ __('No expenses match the selected filters.') }}</div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showFormModal" class="overflow-visible md:w-[34rem]">
        <form wire:submit="save" class="flex max-h-[85vh] flex-col gap-5">
            <flux:heading size="lg" class="shrink-0">{{ $editingId ? __('Edit Expense') : __('Record Expense') }}</flux:heading>

            <div class="space-y-5 overflow-y-auto px-1 pb-1">
                <flux:field>
                    <flux:label>{{ __('Expense Category') }}</flux:label>
                    <x-searchable-select wire:model="categoryId" :options="$categories" placeholder="Select category" dropdown-inline />
                    <flux:error name="categoryId" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Payment Mode') }}</flux:label>
                    <x-searchable-select wire:model="modeId" :options="$paymentModes" placeholder="Select payment mode" dropdown-inline />
                    <flux:error name="modeId" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Financial Year') }}</flux:label>
                    <x-searchable-select wire:model="yearId" :options="$financialYears" placeholder="Select financial year" dropdown-inline />
                    <flux:error name="yearId" />
                </flux:field>

                <flux:input wire:model="date" type="date" label="{{ __('Date') }}" />
                <flux:input wire:model="amount" type="number" step="0.01" min="0.01" label="{{ __('Amount') }}" />
                <flux:textarea wire:model="description" label="{{ __('Description') }}" rows="3" />
            </div>

            <div class="flex shrink-0 gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Close') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Submit') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
