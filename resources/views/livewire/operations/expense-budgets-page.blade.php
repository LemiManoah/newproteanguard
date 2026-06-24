<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Expense Budgets') }}</flux:heading>
            <flux:text>{{ __('Set planned expense amounts by category and financial year.') }}</flux:text>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('New Budget') }}</flux:button>
    </div>

    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 lg:w-80">
        <flux:field>
            <flux:label>{{ __('Financial Year') }}</flux:label>
            <x-searchable-select wire:model="yearFilter" :options="$financialYears" placeholder="All years" />
        </flux:field>
    </div>

    <flux:table :paginate="$budgets" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Category') }}</flux:table.column>
            <flux:table.column>{{ __('Financial Year') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Amount') }}</flux:table.column>
            <flux:table.column>{{ __('Created') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($budgets as $budget)
                <flux:table.row :key="$budget->id">
                    <flux:table.cell>{{ $budgets->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $budget->category?->name }}</flux:table.cell>
                    <flux:table.cell>{{ $budget->financialYear?->name }}</flux:table.cell>
                    <flux:table.cell align="end">{{ number_format((float) $budget->amount, 2) }}</flux:table.cell>
                    <flux:table.cell>{{ $budget->created_at?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            <flux:button wire:click="edit({{ $budget->id }})" size="sm">{{ __('Edit') }}</flux:button>
                            <flux:button wire:click="delete({{ $budget->id }})" wire:confirm="{{ __('Are you sure you want to delete this budget?') }}" size="sm" variant="danger">{{ __('Delete') }}</flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">
                        <div class="py-8 text-center text-sm text-zinc-500">{{ __('No expense budgets found.') }}</div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showFormModal" class="overflow-visible md:w-[34rem]">
        <form wire:submit="save" class="space-y-5">
            <flux:heading size="lg">{{ $editingId ? __('Edit Budget') : __('New Expense Budget') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Expense Category') }}</flux:label>
                <x-searchable-select wire:model="categoryId" :options="$categories" placeholder="Select category" dropdown-inline />
                <flux:error name="categoryId" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Financial Year') }}</flux:label>
                <x-searchable-select wire:model="yearId" :options="$financialYears" placeholder="Select financial year" dropdown-inline />
                <flux:error name="yearId" />
            </flux:field>

            <flux:input wire:model="amount" type="number" step="0.01" min="0" label="{{ __('Amount') }}" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Close') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Submit') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
