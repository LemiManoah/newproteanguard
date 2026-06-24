<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Inventory Categories') }}</flux:heading>
            <flux:text>{{ __('Group inventory items for easier stock control.') }}</flux:text>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('New Category') }}</flux:button>
    </div>

    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
        <flux:input wire:model.live.debounce.350ms="search" icon="magnifying-glass" label="{{ __('Search') }}" placeholder="{{ __('Category name') }}" />
    </div>

    <flux:table :paginate="$categories" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Category') }}</flux:table.column>
            <flux:table.column>{{ __('Items') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($categories as $category)
                <flux:table.row :key="$category->id">
                    <flux:table.cell>{{ $categories->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $category->name }}</flux:table.cell>
                    <flux:table.cell>{{ number_format((int) $category->items_count) }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            <flux:button wire:click="edit({{ $category->id }})" size="sm">{{ __('Edit') }}</flux:button>
                            <flux:button wire:click="delete({{ $category->id }})" wire:confirm="{{ __('Are you sure you want to delete this category?') }}" size="sm" variant="danger">{{ __('Delete') }}</flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="4"><div class="py-8 text-center text-sm text-zinc-500">{{ __('No inventory categories found.') }}</div></flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showFormModal" class="md:w-[30rem]">
        <form wire:submit="save" class="space-y-5">
            <flux:heading size="lg">{{ $editingId ? __('Edit Category') : __('New Inventory Category') }}</flux:heading>
            <flux:input wire:model="name" label="{{ __('Category Name') }}" />
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Close') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Submit') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
