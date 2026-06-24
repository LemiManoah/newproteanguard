<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Attendance Summary') }}</flux:heading>
        <flux:text>{{ __('Guard workday totals for the selected date range.') }}</flux:text>
    </div>

    <div class="grid gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 lg:grid-cols-4">
        <flux:input wire:model.live="from" type="date" label="{{ __('From') }}" />
        <flux:input wire:model.live="to" type="date" label="{{ __('To') }}" />
        <flux:input
            wire:model.live.debounce.350ms="search"
            icon="magnifying-glass"
            label="{{ __('Search') }}"
            placeholder="{{ __('Guard name or code') }}"
        />
        <div class="flex items-end text-sm text-zinc-500">
            {{ __('Showing present attendance from :from to :to', ['from' => $from, 'to' => $to]) }}
        </div>
    </div>

    <flux:table :paginate="$guards" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('No') }}</flux:table.column>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Worked') }}</flux:table.column>
            <flux:table.column>{{ __('Overtime') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($guards as $guard)
                <flux:table.row :key="$guard->id">
                    <flux:table.cell>{{ $guards->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="font-medium">{{ trim(($guard->fname ?? '').' '.($guard->lname ?? '')) }}</div>
                        <div class="text-xs text-zinc-500">{{ $guard->code }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $guard->worked_count }}</flux:table.cell>
                    <flux:table.cell>{{ $guard->overtime_count }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4">
                        <div class="py-8 text-center text-sm text-zinc-500">
                            {{ __('No guard attendance totals match the selected filters.') }}
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
