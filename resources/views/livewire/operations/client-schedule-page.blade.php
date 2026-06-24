<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('CLIENT SCHEDULES') }}</flux:heading>
        <flux:text>{{ __('Active guard deployments by client.') }}</flux:text>
    </div>

    <div class="grid gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 lg:grid-cols-3">
        <flux:input
            wire:model.live.debounce.350ms="search"
            icon="magnifying-glass"
            label="{{ __('Search') }}"
            placeholder="{{ __('Client, guard, code') }}"
        />

        <flux:field>
            <flux:label>{{ __('Client') }}</flux:label>
            <x-searchable-select
                wire:model.live="clientId"
                :options="$clients"
                placeholder="All clients"
            />
            <flux:error name="clientId" />
        </flux:field>

        <div class="flex items-end">
            <flux:button wire:click="$set('search', null); $set('clientId', null)" variant="ghost" icon="x-mark">
                {{ __('Reset') }}
            </flux:button>
        </div>
    </div>

    <flux:table :paginate="$schedules" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Client') }}</flux:table.column>
            <flux:table.column>{{ __('Guard') }}</flux:table.column>
            <flux:table.column>{{ __('Start Date') }}</flux:table.column>
            <flux:table.column>{{ __('Period') }}</flux:table.column>
            <flux:table.column>{{ __('Category') }}</flux:table.column>
            <flux:table.column>{{ __('Added On') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($schedules as $schedule)
                <flux:table.row :key="$schedule->id">
                    <flux:table.cell>{{ $schedules->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $schedule->client?->name }}</flux:table.cell>
                    <flux:table.cell>
                        <div>{{ trim(($schedule->securityGuard?->fname ?? '').' '.($schedule->securityGuard?->lname ?? '')) }}</div>
                        <div class="text-xs text-zinc-500">{{ $schedule->securityGuard?->code }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $schedule->from?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>{{ $schedule->schedule_type?->label() }}</flux:table.cell>
                    <flux:table.cell>{{ $schedule->over_time ? __('Overtime') : __('Duty') }}</flux:table.cell>
                    <flux:table.cell>{{ $schedule->created_at?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell align="end">
                        @if ($canRemove)
                            <flux:button
                                wire:click="removeGuard({{ $schedule->id }})"
                                wire:confirm="{{ __('Are you sure you want to remove this guard from :client?', ['client' => $schedule->client?->name]) }}"
                                size="sm"
                                variant="danger"
                            >
                                {{ __('Remove') }}
                            </flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8">
                        <div class="py-8 text-center text-sm text-zinc-500">
                            {{ __('No active client schedules match the selected filters.') }}
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
