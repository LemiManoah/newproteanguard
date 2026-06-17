<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Clients') }}</flux:heading>
            <flux:text>{{ __('Active client accounts and deployment coverage.') }}</flux:text>
        </div>

        <flux:button :href="route('clients.create')" variant="primary" wire:navigate>{{ __('New Client') }}</flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Client') }}</flux:table.column>
            <flux:table.column>{{ __('Category') }}</flux:table.column>
            <flux:table.column>{{ __('Contacts') }}</flux:table.column>
            <flux:table.column>{{ __('Billing') }}</flux:table.column>
            <flux:table.column>{{ __('Guards') }}</flux:table.column>
            <flux:table.column>{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($clients as $client)
                <flux:table.row :key="$client->id">
                    <flux:table.cell>
                        <div class="font-medium">{{ $client->name }}</div>
                        <div class="text-xs text-zinc-500">{{ $client->email }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $client->category?->name ?? __('Uncategorised') }}</flux:table.cell>
                    <flux:table.cell>{{ $client->contact1 }}</flux:table.cell>
                    <flux:table.cell>{{ $client->billing_cycle?->label() }} · {{ number_format((float) $client->amount) }}</flux:table.cell>
                    <flux:table.cell>{{ $client->active_guards_count }} / {{ (float) $client->no_guards }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button :href="route('clients.edit', $client)" size="sm" wire:navigate>{{ __('Open') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</section>
