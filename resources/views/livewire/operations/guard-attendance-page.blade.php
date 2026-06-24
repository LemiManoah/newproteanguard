<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Attendance by Single Guard') }}</flux:heading>
        <flux:text>{{ __('View and manage attendance records for a specific security guard.') }}</flux:text>
    </div>

    @if (session()->has('success'))
        <div class="rounded-md bg-green-50 p-4 text-sm text-green-700 dark:bg-green-950/30 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 lg:grid-cols-4 bg-white dark:bg-zinc-900">
        <flux:field>
            <flux:label>{{ __('Guard') }}</flux:label>
            <x-searchable-select
                wire:model.live="guardId"
                :options="$guards"
                placeholder="Choose guard"
            />
            <flux:error name="guardId" />
        </flux:field>

        <flux:input wire:model.live="from" type="date" label="{{ __('From') }}" required />

        <flux:input wire:model.live="to" type="date" label="{{ __('To') }}" required />
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column>{{ __('Client') }}</flux:table.column>
            <flux:table.column>{{ __('Period') }}</flux:table.column>
            <flux:table.column>{{ __('Category') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($attendanceRows as $row)
                <flux:table.row :key="$row->id">
                    <flux:table.cell>{{ $row->date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>{{ $row->client?->name }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($row->schedule_type?->value === 0)
                            {{ __('Day') }}
                        @elseif ($row->schedule_type?->value === 1)
                            {{ __('Night') }}
                        @else
                            {{ __('Full Time') }}
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($row->over_time)
                            {{ __('Overtime') }}
                        @else
                            {{ __('Normal Duty') }}
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $row->attended?->label() }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button wire:click="delete({{ $row->id }})" wire:confirm="{{ __('Are you sure you want to delete this attendance record?') }}" size="sm" variant="danger">
                            {{ __('Delete') }}
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center text-zinc-500 py-6">
                        {{ $guardId ? __('No attendance records found for the selected period.') : __('Select a guard to view attendance.') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
