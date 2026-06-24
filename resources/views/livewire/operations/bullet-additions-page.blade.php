<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Bullet Additions') }}</flux:heading>
            <flux:text>{{ __('Record bullets added to guns.') }}</flux:text>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('Add Bullets') }}</flux:button>
    </div>

    <flux:table :paginate="$additions" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column>{{ __('Gun') }}</flux:table.column>
            <flux:table.column>{{ __('Bullets Added') }}</flux:table.column>
            <flux:table.column>{{ __('Brought By') }}</flux:table.column>
            <flux:table.column>{{ __('description') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($additions as $add)
                <flux:table.row :key="$add->id">
                    <flux:table.cell>{{ $additions->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell>{{ $add->date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $add->gun?->mark_number }}</flux:table.cell>
                    <flux:table.cell>{{ number_format((int) $add->quantity) }}</flux:table.cell>
                    <flux:table.cell>{{ $add->brought_by }}</flux:table.cell>
                    <flux:table.cell>{{ $add->description }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button wire:click="delete({{ $add->id }})" wire:confirm="{{ __('Are you sure you want to delete these added bullets?') }}" size="sm" variant="danger">{{ __('Delete') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7">
                        <div class="py-8 text-center text-sm text-zinc-500">{{ __('No bullet additions recorded.') }}</div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showFormModal" class="overflow-visible md:w-[34rem]">
        <form wire:submit="save" class="space-y-5">
            <flux:heading size="lg">{{ __('Add Bullets') }}</flux:heading>
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
            <flux:input wire:model="quantity" type="number" min="1" label="{{ __('Bullets Added') }}" />
            <flux:input wire:model="broughtBy" label="{{ __('Brought By') }}" />
            <flux:input wire:model="description" label="{{ __('Description') }}" />
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Close') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Submit') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
