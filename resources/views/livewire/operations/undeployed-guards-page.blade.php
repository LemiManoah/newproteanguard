<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Undeployed Guards') }}</flux:heading>
        <flux:text>{{ __('Active guards without an active client deployment.') }}</flux:text>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Code') }}</flux:table.column>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Contact') }}</flux:table.column>
            <flux:table.column>{{ __('Joined') }}</flux:table.column>
            <flux:table.column>{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($guards as $guard)
                <flux:table.row :key="$guard->id">
                    <flux:table.cell>{{ $guard->code }}</flux:table.cell>
                    <flux:table.cell>{{ trim($guard->fname.' '.$guard->lname) }}</flux:table.cell>
                    <flux:table.cell>{{ $guard->contact1 }}</flux:table.cell>
                    <flux:table.cell>{{ $guard->join_date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button :href="route('assignments.index')" size="sm" wire:navigate>{{ __('Assign') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</section>
