<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ $clientId ? __('Edit Client') : __('New Client') }}</flux:heading>
        <flux:text>{{ __('Core billing and deployment details for a client account.') }}</flux:text>
    </div>

    <form wire:submit="save" class="space-y-8">
        <div class="grid gap-4 lg:grid-cols-2">
            <flux:input wire:model="name" label="{{ __('Name') }}" />

            <flux:select wire:model="categoryId" label="{{ __('Category') }}" placeholder="{{ __('Choose category') }}">
                @foreach ($categories as $category)
                    <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="contact1" label="{{ __('Primary Contact') }}" />
            <flux:input wire:model="contact2" label="{{ __('Secondary Contact') }}" />
            <flux:input wire:model="email" type="email" label="{{ __('Email') }}" />
            <flux:input wire:model="idNo" label="{{ __('Client ID') }}" />
            <flux:input wire:model="tin" label="{{ __('TIN') }}" />
            <flux:input wire:model="vatNo" label="{{ __('VAT No.') }}" />
        </div>

        <flux:input wire:model="address" label="{{ __('Address') }}" />

        <div class="grid gap-4 lg:grid-cols-4">
            <flux:select wire:model="billingCycle" label="{{ __('Billing Cycle') }}">
                @foreach ($billingCycles as $billingCycle)
                    <flux:select.option value="{{ $billingCycle->value }}">{{ $billingCycle->label() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="amount" type="number" step="0.01" label="{{ __('Amount') }}" />
            <flux:input wire:model="noGuards" type="number" step="0.01" label="{{ __('Expected Guards') }}" />
            <flux:input wire:model="billStart" type="date" label="{{ __('Bill Start') }}" />
        </div>

        <div class="max-w-md">
            <flux:select wire:model="scheduleType" label="{{ __('Schedule') }}">
                @foreach ($scheduleTypes as $scheduleType)
                    <flux:select.option value="{{ $scheduleType->value }}">{{ $scheduleType->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div class="flex gap-2">
            <flux:button :href="route('clients.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary">{{ __('Save Client') }}</flux:button>
        </div>
    </form>

    @if ($client)
        <div class="grid gap-6 xl:grid-cols-3">
            <section class="space-y-3">
                <flux:heading size="lg">{{ __('Active Deployments') }}</flux:heading>

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Guard') }}</flux:table.column>
                        <flux:table.column>{{ __('From') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($client->activeGuards as $deployment)
                            <flux:table.row :key="$deployment->id">
                                <flux:table.cell>{{ $deployment->securityGuard?->code }}</flux:table.cell>
                                <flux:table.cell>{{ $deployment->from?->toFormattedDateString() }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </section>

            <section class="space-y-3">
                <flux:heading size="lg">{{ __('Documents') }}</flux:heading>

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Title') }}</flux:table.column>
                        <flux:table.column>{{ __('File') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($client->activeDocuments as $document)
                            <flux:table.row :key="$document->id">
                                <flux:table.cell>{{ $document->title }}</flux:table.cell>
                                <flux:table.cell>{{ $document->original_name }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </section>

            <section class="space-y-3">
                <flux:heading size="lg">{{ __('Recent Attendance') }}</flux:heading>

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Date') }}</flux:table.column>
                        <flux:table.column>{{ __('Status') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($client->attendances->sortByDesc('date')->take(5) as $attendance)
                            <flux:table.row :key="$attendance->id">
                                <flux:table.cell>{{ $attendance->date?->toFormattedDateString() }}</flux:table.cell>
                                <flux:table.cell>{{ $attendance->attended?->label() }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </section>
        </div>
    @endif
</section>
