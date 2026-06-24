<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Former Guards') }}</flux:heading>
        <flux:text>{{ __('Inactive guards with leaving date and reason.') }}</flux:text>
    </div>

    <div class="grid gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <flux:input
                wire:model.live.debounce.350ms="search"
                icon="magnifying-glass"
                label="{{ __('Search') }}"
                placeholder="{{ __('Name, code, contact') }}"
            />
        </div>
    </div>

    <flux:table :paginate="$guards" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('First Name') }}</flux:table.column>
            <flux:table.column>{{ __('Last Name') }}</flux:table.column>
            <flux:table.column>{{ __('Contact 1') }}</flux:table.column>
            <flux:table.column>{{ __('Contact 2') }}</flux:table.column>
            <flux:table.column>{{ __('Joining Date') }}</flux:table.column>
            <flux:table.column>{{ __('Leaving Date') }}</flux:table.column>
            <flux:table.column>{{ __('Reason') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($guards as $guard)
                <flux:table.row :key="$guard->id">
                    <flux:table.cell>{{ $guards->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell variant="strong">
                        <div>{{ $guard->fname }}</div>
                        <div class="text-xs font-normal text-zinc-500">{{ $guard->code }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $guard->lname }}</flux:table.cell>
                    <flux:table.cell>{{ $guard->contact1 }}</flux:table.cell>
                    <flux:table.cell>{{ $guard->contact2 }}</flux:table.cell>
                    <flux:table.cell>{{ $guard->join_date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>{{ $guard->left_date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>{{ $guard->left_reason }}</flux:table.cell>
                    <flux:table.cell align="end">
                        @if ($canEdit)
                            <flux:button
                                wire:click="restore({{ $guard->id }})"
                                wire:confirm="{{ __('Are you sure you want to restore this security guard?') }}"
                                size="sm"
                                variant="primary"
                            >
                                {{ __('Restore') }}
                            </flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="9">
                        <div class="py-8 text-center text-sm text-zinc-500">
                            {{ __('No former guards match the selected filters.') }}
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
