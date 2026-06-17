<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Guard Referees') }}</flux:heading>
            <flux:text>{{ __('Reference contacts recorded for security guards.') }}</flux:text>
        </div>

        @if ($canEdit)
            <flux:button wire:click="create" variant="primary">{{ __('New Referee') }}</flux:button>
        @endif
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Guard') }}</flux:table.column>
            <flux:table.column>{{ __('Referee') }}</flux:table.column>
            <flux:table.column>{{ __('Contact') }}</flux:table.column>
            <flux:table.column>{{ __('Residence') }}</flux:table.column>
            <flux:table.column>{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($referees as $referee)
                <flux:table.row :key="$referee->id">
                    <flux:table.cell>{{ $referee->securityGuard?->code }} · {{ trim(($referee->securityGuard?->fname ?? '').' '.($referee->securityGuard?->lname ?? '')) }}</flux:table.cell>
                    <flux:table.cell>{{ $referee->name }}</flux:table.cell>
                    <flux:table.cell>{{ $referee->contact }}</flux:table.cell>
                    <flux:table.cell>{{ $referee->residence }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($canEdit)
                            <flux:button wire:click="edit({{ $referee->id }})" size="sm">{{ __('Edit') }}</flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showFormModal" class="md:w-[32rem]">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingId ? __('Edit Referee') : __('New Referee') }}
                </flux:heading>
                <flux:text class="mt-2">{{ __('Attach a reference contact to a guard.') }}</flux:text>
            </div>

            <flux:select wire:model="guardId" label="{{ __('Guard') }}" placeholder="{{ __('Choose guard') }}">
                @foreach ($guards as $guard)
                    <flux:select.option value="{{ $guard->id }}">{{ $guard->code }} · {{ trim($guard->fname.' '.$guard->lname) }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="name" label="{{ __('Name') }}" />
            <flux:input wire:model="contact" label="{{ __('Contact') }}" />
            <flux:input wire:model="residence" label="{{ __('Residence') }}" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
