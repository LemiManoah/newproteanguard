<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Guns') }}</flux:heading>
            <flux:text>{{ __('Armory gun register, bullet balances, and availability.') }}</flux:text>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('Add Gun') }}</flux:button>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-2xl font-semibold">{{ number_format($allGunsCount) }}</div>
            <div class="text-sm text-zinc-500">{{ __('All guns') }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-2xl font-semibold">{{ number_format($assignedGunsCount) }}</div>
            <div class="text-sm text-zinc-500">{{ __('Assigned Guns') }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-2xl font-semibold">{{ number_format($availableGunsCount) }}</div>
            <div class="text-sm text-zinc-500">{{ __('Available Guns') }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-2xl font-semibold">{{ number_format($availableBulletsCount) }}</div>
            <div class="text-sm text-zinc-500">{{ __('Available Bullets') }}</div>
        </div>
    </div>

    <div class="grid gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 lg:grid-cols-4">
        <div class="lg:col-span-2">
            <flux:input wire:model.live.debounce.350ms="search" icon="magnifying-glass" label="{{ __('Search') }}" placeholder="{{ __('Type, serial number, mark number') }}" />
        </div>

        <flux:select wire:model.live="availability" label="{{ __('Status') }}" placeholder="{{ __('All guns') }}">
            <flux:select.option value="">{{ __('All guns') }}</flux:select.option>
            <flux:select.option value="1">{{ __('Available') }}</flux:select.option>
            <flux:select.option value="0">{{ __('Assigned') }}</flux:select.option>
        </flux:select>
    </div>

    <flux:table :paginate="$guns" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Mark Number') }}</flux:table.column>
            <flux:table.column>{{ __('Type') }}</flux:table.column>
            <flux:table.column>{{ __('Serial Number') }}</flux:table.column>
            <flux:table.column>{{ __('Opening Bullets') }}</flux:table.column>
            <flux:table.column>{{ __('Available Bullets') }}</flux:table.column>
            <flux:table.column>{{ __('Ownership') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($guns as $gun)
                <flux:table.row :key="$gun->id">
                    <flux:table.cell>{{ $guns->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $gun->mark_number }}</flux:table.cell>
                    <flux:table.cell>{{ $gun->type }}</flux:table.cell>
                    <flux:table.cell>{{ $gun->serial_number }}</flux:table.cell>
                    <flux:table.cell>{{ number_format((int) $gun->bullets) }}</flux:table.cell>
                    <flux:table.cell>{{ number_format($gun->available_bullets) }}</flux:table.cell>
                    <flux:table.cell>{{ $gun->owner?->label() }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="{{ $gun->available->value === 1 ? 'green' : 'amber' }}">
                            {{ $gun->available->value === 1 ? __('Available') : __('Assigned') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            <flux:button wire:click="edit({{ $gun->id }})" size="sm">{{ __('Edit') }}</flux:button>
                            <flux:button wire:click="delete({{ $gun->id }})" wire:confirm="{{ __('Are you sure you want to delete this gun?') }}" size="sm" variant="danger">{{ __('Delete') }}</flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="9">
                        <div class="py-8 text-center text-sm text-zinc-500">{{ __('No guns match the selected filters.') }}</div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showFormModal" class="md:w-[34rem]">
        <form wire:submit="save" class="space-y-5">
            <flux:heading size="lg">{{ $editingId ? __('Edit Gun') : __('Add New Gun') }}</flux:heading>
            <flux:input wire:model="type" label="{{ __('Gun Type') }}" />
            <flux:input wire:model="serialNumber" label="{{ __('Serial Number') }}" />
            <flux:input wire:model="markNumber" label="{{ __('Mark Number') }}" />
            <flux:input wire:model="bullets" type="number" min="0" label="{{ __('Bullets Assigned') }}" />
            <flux:select wire:model="owner" label="{{ __('Ownership') }}" placeholder="{{ __('Select ownership') }}">
                <flux:select.option value="">{{ __('Select ownership') }}</flux:select.option>
                @foreach ($owners as $ownerOption)
                    <flux:select.option value="{{ $ownerOption->value }}">{{ $ownerOption->label() }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input wire:model="vendorName" label="{{ __('Vendor Name') }}" />
            <flux:input wire:model="vendorContact" label="{{ __('Vendor Contact') }}" />
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Close') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Submit') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
