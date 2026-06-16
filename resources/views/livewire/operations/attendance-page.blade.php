<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Client Attendance') }}</flux:heading>
        <flux:text>{{ __('Record daily attendance against active deployments.') }}</flux:text>
    </div>

    <form wire:submit="record" class="grid gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 lg:grid-cols-5">
        <flux:select wire:model="deploymentId" label="{{ __('Deployment') }}" placeholder="{{ __('Choose deployment') }}">
            @foreach ($deployments as $deployment)
                <flux:select.option value="{{ $deployment->id }}">
                    {{ $deployment->client?->name }} · {{ $deployment->securityGuard?->code }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:input wire:model="date" type="date" label="{{ __('Date') }}" />

        <flux:select wire:model.live="attended" label="{{ __('Attendance') }}">
            @foreach ($attendanceStatuses as $attendanceStatus)
                <flux:select.option value="{{ $attendanceStatus->value }}">{{ $attendanceStatus->label() }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model="absentCategory" label="{{ __('Absence') }}" placeholder="{{ __('Optional') }}">
            @foreach ($absenceCategories as $absenceCategory)
                <flux:select.option value="{{ $absenceCategory->value }}">{{ $absenceCategory->label() }}</flux:select.option>
            @endforeach
        </flux:select>

        <div class="flex items-end">
            <flux:button type="submit" variant="primary">{{ __('Record') }}</flux:button>
        </div>

        <div class="lg:col-span-5">
            <flux:input wire:model="reason" label="{{ __('Reason') }}" />
        </div>
    </form>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column>{{ __('Client') }}</flux:table.column>
            <flux:table.column>{{ __('Guard') }}</flux:table.column>
            <flux:table.column>{{ __('Attendance') }}</flux:table.column>
            <flux:table.column>{{ __('Reason') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($attendanceRows as $attendance)
                <flux:table.row :key="$attendance->id">
                    <flux:table.cell>{{ $attendance->date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>{{ $attendance->client?->name }}</flux:table.cell>
                    <flux:table.cell>{{ $attendance->securityGuard?->code }} · {{ trim(($attendance->securityGuard?->fname ?? '').' '.($attendance->securityGuard?->lname ?? '')) }}</flux:table.cell>
                    <flux:table.cell>{{ $attendance->attended?->label() }}</flux:table.cell>
                    <flux:table.cell>{{ $attendance->reason }}</flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</section>
