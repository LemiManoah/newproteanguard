<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Attendance Summary') }}</flux:heading>
        <flux:text>{{ __('Attendance records for the selected date range.') }}</flux:text>
    </div>

    <div class="grid gap-4 lg:grid-cols-5">
        <flux:input wire:model.live="from" type="date" label="{{ __('From') }}" />
        <flux:input wire:model.live="to" type="date" label="{{ __('To') }}" />

        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-xs text-zinc-500">{{ __('Present') }}</div>
            <div class="text-2xl font-semibold">{{ $presentCount }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-xs text-zinc-500">{{ __('Absent') }}</div>
            <div class="text-2xl font-semibold">{{ $absentCount }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-xs text-zinc-500">{{ __('Replaced') }}</div>
            <div class="text-2xl font-semibold">{{ $replacedCount }}</div>
        </div>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column>{{ __('Client') }}</flux:table.column>
            <flux:table.column>{{ __('Guard') }}</flux:table.column>
            <flux:table.column>{{ __('Schedule') }}</flux:table.column>
            <flux:table.column>{{ __('Attendance') }}</flux:table.column>
            <flux:table.column>{{ __('Reason') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($rows as $row)
                <flux:table.row :key="$row->id">
                    <flux:table.cell>{{ $row->date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>{{ $row->client?->name }}</flux:table.cell>
                    <flux:table.cell>{{ $row->securityGuard?->code }} · {{ trim(($row->securityGuard?->fname ?? '').' '.($row->securityGuard?->lname ?? '')) }}</flux:table.cell>
                    <flux:table.cell>{{ $row->schedule_type?->label() }}</flux:table.cell>
                    <flux:table.cell>{{ $row->attended?->label() }}</flux:table.cell>
                    <flux:table.cell>{{ $row->reason }}</flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</section>
