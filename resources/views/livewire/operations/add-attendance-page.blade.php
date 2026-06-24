<section class="max-w-2xl mx-auto flex flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Assign Guard / Add Attendance') }}</flux:heading>
        <flux:text>{{ __('Manually add a guard attendance record to a schedule.') }}</flux:text>
    </div>

    @if (session()->has('error'))
        <div class="rounded-md bg-red-50 p-4 text-sm text-red-700 dark:bg-red-950/30 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div class="rounded-md bg-green-50 p-4 text-sm text-green-700 dark:bg-green-950/30 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="save" class="grid gap-6 rounded-lg border border-zinc-200 p-6 dark:border-zinc-700 bg-white dark:bg-zinc-900">
        <flux:field>
            <flux:label>{{ __('Client') }}</flux:label>
            <x-searchable-select
                wire:model="clientId"
                :options="$clients"
                placeholder="Choose client"
            />
            <flux:error name="clientId" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Guard') }}</flux:label>
            <x-searchable-select
                wire:model="guardId"
                :options="$guards"
                placeholder="Choose guard"
            />
            <flux:error name="guardId" />
        </flux:field>

        <div class="grid gap-6 md:grid-cols-3">
            <flux:select wire:model="scheduleType" label="{{ __('Schedule') }}" required>
                @foreach ($scheduleTypes as $type)
                    <flux:select.option value="{{ $type->value }}">{{ $type->label() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="date" type="date" label="{{ __('Date') }}" required />

            <flux:select wire:model="overtime" label="{{ __('Duty Type') }}" required>
                <flux:select.option value="0">{{ __('Normal Duty') }}</flux:select.option>
                <flux:select.option value="1">{{ __('Overtime') }}</flux:select.option>
            </flux:select>
        </div>

        <div class="flex justify-end gap-3 border-t border-zinc-200 pt-6 dark:border-zinc-700">
            <flux:button type="submit" variant="primary">{{ __('Record Attendance') }}</flux:button>
        </div>
    </form>
</section>
