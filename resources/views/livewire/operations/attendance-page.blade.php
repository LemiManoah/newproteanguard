<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Attendance') }}</flux:heading>
            <flux:text>{{ __('Track daily guard attendance by client, period, duty category, and status.') }}</flux:text>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">
            {{ __('Record Attendance') }}
        </flux:button>
    </div>

    <div class="grid gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 lg:grid-cols-6">
        <flux:input
            wire:model.live.debounce.350ms="search"
            icon="magnifying-glass"
            label="{{ __('Search') }}"
            placeholder="{{ __('Client, guard, code, reason') }}"
            class="lg:col-span-2"
        />

        <flux:select wire:model.live="dateRange" label="{{ __('Date range') }}">
            @foreach ($dateRanges as $key => $range)
                <flux:select.option value="{{ $key }}">{{ __($range['label']) }}</flux:select.option>
            @endforeach
            <flux:select.option value="custom">{{ __('Custom') }}</flux:select.option>
        </flux:select>

        <flux:input wire:model.live="from" type="date" label="{{ __('From') }}" />
        <flux:input wire:model.live="to" type="date" label="{{ __('To') }}" />

        <flux:field>
            <flux:label>{{ __('Client') }}</flux:label>
            <x-searchable-select
                wire:model.live="clientId"
                :options="$clients"
                placeholder="All clients"
            />
            <flux:error name="clientId" />
        </flux:field>

        <flux:select wire:model.live="status" label="{{ __('Status') }}" placeholder="{{ __('All statuses') }}">
            @foreach ($attendanceStatuses as $attendanceStatus)
                <flux:select.option value="{{ $attendanceStatus->value }}">{{ $attendanceStatus->label() }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="scheduleType" label="{{ __('Period') }}" placeholder="{{ __('All periods') }}">
            @foreach ($scheduleTypes as $type)
                <flux:select.option value="{{ $type->value }}">{{ $type->label() }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="duty" label="{{ __('Category') }}" placeholder="{{ __('All categories') }}">
            <flux:select.option value="duty">{{ __('Duty') }}</flux:select.option>
            <flux:select.option value="overtime">{{ __('Overtime') }}</flux:select.option>
        </flux:select>

        <div class="flex items-end">
            <flux:button wire:click="resetFilters" variant="ghost" icon="x-mark">
                {{ __('Reset') }}
            </flux:button>
        </div>
    </div>

    <flux:table :paginate="$attendanceRows" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'client'" :direction="$sortDirection" wire:click="sort('client')">{{ __('Client') }}</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'guard'" :direction="$sortDirection" wire:click="sort('guard')">{{ __('Guard') }}</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'date'" :direction="$sortDirection" wire:click="sort('date')">{{ __('Date') }}</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'schedule_type'" :direction="$sortDirection" wire:click="sort('schedule_type')">{{ __('Period') }}</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'over_time'" :direction="$sortDirection" wire:click="sort('over_time')">{{ __('Category') }}</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'attended'" :direction="$sortDirection" wire:click="sort('attended')">{{ __('Status') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($attendanceRows as $attendance)
                <flux:table.row :key="$attendance->id">
                    <flux:table.cell>{{ $attendanceRows->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $attendance->client?->name ?? __('Unknown client') }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="font-medium">
                            {{ trim(($attendance->securityGuard?->fname ?? '').' '.($attendance->securityGuard?->lname ?? '')) ?: __('Unknown guard') }}
                        </div>
                        <div class="text-xs text-zinc-500">{{ $attendance->securityGuard?->code }}</div>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $attendance->date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>{{ $attendance->schedule_type?->label() }}</flux:table.cell>
                    <flux:table.cell>{{ $attendanceHelper->dutyLabel((bool) $attendance->over_time) }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$attendanceHelper->attendanceBadgeColor($attendance->attended)" inset="top bottom">
                            {{ $attendance->attended?->label() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button wire:click="edit({{ $attendance->id }})" size="sm" variant="{{ $attendance->attended === \App\Enums\AttendanceStatus::Absent ? 'primary' : 'ghost' }}">
                            {{ $attendance->attended === \App\Enums\AttendanceStatus::Absent ? __('Mark Present') : __('Record') }}
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8">
                        <div class="py-8 text-center text-sm text-zinc-500">
                            {{ __('No attendance records match the selected filters.') }}
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showRecordModal" class="md:w-[38rem]">
        <form wire:submit="record" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingAttendanceId ? __('Record Attendance') : __('New Attendance Record') }}
                </flux:heading>
                <flux:text class="mt-2">
                    @if ($recordGuardName || $recordClientName)
                        {{ trim(($recordGuardName ?? __('Unknown guard')).' - '.($recordClientName ?? __('Unknown client'))) }}
                    @else
                        {{ __('Choose a deployment and record the guard status for the selected date.') }}
                    @endif
                </flux:text>
            </div>

            @unless ($editingAttendanceId)
                <flux:field>
                    <flux:label>{{ __('Deployment') }}</flux:label>
                    <x-searchable-select
                        wire:model="deploymentId"
                        :options="$deployments"
                        placeholder="Choose deployment"
                    />
                    <flux:error name="deploymentId" />
                </flux:field>
            @endunless

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model="date" type="date" label="{{ __('Date') }}" />

                <flux:select wire:model.live="attended" label="{{ __('Attendance') }}">
                    @foreach ($attendanceStatuses as $attendanceStatus)
                        <flux:select.option value="{{ $attendanceStatus->value }}">{{ $attendanceStatus->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            @if ((int) $attended === \App\Enums\AttendanceStatus::Absent->value)
                <flux:select wire:model="absentCategory" label="{{ __('Absence category') }}" placeholder="{{ __('Choose category') }}">
                    @foreach ($absenceCategories as $absenceCategory)
                        <flux:select.option value="{{ $absenceCategory->value }}">{{ $absenceCategory->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            @endif

            <flux:textarea wire:model="reason" label="{{ __('Comment / Reason') }}" rows="3" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Submit') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
