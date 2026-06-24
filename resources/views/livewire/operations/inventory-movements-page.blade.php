<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Inventory Movements') }}</flux:heading>
        <flux:text>{{ __('Audit trail for opening stock, stock in, and usage.') }}</flux:text>
    </div>

    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700 lg:w-96">
        <flux:field>
            <flux:label>{{ __('Item') }}</flux:label>
            <x-searchable-select wire:model="itemFilter" :options="$items" placeholder="All items" />
        </flux:field>
    </div>

    <flux:table :paginate="$movements" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column>{{ __('Item') }}</flux:table.column>
            <flux:table.column>{{ __('Type') }}</flux:table.column>
            <flux:table.column align="end">{{ __('In') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Out') }}</flux:table.column>
            <flux:table.column>{{ __('Description') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($movements as $movement)
                <flux:table.row :key="$movement->id">
                    <flux:table.cell>{{ $movements->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell>{{ $movement->date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $movement->item?->name }}</flux:table.cell>
                    <flux:table.cell>{{ $movement->type?->label() }}</flux:table.cell>
                    <flux:table.cell align="end">{{ number_format((float) $movement->quantity_in, 2) }}</flux:table.cell>
                    <flux:table.cell align="end">{{ number_format((float) $movement->quantity_out, 2) }}</flux:table.cell>
                    <flux:table.cell>{{ $movement->description }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="7"><div class="py-8 text-center text-sm text-zinc-500">{{ __('No inventory movements found.') }}</div></flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
