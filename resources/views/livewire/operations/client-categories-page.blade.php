<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Client Categories') }}</flux:heading>
            <flux:text>{{ __('Manage lightweight client grouping values.') }}</flux:text>
        </div>

        <flux:button wire:click="create" variant="primary">{{ __('New Category') }}</flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Clients') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($categories as $category)
                <flux:table.row :key="$category->id">
                    <flux:table.cell>{{ $category->name }}</flux:table.cell>
                    <flux:table.cell>{{ $category->clients_count }}</flux:table.cell>
                    <flux:table.cell>{{ $category->status ? __('Active') : __('Inactive') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button wire:click="edit({{ $category->id }})" size="sm">{{ __('Edit') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showFormModal" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingId ? __('Edit Category') : __('New Category') }}
                </flux:heading>
                <flux:text class="mt-2">{{ __('Used to group clients in operations and reports.') }}</flux:text>
            </div>

            <flux:input wire:model="name" label="{{ __('Name') }}" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
