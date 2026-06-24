<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Stock Usage') }}</flux:heading>
            <flux:text>{{ __('Issue inventory stock to guards or operations.') }}</flux:text>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('Record Usage') }}</flux:button>
    </div>

    <flux:table :paginate="$usages" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column>{{ __('Item') }}</flux:table.column>
            <flux:table.column>{{ __('Guard') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Quantity') }}</flux:table.column>
            <flux:table.column>{{ __('Description') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($usages as $usage)
                <flux:table.row :key="$usage->id">
                    <flux:table.cell>{{ $usages->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell>{{ $usage->date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $usage->item?->name }}</flux:table.cell>
                    <flux:table.cell>{{ $usage->securityGuard?->fname }} {{ $usage->securityGuard?->lname }}</flux:table.cell>
                    <flux:table.cell align="end">{{ number_format((float) $usage->quantity, 2) }} {{ $usage->item?->unit?->symbol }}</flux:table.cell>
                    <flux:table.cell>{{ $usage->description }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button wire:click="delete({{ $usage->id }})" wire:confirm="{{ __('Are you sure you want to delete this usage record?') }}" size="sm" variant="danger">{{ __('Delete') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="7"><div class="py-8 text-center text-sm text-zinc-500">{{ __('No stock usage records found.') }}</div></flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showFormModal" class="overflow-visible md:w-[34rem]">
        <form wire:submit="save" class="flex max-h-[85vh] flex-col gap-5">
            <flux:heading size="lg" class="shrink-0">{{ __('Record Stock Usage') }}</flux:heading>
            <div class="space-y-5 overflow-y-auto px-1 pb-1">
                <flux:field>
                    <flux:label>{{ __('Item') }}</flux:label>
                    <x-searchable-select wire:model="itemId" :options="$items" placeholder="Select item" dropdown-inline />
                    <flux:error name="itemId" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Guard') }}</flux:label>
                    <x-searchable-select wire:model="guardId" :options="$guards" placeholder="Select guard" dropdown-inline />
                    <flux:error name="guardId" />
                </flux:field>
                <flux:input wire:model="date" type="date" label="{{ __('Date') }}" />
                <flux:input wire:model="quantity" type="number" step="0.01" min="0.01" label="{{ __('Quantity') }}" />
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
