<form wire:submit="save" class="space-y-8">
    <section class="space-y-4">
        <flux:heading size="lg">{{ __('Basic Info') }}</flux:heading>
        <div class="grid gap-4 lg:grid-cols-4">
            <flux:input wire:model="codeNumber" type="number" label="{{ __('Code Number') }}" />
            <flux:input wire:model="code" label="{{ __('Code') }}" />
            <flux:input wire:model="fname" label="{{ __('First Name') }}" placeholder="{{ __('First name of the Guard') }}" />
            <flux:input wire:model="lname" label="{{ __('Last Name') }}" placeholder="{{ __('Last name of the Guard') }}" />
            <flux:input wire:model="contact1" label="{{ __('Contact 1') }}" />
            <flux:input wire:model="contact2" label="{{ __('Contact 2') }}" />
            <flux:input wire:model="email" type="email" label="{{ __('Email') }}" placeholder="info@example.com" />
            <flux:input wire:model="dob" type="date" label="{{ __('D.O.B') }}" />
            <flux:input wire:model="weight" type="number" step="any" label="{{ __('Weight (Kgs)') }}" />
            <flux:input wire:model="height" type="number" step="any" label="{{ __('Height (meters)') }}" />
            <flux:input wire:model="joinDate" type="date" label="{{ __('Joining Date') }}" />

            <flux:select wire:model="gender" label="{{ __('Gender') }}" placeholder="{{ __('Select Gender') }}">
                <flux:select.option value="">{{ __('Select Gender') }}</flux:select.option>
                @foreach ($genders as $gender)
                    <flux:select.option value="{{ $gender->value }}">{{ $gender->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </section>

    <section class="space-y-4">
        <flux:heading size="lg">{{ __('Demographic Info') }}</flux:heading>
        <div class="grid gap-4 lg:grid-cols-4">
            <flux:input wire:model="nationality" label="{{ __('Nationality') }}" />
            <flux:input wire:model="religion" label="{{ __('Religion') }}" />
            <flux:input wire:model="tribe" label="{{ __('Tribe') }}" />
            <flux:select wire:model="maritalStatus" label="{{ __('Marital Status') }}" placeholder="{{ __('Select') }}">
                <flux:select.option value="">{{ __('Select') }}</flux:select.option>
                @foreach ($maritalStatuses as $maritalStatus)
                    <flux:select.option value="{{ $maritalStatus->value }}">{{ $maritalStatus->label() }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input wire:model="address" label="{{ __('Address') }}" />
            <flux:input wire:model="homeContact" label="{{ __('Home Contact') }}" />
            <flux:input wire:model="homeLocation" label="{{ __('Home Location') }}" />
        </div>
    </section>

    <section class="space-y-4">
        <flux:heading size="lg">{{ __('Parents Info') }}</flux:heading>
        <div class="grid gap-4 lg:grid-cols-4">
            <flux:input wire:model="fatherName" label="{{ __('Father\'s Name') }}" />
            <flux:input wire:model="fatherContact" label="{{ __('Father\'s Contact') }}" />
            <flux:input wire:model="fatherOccupation" label="{{ __('Father\'s Occupation') }}" />
            <flux:select wire:model="fatherLifeStatus" label="{{ __('Father Alive') }}" placeholder="{{ __('Select') }}">
                <flux:select.option value="">{{ __('Select') }}</flux:select.option>
                @foreach ($lifeStatuses as $lifeStatus)
                    <flux:select.option value="{{ $lifeStatus->value }}">{{ $lifeStatus->value === 0 ? __('Yes') : __('No') }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="motherName" label="{{ __('Mother\'s Name') }}" />
            <flux:input wire:model="motherContact" label="{{ __('Mother\'s Contact') }}" />
            <flux:input wire:model="motherOccupation" label="{{ __('Mother\'s Occupation') }}" />
            <flux:select wire:model="motherLifeStatus" label="{{ __('Mother Alive') }}" placeholder="{{ __('Select') }}">
                <flux:select.option value="">{{ __('Select') }}</flux:select.option>
                @foreach ($lifeStatuses as $lifeStatus)
                    <flux:select.option value="{{ $lifeStatus->value }}">{{ $lifeStatus->value === 0 ? __('Yes') : __('No') }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </section>

    <section class="space-y-4">
        <flux:heading size="lg">{{ __('Other Info') }}</flux:heading>
        <div class="grid gap-4 lg:grid-cols-4">
            <flux:input wire:model="nok" label="{{ __('Next Of Kin') }}" />
            <flux:input wire:model="nokContact" label="{{ __('Next Of Kin Contact') }}" />
            <flux:input wire:model="nokRelationship" label="{{ __('Next Of Kin Relationship') }}" />
            <flux:input wire:model="nokResidence" label="{{ __('Next Of Kin Residence') }}" />

            <flux:select wire:model="idType" label="{{ __('ID Type') }}" placeholder="{{ __('Select') }}">
                <flux:select.option value="">{{ __('Select') }}</flux:select.option>
                @foreach ($identityTypes as $identityType)
                    <flux:select.option value="{{ $identityType->value }}">{{ $identityType->label() }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input wire:model="idNo" label="{{ __('ID Number') }}" />
            <flux:input wire:model="idExpiry" type="date" label="{{ __('ID Expiry Date') }}" />
            <flux:input wire:model="languages" label="{{ __('Languages Spoken') }}" />

            <div class="flex items-end">
                <flux:checkbox wire:model.live="medicalHistory" label="{{ __('Medical History') }}" />
            </div>

            @if ($medicalHistory)
                <div class="lg:col-span-3">
                    <flux:input wire:model="medicalHistoryDetails" label="{{ __('Medical History Details') }}" />
                </div>
            @endif
        </div>
    </section>

    <div class="flex gap-2">
        <flux:button :href="route('guards.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
        <flux:button type="submit" variant="primary">{{ $guardId ? __('Update Profile') : __('Add Security Guard') }}</flux:button>
    </div>
</form>
