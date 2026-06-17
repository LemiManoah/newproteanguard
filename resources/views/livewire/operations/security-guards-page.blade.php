<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Security Guards') }}</flux:heading>
            <flux:text>{{ __('Guard roster and current deployment state.') }}</flux:text>
        </div>

        <flux:button :href="route('guards.create')" variant="primary" wire:navigate>{{ __('New Guard') }}</flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Code') }}</flux:table.column>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Contact') }}</flux:table.column>
            <flux:table.column>{{ __('Gender') }}</flux:table.column>
            <flux:table.column>{{ __('Deployments') }}</flux:table.column>
            <flux:table.column>{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($guards as $guard)
                <flux:table.row :key="$guard->id">
                    <flux:table.cell>{{ $guard->code }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="font-medium">{{ trim($guard->fname.' '.$guard->lname) }}</div>
                        <div class="text-xs text-zinc-500">{{ $guard->id_no }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $guard->contact1 }}</flux:table.cell>
                    <flux:table.cell>{{ $guard->gender?->label() }}</flux:table.cell>
                    <flux:table.cell>{{ $guard->active_clients_count }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button :href="route('guards.edit', $guard)" size="sm" wire:navigate>{{ __('Open') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</section>
