<section class="flex h-full w-full flex-1 flex-col gap-6">
    @if (! $guard)
        <div>
            <flux:heading size="xl">{{ __('New Guard') }}</flux:heading>
            <flux:text>{{ __('Create a guard record using the old system sections.') }}</flux:text>
        </div>

        @include('livewire.operations.security-guard-form')
    @else
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <flux:heading size="xl">{{ __('Security Guard Profile') }}</flux:heading>
                <flux:text>{{ trim($guard->fname.' '.$guard->lname) }} {{ $guard->code ? '('.$guard->code.')' : '' }}</flux:text>
            </div>

            <flux:button :href="route('guards.index')" variant="ghost" wire:navigate>
                {{ __('Back to Guards') }}
            </flux:button>
        </div>

        <div class="grid gap-6 xl:grid-cols-[22rem_1fr]">
            <aside class="space-y-4 rounded-lg border border-zinc-200 p-5 dark:border-zinc-700">
                <div class="flex flex-col items-center text-center">
                    <div class="flex size-24 items-center justify-center rounded-full bg-zinc-100 text-2xl font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                        {{ strtoupper(substr((string) $guard->fname, 0, 1).substr((string) $guard->lname, 0, 1)) }}
                    </div>
                    <flux:heading size="lg" class="mt-3">{{ trim($guard->fname.' '.$guard->lname) }}</flux:heading>
                    <flux:text>{{ $guard->code }}</flux:text>
                </div>

                <div class="grid gap-3 text-sm">
                    <div>
                        <div class="text-zinc-500">{{ __('Email address') }}</div>
                        <div>{{ $guard->email ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-zinc-500">{{ __('Phone') }}</div>
                        <div>{{ trim(($guard->contact1 ?? '').' '.($guard->contact2 ?? '')) ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-zinc-500">{{ __('Address') }}</div>
                        <div>{{ $guard->address ?: '-' }}</div>
                    </div>
                    <div class="flex gap-2">
                        <flux:badge color="{{ $guard->assigned ? 'green' : 'zinc' }}">
                            {{ $guard->assigned ? __('Deployed') : __('Un deployed') }}
                        </flux:badge>
                        <flux:badge color="{{ $guard->doc_verified ? 'green' : 'amber' }}">
                            {{ $guard->doc_verified ? __('Files verified') : __('Incomplete files') }}
                        </flux:badge>
                    </div>
                </div>
            </aside>

            <div class="space-y-5">
                <div class="flex flex-wrap gap-2">
                    @foreach ([
                        'profile' => __('Profile'),
                        'clients' => __('Clients'),
                        'documents' => __('Documents'),
                        'referees' => __('Referees'),
                        'edit' => __('Update Info'),
                    ] as $tab => $label)
                        <flux:button
                            wire:click="$set('activeTab', '{{ $tab }}')"
                            variant="{{ $activeTab === $tab ? 'primary' : 'ghost' }}"
                            size="sm"
                        >
                            {{ $label }}
                        </flux:button>
                    @endforeach
                </div>

                @if ($activeTab === 'profile')
                    <div class="space-y-6 rounded-lg border border-zinc-200 p-5 dark:border-zinc-700">
                        <section class="space-y-3">
                            <flux:heading size="lg">{{ __('Bio data') }}</flux:heading>
                            <div class="grid gap-4 text-sm md:grid-cols-3">
                                <div><strong>{{ __('Full Name') }}</strong><div class="text-zinc-500">{{ trim($guard->fname.' '.$guard->lname) }}</div></div>
                                <div><strong>{{ __('Contact 1') }}</strong><div class="text-zinc-500">{{ $guard->contact1 ?: '-' }}</div></div>
                                <div><strong>{{ __('Contact 2') }}</strong><div class="text-zinc-500">{{ $guard->contact2 ?: '-' }}</div></div>
                                <div><strong>{{ __('Email') }}</strong><div class="text-zinc-500">{{ $guard->email ?: '-' }}</div></div>
                                <div><strong>{{ __('DOB') }}</strong><div class="text-zinc-500">{{ $guard->dob?->toFormattedDateString() ?: '-' }}</div></div>
                                <div><strong>{{ __('Height') }}</strong><div class="text-zinc-500">{{ $guard->height ?: '-' }}</div></div>
                                <div><strong>{{ __('Weight') }}</strong><div class="text-zinc-500">{{ $guard->weight ?: '-' }}</div></div>
                                <div><strong>{{ __('Join Date') }}</strong><div class="text-zinc-500">{{ $guard->join_date?->toFormattedDateString() ?: '-' }}</div></div>
                                <div><strong>{{ __('Gender') }}</strong><div class="text-zinc-500">{{ $guard->gender?->label() ?: '-' }}</div></div>
                                <div><strong>{{ __('Languages Spoken') }}</strong><div class="text-zinc-500">{{ $guard->languages ?: '-' }}</div></div>
                            </div>
                        </section>

                        <section class="space-y-3">
                            <flux:heading size="lg">{{ __('Demographic Info') }}</flux:heading>
                            <div class="grid gap-4 text-sm md:grid-cols-3">
                                <div><strong>{{ __('Nationality') }}</strong><div class="text-zinc-500">{{ $guard->nationality ?: '-' }}</div></div>
                                <div><strong>{{ __('Religion') }}</strong><div class="text-zinc-500">{{ $guard->religion ?: '-' }}</div></div>
                                <div><strong>{{ __('Tribe') }}</strong><div class="text-zinc-500">{{ $guard->tribe ?: '-' }}</div></div>
                                <div><strong>{{ __('Marital Status') }}</strong><div class="text-zinc-500">{{ $guard->marital_status?->label() ?: '-' }}</div></div>
                                <div><strong>{{ __('Home Contact') }}</strong><div class="text-zinc-500">{{ $guard->home_contact ?: '-' }}</div></div>
                                <div><strong>{{ __('Home Location') }}</strong><div class="text-zinc-500">{{ $guard->home_location ?: '-' }}</div></div>
                            </div>
                        </section>
                    </div>
                @elseif ($activeTab === 'clients')
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('S/N') }}</flux:table.column>
                            <flux:table.column>{{ __('Client Name') }}</flux:table.column>
                            <flux:table.column>{{ __('Contact 1') }}</flux:table.column>
                            <flux:table.column>{{ __('Schedule Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Status') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse ($guard->activeClients as $deployment)
                                <flux:table.row :key="$deployment->id">
                                    <flux:table.cell>{{ $loop->iteration }}</flux:table.cell>
                                    <flux:table.cell variant="strong">{{ $deployment->client?->name }}</flux:table.cell>
                                    <flux:table.cell>{{ $deployment->client?->contact1 }}</flux:table.cell>
                                    <flux:table.cell>{{ $deployment->schedule_type?->label() }}</flux:table.cell>
                                    <flux:table.cell>{{ $deployment->status ? __('Active') : __('Inactive') }}</flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="5">
                                        <div class="py-8 text-center text-sm text-zinc-500">{{ __('No active clients assigned.') }}</div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                @elseif ($activeTab === 'documents')
                    <div class="space-y-3">
                        <div class="flex justify-end gap-2">
                            @if ($canEdit && $guard->activeDocuments->count() > 1)
                                <flux:button wire:click="verifyDocuments" variant="{{ $guard->doc_verified ? 'filled' : 'primary' }}">
                                    {{ $guard->doc_verified ? __('Disapprove') : __('Verify') }}
                                </flux:button>
                            @endif
                            @if ($canEdit)
                                <flux:button wire:click="openUpload" variant="primary" icon="plus">{{ __('Upload New') }}</flux:button>
                            @endif
                        </div>

                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('S/N') }}</flux:table.column>
                                <flux:table.column>{{ __('Category') }}</flux:table.column>
                                <flux:table.column>{{ __('File') }}</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @forelse ($guard->activeDocuments as $document)
                                    <flux:table.row :key="$document->id">
                                        <flux:table.cell>{{ $loop->iteration }}</flux:table.cell>
                                        <flux:table.cell>
                                            {{ match ((int) $document->type) { 0 => __('Passport photo'), 1 => __('ID'), 2 => __('LC1 Letter'), default => __('Other') } }}
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:link :href="route('guard-documents.download', $document)">{{ $document->title ?: $document->original_name }}</flux:link>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="3">
                                            <div class="py-8 text-center text-sm text-zinc-500">{{ __('No documents uploaded.') }}</div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            </flux:table.rows>
                        </flux:table>
                    </div>
                @elseif ($activeTab === 'referees')
                    <div class="space-y-3">
                        @if ($canEdit)
                            <div class="flex justify-end">
                                <flux:button wire:click="openReferee" variant="primary" icon="plus">{{ __('Add Referee') }}</flux:button>
                            </div>
                        @endif

                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('S/N') }}</flux:table.column>
                                <flux:table.column>{{ __('Name') }}</flux:table.column>
                                <flux:table.column>{{ __('Contact') }}</flux:table.column>
                                <flux:table.column>{{ __('Residence') }}</flux:table.column>
                                @if ($canEdit)
                                    <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
                                @endif
                            </flux:table.columns>
                            <flux:table.rows>
                                @forelse ($guard->activeReferees as $referee)
                                    <flux:table.row :key="$referee->id">
                                        <flux:table.cell>{{ $loop->iteration }}</flux:table.cell>
                                        <flux:table.cell variant="strong">{{ $referee->name }}</flux:table.cell>
                                        <flux:table.cell>{{ $referee->contact }}</flux:table.cell>
                                        <flux:table.cell>{{ $referee->residence }}</flux:table.cell>
                                        @if ($canEdit)
                                            <flux:table.cell align="end">
                                                <flux:button wire:click="openReferee({{ $referee->id }})" size="sm">{{ __('Edit') }}</flux:button>
                                            </flux:table.cell>
                                        @endif
                                    </flux:table.row>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="{{ $canEdit ? 5 : 4 }}">
                                            <div class="py-8 text-center text-sm text-zinc-500">{{ __('No referees recorded.') }}</div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            </flux:table.rows>
                        </flux:table>
                    </div>
                        @elseif ($activeTab === 'edit')
                    @if ($canEdit)
                        @include('livewire.operations.security-guard-form')
                    @else
                        <div class="rounded-lg border border-zinc-200 p-6 text-sm text-zinc-500 dark:border-zinc-700">
                            {{ __('You do not have permission to update this guard.') }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <flux:modal wire:model.self="showUploadModal" class="md:w-[32rem]">
            <form wire:submit="saveDocument" class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Upload New File') }}</flux:heading>
                    <flux:text>{{ __('Attach a guard document to this profile.') }}</flux:text>
                </div>

                <flux:select wire:model="documentType" label="{{ __('Category') }}" placeholder="{{ __('Select') }}">
                    <flux:select.option value="">{{ __('Select') }}</flux:select.option>
                    <flux:select.option value="0">{{ __('Profile Photo/Passport Photo') }}</flux:select.option>
                    <flux:select.option value="1">{{ __('National ID') }}</flux:select.option>
                    <flux:select.option value="2">{{ __('LC 1 Letter') }}</flux:select.option>
                    <flux:select.option value="3">{{ __('Other') }}</flux:select.option>
                </flux:select>
                <flux:input wire:model="documentTitle" label="{{ __('Title') }}" />
                <flux:input wire:model="document" type="file" label="{{ __('File') }}" />

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Close') }}</flux:button></flux:modal.close>
                    <flux:button type="submit" variant="primary">{{ __('Submit') }}</flux:button>
                </div>
            </form>
        </flux:modal>

        <flux:modal wire:model.self="showRefereeModal" class="md:w-[32rem]">
            <form wire:submit="saveReferee" class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Add a new referee') }}</flux:heading>
                    <flux:text>{{ __('Record referee contact and residence details.') }}</flux:text>
                </div>

                <flux:input wire:model="refereeName" label="{{ __('Name') }}" />
                <flux:input wire:model="refereeContact" label="{{ __('Contact') }}" />
                <flux:input wire:model="refereeResidence" label="{{ __('Residence') }}" />

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close><flux:button type="button" variant="ghost">{{ __('Close') }}</flux:button></flux:modal.close>
                    <flux:button type="submit" variant="primary">{{ __('Submit') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</section>
