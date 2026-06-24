<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Inventory Items') }}</flux:heading>
            <flux:text>{{ __('Manage stock items, categories, units, and current balances.') }}</flux:text>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('New Item') }}</flux:button>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-2xl font-semibold">{{ number_format($itemsCount) }}</div>
            <div class="text-sm text-zinc-500">{{ __('Active Items') }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-2xl font-semibold">{{ number_format($stockValue, 2) }}</div>
            <div class="text-sm text-zinc-500">{{ __('Estimated Stock Value') }}</div>
        </div>
    </div>

    <div class="grid gap-3 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700 md:grid-cols-3">
        <div class="md:col-span-2">
            <flux:input wire:model.live.debounce.350ms="search" icon="magnifying-glass" label="{{ __('Search') }}" placeholder="{{ __('Item name') }}" />
        </div>
        <flux:field>
            <flux:label>{{ __('Category') }}</flux:label>
            <x-searchable-select wire:model="categoryFilter" :options="$categories" placeholder="All categories" />
        </flux:field>
    </div>

    <flux:table :paginate="$items" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Item') }}</flux:table.column>
            <flux:table.column>{{ __('Category') }}</flux:table.column>
            <flux:table.column>{{ __('Unit') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Quantity') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Last Price') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($items as $item)
                <flux:table.row :key="$item->id">
                    <flux:table.cell>{{ $items->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $item->name }}</flux:table.cell>
                    <flux:table.cell>{{ $item->category?->name }}</flux:table.cell>
                    <flux:table.cell>{{ $item->unit?->symbol }}</flux:table.cell>
                    <flux:table.cell align="end">{{ number_format((float) $item->quantity, 2) }}</flux:table.cell>
                    <flux:table.cell align="end">{{ number_format((float) $item->last_buying_price, 2) }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            <flux:button wire:click="edit({{ $item->id }})" size="sm">{{ __('Edit') }}</flux:button>
                            <flux:button wire:click="delete({{ $item->id }})" wire:confirm="{{ __('Are you sure you want to delete this item?') }}" size="sm" variant="danger">{{ __('Delete') }}</flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7">
                        <div class="py-8 text-center text-sm text-zinc-500">{{ __('No inventory items found.') }}</div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showFormModal" class="overflow-visible md:w-[34rem]">
        <form wire:submit="save" class="flex max-h-[85vh] flex-col gap-5">
            <flux:heading size="lg" class="shrink-0">{{ $editingId ? __('Edit Item') : __('New Inventory Item') }}</flux:heading>
            <div class="space-y-5 overflow-y-auto px-1 pb-1">
                <flux:field>
                    <flux:label>{{ __('Category') }}</flux:label>
                    <x-searchable-select wire:model="categoryId" :options="$categories" placeholder="Select category" dropdown-inline />
                    <flux:error name="categoryId" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Unit') }}</flux:label>
                    <x-searchable-select wire:model="unitId" :options="$units" option-label="symbol" placeholder="Select unit" dropdown-inline />
                    <flux:error name="unitId" />
                </flux:field>
                <flux:input wire:model="name" label="{{ __('Item Name') }}" />
                <flux:input wire:model="openingStock" type="number" step="0.01" min="0" label="{{ __('Opening Stock') }}" />
                <flux:input wire:model="quantity" type="number" step="0.01" min="0" label="{{ __('Current Quantity') }}" />
                <flux:input wire:model="buyingPrice" type="number" step="0.01" min="0" label="{{ __('Buying Price') }}" />
            </div>
            <div class="flex shrink-0 gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Close') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Submit') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
