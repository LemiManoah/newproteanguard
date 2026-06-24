<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Bullet Usage') }}</flux:heading>
            <flux:text>{{ __('Record bullets used by guards.') }}</flux:text>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('Record Usage') }}</flux:button>
    </div>

    <flux:table :paginate="$used" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column>{{ __('Gun') }}</flux:table.column>
            <flux:table.column>{{ __('Security Guard') }}</flux:table.column>
            <flux:table.column>{{ __('Bullets Used') }}</flux:table.column>
            <flux:table.column>{{ __('description') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($used as $usage)
                <flux:table.row :key="$usage->id">
                    <flux:table.cell>{{ $used->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell>{{ $usage->date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $usage->gun?->mark_number }}</flux:table.cell>
                    <flux:table.cell>{{ $usage->securityGuard?->fname }} {{ $usage->securityGuard?->lname }}</flux:table.cell>
                    <flux:table.cell>{{ number_format((int) $usage->quantity) }}</flux:table.cell>
                    <flux:table.cell>{{ $usage->description }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button wire:click="delete({{ $usage->id }})" wire:confirm="{{ __('Are you sure you want to delete this bullet usage?') }}" size="sm" variant="danger">{{ __('Delete') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7">
                        <div class="py-8 text-center text-sm text-zinc-500">{{ __('No bullet usage recorded.') }}</div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showFormModal" class="overflow-visible md:w-[34rem]">
        <form wire:submit="save" class="space-y-5">
            <flux:heading size="lg">{{ __('Record Bullet Usage') }}</flux:heading>
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
            <flux:field>
                <flux:label>{{ __('Security Guard') }}</flux:label>
                <x-searchable-select
                    wire:model="guardId"
                    :options="$guards"
                    placeholder="Select security guard"
                    dropdown-inline
                />
                <flux:error name="guardId" />
            </flux:field>
            <flux:input wire:model="date" type="date" label="{{ __('Date') }}" />
            <flux:input wire:model="quantity" type="number" min="1" label="{{ __('Bullets Used') }}" />
            <flux:input wire:model="description" label="{{ __('Description') }}" />
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Close') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Submit') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
