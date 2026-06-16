<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Guard Assignments') }}</flux:heading>
        <flux:text>{{ __('Assign available guards to active clients.') }}</flux:text>
    </div>

    <form wire:submit="assign" class="grid gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 lg:grid-cols-5">
        <flux:select wire:model="clientId" label="{{ __('Client') }}" placeholder="{{ __('Choose client') }}">
            @foreach ($clients as $client)
                <flux:select.option value="{{ $client->id }}">{{ $client->name }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model="guardId" label="{{ __('Guard') }}" placeholder="{{ __('Choose guard') }}">
            @foreach ($guards as $guard)
                <flux:select.option value="{{ $guard->id }}">{{ $guard->code }} · {{ trim($guard->fname.' '.$guard->lname) }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model="scheduleType" label="{{ __('Schedule') }}">
            @foreach ($scheduleTypes as $scheduleType)
                <flux:select.option value="{{ $scheduleType->value }}">{{ $scheduleType->label() }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:input wire:model="from" type="date" label="{{ __('From') }}" />

        <div class="flex items-end">
            <flux:button type="submit" variant="primary">{{ __('Assign') }}</flux:button>
        </div>
    </form>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Client') }}</flux:table.column>
            <flux:table.column>{{ __('Guard') }}</flux:table.column>
            <flux:table.column>{{ __('From') }}</flux:table.column>
            <flux:table.column>{{ __('Schedule') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($deployments as $deployment)
                <flux:table.row :key="$deployment->id">
                    <flux:table.cell>{{ $deployment->client?->name }}</flux:table.cell>
                    <flux:table.cell>{{ $deployment->securityGuard?->code }} · {{ trim(($deployment->securityGuard?->fname ?? '').' '.($deployment->securityGuard?->lname ?? '')) }}</flux:table.cell>
                    <flux:table.cell>{{ $deployment->from?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>{{ $deployment->schedule_type?->label() }}</flux:table.cell>
                    <flux:table.cell>{{ $deployment->status ? __('Active') : __('Inactive') }}</flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</section>
