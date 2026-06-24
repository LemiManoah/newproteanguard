<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Guard Cycle') }}</flux:heading>
        <flux:text>{{ __('Deployment history for a selected client.') }}</flux:text>
    </div>

    <div class="grid gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 lg:grid-cols-3">
        <flux:field>
            <flux:label>{{ __('Client') }}</flux:label>
            <x-searchable-select
                wire:model.live="clientId"
                :options="$clients"
                placeholder="Select Client"
            />
            <flux:error name="clientId" />
        </flux:field>
    </div>

    <flux:table :paginate="$cycles" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Client') }}</flux:table.column>
            <flux:table.column>{{ __('Guard') }}</flux:table.column>
            <flux:table.column>{{ __('Start Date') }}</flux:table.column>
            <flux:table.column>{{ __('End Date') }}</flux:table.column>
            <flux:table.column>{{ __('Period') }}</flux:table.column>
            <flux:table.column>{{ __('Category') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Added On') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($cycles as $cycle)
                <flux:table.row :key="$cycle->id">
                    <flux:table.cell>{{ $cycles->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $cycle->client?->name }}</flux:table.cell>
                    <flux:table.cell>
                        <div>{{ trim(($cycle->securityGuard?->fname ?? '').' '.($cycle->securityGuard?->lname ?? '')) }}</div>
                        <div class="text-xs text-zinc-500">{{ $cycle->securityGuard?->code }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $cycle->from?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>{{ $cycle->to?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>{{ $cycle->schedule_type?->label() }}</flux:table.cell>
                    <flux:table.cell>{{ $cycle->over_time ? __('Overtime') : __('Duty') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="{{ $cycle->status ? 'green' : 'zinc' }}">
                            {{ $cycle->status ? __('Active') : __('Inactive') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $cycle->created_at?->toFormattedDateString() }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="9">
                        <div class="py-8 text-center text-sm text-zinc-500">
                            {{ $clientId ? __('No guard cycle records found for this client.') : __('Select a client to view guard cycle history.') }}
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
