<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('List Of Security Guards') }}</flux:heading>
            <flux:text>{{ __('Active guards, deployment state, and file completion.') }}</flux:text>
        </div>

        <flux:button :href="route('guards.create')" variant="primary" icon="plus" wire:navigate>
            {{ __('New Guard') }}
        </flux:button>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-2xl font-semibold">{{ number_format($activeCount) }}</div>
            <div class="text-sm text-zinc-500">{{ __('Active Guards') }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-2xl font-semibold">{{ number_format($deployedCount) }}</div>
            <div class="text-sm text-zinc-500">{{ __('Deployed Guards') }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-2xl font-semibold">{{ number_format($undeployedCount) }}</div>
            <div class="text-sm text-zinc-500">{{ __('Un deployed Guards') }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="text-2xl font-semibold">{{ number_format($incompleteFilesCount) }}</div>
            <div class="text-sm text-zinc-500">{{ __('Incomplete Files') }}</div>
        </div>
    </div>

    <div class="grid gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 lg:grid-cols-6">
        <div class="lg:col-span-2">
            <flux:input
                wire:model.live.debounce.350ms="search"
                icon="magnifying-glass"
                label="{{ __('Search') }}"
                placeholder="{{ __('Name, code, contact, ID') }}"
            />
        </div>

        <flux:input wire:model.live="from" type="date" label="{{ __('From Joining Date') }}" />
        <flux:input wire:model.live="to" type="date" label="{{ __('To Joining Date') }}" />

        <flux:select wire:model.live="deployment" label="{{ __('Deployment') }}" placeholder="{{ __('All guards') }}">
            <flux:select.option value="">{{ __('All guards') }}</flux:select.option>
            <flux:select.option value="deployed">{{ __('Deployed') }}</flux:select.option>
            <flux:select.option value="undeployed">{{ __('Un deployed') }}</flux:select.option>
        </flux:select>

        <flux:select wire:model.live="fileStatus" label="{{ __('Files') }}" placeholder="{{ __('All files') }}">
            <flux:select.option value="">{{ __('All files') }}</flux:select.option>
            <flux:select.option value="verified">{{ __('Verified') }}</flux:select.option>
            <flux:select.option value="incomplete">{{ __('Incomplete') }}</flux:select.option>
        </flux:select>

        <div class="flex items-end">
            <flux:button wire:click="resetFilters" variant="ghost" icon="x-mark">
                {{ __('Reset') }}
            </flux:button>
        </div>
    </div>

    <flux:table :paginate="$guards" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('First Name') }}</flux:table.column>
            <flux:table.column>{{ __('Last Name') }}</flux:table.column>
            <flux:table.column>{{ __('Contact 1') }}</flux:table.column>
            <flux:table.column>{{ __('Contact 2') }}</flux:table.column>
            <flux:table.column>{{ __('Joining Date') }}</flux:table.column>
            <flux:table.column>{{ __('Deployed') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($guards as $guard)
                <flux:table.row :key="$guard->id">
                    <flux:table.cell>{{ $guards->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell variant="strong">
                        <div>{{ $guard->fname }}</div>
                        <div class="text-xs font-normal text-zinc-500">{{ $guard->code }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $guard->lname }}</flux:table.cell>
                    <flux:table.cell>{{ $guard->contact1 }}</flux:table.cell>
                    <flux:table.cell>{{ $guard->contact2 }}</flux:table.cell>
                    <flux:table.cell>{{ $guard->join_date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="{{ $guard->active_clients_count > 0 ? 'green' : 'zinc' }}">
                            {{ $guard->active_clients_count > 0 ? __('Yes') : __('No') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            <flux:button :href="route('guards.edit', $guard)" size="sm" variant="primary" wire:navigate>
                                {{ __('Profile') }}
                            </flux:button>

                            @if ($canEdit)
                                <flux:button wire:click="markFormer({{ $guard->id }})" size="sm" variant="danger">
                                    {{ __('Delete') }}
                                </flux:button>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8">
                        <div class="py-8 text-center text-sm text-zinc-500">
                            {{ __('No active guards match the selected filters.') }}
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showFormerModal" class="md:w-[32rem]">
        <form wire:submit="confirmFormer" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Mark Guard as Left') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ __('Record why :name is no longer active.', ['name' => $formerGuardName ?? __('this guard')]) }}
                </flux:text>
            </div>

            <flux:input wire:model="leftDate" type="date" label="{{ __('Date') }}" />
            <flux:textarea wire:model="leftReason" label="{{ __('Reason') }}" rows="3" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">{{ __('Close') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger">{{ __('Submit') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
