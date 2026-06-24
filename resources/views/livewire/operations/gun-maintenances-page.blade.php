<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Maintenance Records') }}</flux:heading>
            <flux:text>{{ __('Gun maintenance records and service notes.') }}</flux:text>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('Add Record') }}</flux:button>
    </div>

    <flux:table :paginate="$records" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column>{{ __('Gun') }}</flux:table.column>
            <flux:table.column>{{ __('Worked On By') }}</flux:table.column>
            <flux:table.column>{{ __('description') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($records as $record)
                <flux:table.row :key="$record->id">
                    <flux:table.cell>{{ $records->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell>{{ $record->date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $record->gun?->mark_number }}</flux:table.cell>
                    <flux:table.cell>{{ $record->work_by }}</flux:table.cell>
                    <flux:table.cell>{{ $record->description }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button wire:click="delete({{ $record->id }})" wire:confirm="{{ __('Are you sure you want to delete this maintenance record?') }}" size="sm" variant="danger">{{ __('Delete') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">
                        <div class="py-8 text-center text-sm text-zinc-500">{{ __('No maintenance records found.') }}</div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showFormModal" class="overflow-visible md:w-[34rem]">
        <form wire:submit="save" class="space-y-5">
            <flux:heading size="lg">{{ __('Add Maintenance Record') }}</flux:heading>
            <flux:field>
                <flux:label>{{ __('Gun') }}</flux:label>
                <x-searchable-select
                    wire:model="gunId"
                    :options="$guns"
                    placeholder="Select gun"
                    dropdown-inline
                />
                <flux:error name="gunId" />
            </flux:field>
            <flux:input wire:model="date" type="date" label="{{ __('Date') }}" />
            <flux:input wire:model="workBy" label="{{ __('Worked On By') }}" />
            <flux:input wire:model="description" label="{{ __('Description') }}" />
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Close') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Submit') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
