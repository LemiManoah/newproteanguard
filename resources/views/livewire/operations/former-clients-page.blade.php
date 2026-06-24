<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Former Clients') }}</flux:heading>
            <flux:text>{{ __('Inactive clients with their left date and reason.') }}</flux:text>
        </div>

        <flux:button :href="route('clients.index')" variant="ghost" wire:navigate>
            {{ __('Active Clients') }}
        </flux:button>
    </div>

    <div class="grid gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 lg:grid-cols-3">
        <flux:input
            wire:model.live.debounce.350ms="search"
            icon="magnifying-glass"
            label="{{ __('Search') }}"
            placeholder="{{ __('Name, contact, email') }}"
        />

        <flux:field>
            <flux:label>{{ __('Category') }}</flux:label>
            <x-searchable-select
                wire:model.live="categoryId"
                :options="$categories"
                placeholder="All categories"
            />
            <flux:error name="categoryId" />
        </flux:field>

        <div class="flex items-end">
            <flux:button wire:click="resetFilters" variant="ghost" icon="x-mark">
                {{ __('Reset') }}
            </flux:button>
        </div>
    </div>

    <flux:table :paginate="$clients" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Contact 1') }}</flux:table.column>
            <flux:table.column>{{ __('Contact 2') }}</flux:table.column>
            <flux:table.column>{{ __('Category') }}</flux:table.column>
            <flux:table.column>{{ __('Requested Guards') }}</flux:table.column>
            <flux:table.column>{{ __('Left Date') }}</flux:table.column>
            <flux:table.column>{{ __('Left Reason') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($clients as $client)
                <flux:table.row :key="$client->id">
                    <flux:table.cell>{{ $clients->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $client->name }}</flux:table.cell>
                    <flux:table.cell>{{ $client->contact1 }}</flux:table.cell>
                    <flux:table.cell>{{ $client->contact2 }}</flux:table.cell>
                    <flux:table.cell>{{ $client->category?->name ?? __('Uncategorised') }}</flux:table.cell>
                    <flux:table.cell>{{ number_format((float) $client->no_guards) }}</flux:table.cell>
                    <flux:table.cell>{{ $client->left_date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>{{ $client->left_reason }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            <flux:button :href="route('clients.edit', $client)" size="sm" variant="ghost" wire:navigate>
                                {{ __('Profile') }}
                            </flux:button>

                            @if ($canRestore)
                                <flux:button wire:click="restore({{ $client->id }})" size="sm" variant="primary">
                                    {{ __('Restore') }}
                                </flux:button>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="9">
                        <div class="py-8 text-center text-sm text-zinc-500">
                            {{ __('No former clients match the selected filters.') }}
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
