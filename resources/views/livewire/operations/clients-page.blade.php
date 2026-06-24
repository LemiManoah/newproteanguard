<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('List Of Clients') }}</flux:heading>
            <flux:text>{{ __('Active clients, requested guard coverage, and allocated guards.') }}</flux:text>
        </div>

        <flux:button :href="route('clients.create')" variant="primary" icon="plus" wire:navigate>
            {{ __('New Client') }}
        </flux:button>
    </div>

    <div class="grid gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 lg:grid-cols-4">
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

        <flux:select wire:model.live="allocation" label="{{ __('Allocation') }}" placeholder="{{ __('All clients') }}">
            <flux:select.option value="">{{ __('All clients') }}</flux:select.option>
            <flux:select.option value="unassigned">{{ __('Unassigned') }}</flux:select.option>
            <flux:select.option value="under">{{ __('Under allocated') }}</flux:select.option>
            <flux:select.option value="full">{{ __('Fully allocated') }}</flux:select.option>
        </flux:select>

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
            <flux:table.column>{{ __('Allocated Guards') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($clients as $client)
                <flux:table.row :key="$client->id">
                    <flux:table.cell>{{ $clients->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell variant="strong">
                        <div>{{ $client->name }}</div>
                        <div class="text-xs font-normal text-zinc-500">{{ $client->email }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $client->contact1 }}</flux:table.cell>
                    <flux:table.cell>{{ $client->contact2 }}</flux:table.cell>
                    <flux:table.cell>{{ $client->category?->name ?? __('Uncategorised') }}</flux:table.cell>
                    <flux:table.cell>{{ number_format((float) $client->no_guards) }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="{{ $client->active_guards_count >= (float) $client->no_guards ? 'green' : 'amber' }}">
                            {{ $client->active_guards_count }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            <flux:button :href="route('clients.edit', $client)" size="sm" variant="primary" wire:navigate>
                                {{ __('Profile') }}
                            </flux:button>

                            @if ($canDelete)
                                <flux:button
                                    wire:click="markFormer({{ $client->id }})"
                                    size="sm"
                                    variant="danger"
                                >
                                    {{ __('Remove') }}
                                </flux:button>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8">
                        <div class="py-8 text-center text-sm text-zinc-500">
                            {{ __('No active clients match the selected filters.') }}
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showFormerModal" class="md:w-[32rem]">
        <form wire:submit="confirmFormer" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Move Client to Former Clients') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ __('Record why :name is no longer active.', ['name' => $formerClientName ?? __('this client')]) }}
                </flux:text>
            </div>

            <flux:input wire:model="leftDate" type="date" label="{{ __('Left Date') }}" />
            <flux:textarea wire:model="leftReason" label="{{ __('Left Reason') }}" rows="3" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger">{{ __('Confirm') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
