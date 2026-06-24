<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Guns') }}</flux:heading>
            <flux:text>{{ __('Assign guns to security guards and manage active assignments.') }}</flux:text>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('Assign Gun') }}</flux:button>
    </div>

    <flux:table :paginate="$assigned" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Guard') }}</flux:table.column>
            <flux:table.column>{{ __('Serial Number') }}</flux:table.column>
            <flux:table.column>{{ __('Mark Number') }}</flux:table.column>
            <flux:table.column>{{ __('Start_date') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($assigned as $ass)
                <flux:table.row :key="$ass->id">
                    <flux:table.cell>{{ $assigned->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $ass->securityGuard?->fname }} {{ $ass->securityGuard?->lname }}</flux:table.cell>
                    <flux:table.cell>{{ $ass->gun?->serial_number }}</flux:table.cell>
                    <flux:table.cell>{{ $ass->gun?->mark_number }}</flux:table.cell>
                    <flux:table.cell>{{ $ass->start_date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            <flux:button wire:click="edit({{ $ass->id }})" size="sm">{{ __('Edit') }}</flux:button>
                            <flux:button wire:click="confirmRemove({{ $ass->id }})" size="sm" variant="danger">{{ __('Remove') }}</flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">
                        <div class="py-8 text-center text-sm text-zinc-500">{{ __('No active gun assignments found.') }}</div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showFormModal" class="overflow-visible md:w-[34rem]">
        <form wire:submit="save" class="space-y-5">
            <flux:heading size="lg">{{ $editingId ? __('Edit Gun Assignment') : __('Assign Gun to a security Guard') }}</flux:heading>

            @if (! $editingId)
                <flux:field>
                    <flux:label>{{ __('Security Guard') }}</flux:label>
                    <x-searchable-select
                        wire:model="guardId"
                        :options="$guards"
                        placeholder="Select security guard"
                        dropdown-inline
                    />
                    <flux:error name="guardId" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Gun') }}</flux:label>
                    <x-searchable-select
                        wire:model="gunId"
                        :options="$availableGuns"
                        placeholder="Select gun"
                        dropdown-inline
                    />
                    <flux:error name="gunId" />
                </flux:field>
            @endif

            <flux:input wire:model="startDate" type="date" max="{{ now()->toDateString() }}" label="{{ __('Start Date') }}" />
            <flux:textarea wire:model="description" label="{{ __('Description') }}" rows="3" />
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Close') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Submit') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal wire:model.self="showRemoveModal" class="md:w-[30rem]">
        <form wire:submit="remove" class="space-y-5">
            <flux:heading size="lg">{{ __('Remove Gun Assignment') }}</flux:heading>
            <flux:text>{{ __('Record the date this gun was removed from the guard.') }}</flux:text>
            <flux:input wire:model="endDate" type="date" label="{{ __('End Date') }}" />
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Close') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="danger">{{ __('Remove') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
