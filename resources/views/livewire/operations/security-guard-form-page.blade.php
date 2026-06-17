<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ $guardId ? __('Edit Guard') : __('New Guard') }}</flux:heading>
        <flux:text>{{ __('Core guard identity, contact, and next-of-kin details.') }}</flux:text>
    </div>

    <form wire:submit="save" class="space-y-8">
        <div class="grid gap-4 lg:grid-cols-3">
            <flux:input wire:model="codeNumber" type="number" label="{{ __('Code Number') }}" />
            <flux:input wire:model="code" label="{{ __('Code') }}" />
            <flux:input wire:model="joinDate" type="date" label="{{ __('Join Date') }}" />
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <flux:input wire:model="fname" label="{{ __('First Name') }}" />
            <flux:input wire:model="lname" label="{{ __('Last Name') }}" />
            <flux:input wire:model="contact1" label="{{ __('Primary Contact') }}" />
            <flux:input wire:model="contact2" label="{{ __('Secondary Contact') }}" />
            <flux:input wire:model="email" type="email" label="{{ __('Email') }}" />
            <flux:input wire:model="dob" type="date" label="{{ __('Date of Birth') }}" />
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <flux:select wire:model="gender" label="{{ __('Gender') }}">
                @foreach ($genders as $gender)
                    <flux:select.option value="{{ $gender->value }}">{{ $gender->label() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="maritalStatus" label="{{ __('Marital Status') }}">
                @foreach ($maritalStatuses as $maritalStatus)
                    <flux:select.option value="{{ $maritalStatus->value }}">{{ $maritalStatus->label() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="address" label="{{ __('Address') }}" />
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <flux:input wire:model="nok" label="{{ __('Next of Kin') }}" />
            <flux:input wire:model="nokContact" label="{{ __('Next of Kin Contact') }}" />
            <flux:input wire:model="nokRelationship" label="{{ __('Relationship') }}" />
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <flux:select wire:model="idType" label="{{ __('ID Type') }}">
                @foreach ($identityTypes as $identityType)
                    <flux:select.option value="{{ $identityType->value }}">{{ $identityType->label() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="idNo" label="{{ __('ID Number') }}" />
            <flux:input wire:model="idExpiry" type="date" label="{{ __('ID Expiry') }}" />
        </div>

        <div class="flex gap-2">
            <flux:button :href="route('guards.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary">{{ __('Save Guard') }}</flux:button>
        </div>
    </form>

    @if ($guard)
        <div class="grid gap-6 xl:grid-cols-3">
            <section class="space-y-3">
                <flux:heading size="lg">{{ __('Active Clients') }}</flux:heading>

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Client') }}</flux:table.column>
                        <flux:table.column>{{ __('From') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($guard->activeClients as $deployment)
                            <flux:table.row :key="$deployment->id">
                                <flux:table.cell>{{ $deployment->client?->name }}</flux:table.cell>
                                <flux:table.cell>{{ $deployment->from?->toFormattedDateString() }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </section>

            <section class="space-y-3">
                <flux:heading size="lg">{{ __('Referees') }}</flux:heading>

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Name') }}</flux:table.column>
                        <flux:table.column>{{ __('Contact') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($guard->activeReferees as $referee)
                            <flux:table.row :key="$referee->id">
                                <flux:table.cell>{{ $referee->name }}</flux:table.cell>
                                <flux:table.cell>{{ $referee->contact }}</flux:table.cell>
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
                        @foreach ($guard->activeDocuments as $document)
                            <flux:table.row :key="$document->id">
                                <flux:table.cell>{{ $document->title }}</flux:table.cell>
                                <flux:table.cell>{{ $document->original_name }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </section>
        </div>
    @endif
</section>
