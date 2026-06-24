<section class="flex h-full w-full flex-1 flex-col gap-6">
    @if (! $client)
        <div>
            <flux:heading size="xl">{{ __('Add New Client') }}</flux:heading>
            <flux:text>{{ __('Record the client profile, schedule preference, and billing information.') }}</flux:text>
        </div>

        <form wire:submit="save" class="space-y-8">
            <section class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ __('Basic Info') }}</flux:heading>
                    <flux:separator class="mt-3" />
                </div>

                <div class="grid gap-4 lg:grid-cols-3">
                    <flux:input wire:model="name" label="{{ __('Name') }}" placeholder="{{ __('Full name of the client') }}" />
                    <flux:input wire:model="contact1" label="{{ __('Contact 1') }}" />
                    <flux:input wire:model="contact2" label="{{ __('Contact 2') }}" />
                    <flux:input wire:model="email" type="email" label="{{ __('Email') }}" placeholder="info@example.com" />
                    <flux:input wire:model="idNo" label="{{ __('ID Number') }}" />
                    <flux:input wire:model="tin" label="{{ __('TIN') }}" />
                    <flux:input wire:model="address" label="{{ __('Address') }}" />

                    <flux:field>
                        <flux:label>{{ __('Category') }}</flux:label>
                        <x-searchable-select
                            wire:model="categoryId"
                            :options="$categories"
                            placeholder="Select Category"
                        />
                        <flux:error name="categoryId" />
                    </flux:field>

                    <flux:select wire:model="scheduleType" label="{{ __('Schedule') }}" placeholder="{{ __('Select') }}">
                        <flux:select.option value="">{{ __('Select Schedule') }}</flux:select.option>
                        @foreach ($scheduleTypes as $scheduleType)
                            <flux:select.option value="{{ $scheduleType->value }}">{{ $scheduleType->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </section>

            <section class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ __('Billing information') }}</flux:heading>
                    <flux:separator class="mt-3" />
                </div>

                <div class="grid gap-4 lg:grid-cols-4">
                    <flux:select wire:model="billingCycle" label="{{ __('Billing Cycle') }}">
                        <flux:select.option value="">{{ __('Select Billing Cycle') }}</flux:select.option>
                        @foreach ($billingCycles as $billingCycle)
                            <flux:select.option value="{{ $billingCycle->value }}">{{ $billingCycle->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input wire:model="noGuards" type="number" step="0.01" label="{{ __('Number of Guards') }}" />
                    <flux:input wire:model="amount" type="number" step="0.01" label="{{ __('Billing Amount') }}" />
                    <flux:input wire:model="billStart" type="date" label="{{ __('Billing Start Date') }}" />
                </div>
            </section>

            <div class="flex gap-2">
                <flux:button :href="route('clients.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Add Client') }}</flux:button>
            </div>
        </form>
    @else
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <flux:heading size="xl">{{ __('Client Profile') }}</flux:heading>
                <flux:text>{{ $client->name }} - {{ $client->category?->name ?? __('Uncategorised') }}</flux:text>
            </div>

            <flux:button :href="route('clients.index')" variant="ghost" wire:navigate>
                {{ __('Back to Clients') }}
            </flux:button>
        </div>

        <div class="grid gap-6 lg:grid-cols-[18rem_1fr]">
            <aside class="space-y-6">
                <section class="rounded-lg border border-zinc-200 p-5 dark:border-zinc-700">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex size-24 items-center justify-center rounded-full bg-zinc-100 text-3xl font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                            {{ strtoupper(substr((string) $client->name, 0, 1)) }}
                        </div>
                        <flux:heading size="lg" class="mt-4">{{ $client->name }}</flux:heading>
                        <flux:text>{{ $client->category?->name ?? __('Uncategorised') }}</flux:text>
                    </div>

                    <div class="mt-6 space-y-4 text-sm">
                        <div>
                            <div class="text-xs text-zinc-500">{{ __('Email address') }}</div>
                            <div class="font-medium">{{ $client->email ?: __('Not recorded') }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-zinc-500">{{ __('Contacts') }}</div>
                            <div class="font-medium">{{ trim(($client->contact1 ?? '').' / '.($client->contact2 ?? ''), ' /') ?: __('Not recorded') }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-zinc-500">{{ __('Address') }}</div>
                            <div class="font-medium">{{ $client->address ?: __('Not recorded') }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-zinc-500">{{ __('Recorded On') }}</div>
                            <div class="font-medium">{{ $client->created_at?->format('Y-m-d h:i:s a') }}</div>
                        </div>
                    </div>
                </section>
            </aside>

            <div class="space-y-6">
                <div class="flex flex-wrap gap-2 border-b border-zinc-200 pb-3 dark:border-zinc-700">
                    <flux:button wire:click="setTab('profile')" variant="{{ $activeTab === 'profile' ? 'primary' : 'ghost' }}" size="sm">{{ __('Profile') }}</flux:button>
                    <flux:button wire:click="setTab('guards')" variant="{{ $activeTab === 'guards' ? 'primary' : 'ghost' }}" size="sm">{{ __('Guards') }}</flux:button>
                    <flux:button wire:click="setTab('edit')" variant="{{ $activeTab === 'edit' ? 'primary' : 'ghost' }}" size="sm">{{ __('Edit Info') }}</flux:button>
                    <flux:button wire:click="setTab('documents')" variant="{{ $activeTab === 'documents' ? 'primary' : 'ghost' }}" size="sm">{{ __('Documents') }}</flux:button>
                </div>

                @if ($activeTab === 'profile')
                    <section class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ __('Bio data') }}</flux:heading>
                            <flux:separator class="mt-3" />
                        </div>

                        <dl class="grid gap-4 md:grid-cols-3">
                            <div>
                                <dt class="text-sm font-semibold">{{ __('Full Name') }}</dt>
                                <dd class="text-sm text-zinc-500">{{ $client->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold">{{ __('Contact 1') }}</dt>
                                <dd class="text-sm text-zinc-500">{{ $client->contact1 }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold">{{ __('Contact 2') }}</dt>
                                <dd class="text-sm text-zinc-500">{{ $client->contact2 }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold">{{ __('Email') }}</dt>
                                <dd class="text-sm text-zinc-500">{{ $client->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold">{{ __('Category') }}</dt>
                                <dd class="text-sm text-zinc-500">{{ $client->category?->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold">{{ __('Address') }}</dt>
                                <dd class="text-sm text-zinc-500">{{ $client->address }}</dd>
                            </div>
                        </dl>

                        <div>
                            <flux:heading size="lg">{{ __('Billing information') }}</flux:heading>
                            <flux:separator class="mt-3" />
                        </div>

                        <dl class="grid gap-4 md:grid-cols-3">
                            <div>
                                <dt class="text-sm font-semibold">{{ __('Bill Start Date') }}</dt>
                                <dd class="text-sm text-zinc-500">{{ $client->bill_start?->toDateString() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold">{{ __('Requested Guards') }}</dt>
                                <dd class="text-sm text-zinc-500">{{ number_format((float) $client->no_guards) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold">{{ __('Assigned Guards') }}</dt>
                                <dd class="text-sm text-zinc-500">{{ $client->activeGuards->count() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold">{{ __('Bill Amount') }}</dt>
                                <dd class="text-sm text-zinc-500">{{ number_format((float) $client->amount) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold">{{ __('Billing Cycle') }}</dt>
                                <dd class="text-sm text-zinc-500">{{ $client->billing_cycle?->label() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold">{{ __('Schedule Type') }}</dt>
                                <dd class="text-sm text-zinc-500">{{ $client->schedule_type?->label() }}</dd>
                            </div>
                        </dl>
                    </section>
                @endif

                @if ($activeTab === 'guards')
                    <section class="space-y-4">
                        <flux:heading size="lg">{{ __('Assigned Guards') }}</flux:heading>

                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('S/N') }}</flux:table.column>
                                <flux:table.column>{{ __('Name') }}</flux:table.column>
                                <flux:table.column>{{ __('Contact 1') }}</flux:table.column>
                                <flux:table.column>{{ __('Schedule Type') }}</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @forelse ($client->guards as $deployment)
                                    <flux:table.row :key="$deployment->id">
                                        <flux:table.cell>{{ $loop->iteration }}</flux:table.cell>
                                        <flux:table.cell>{{ trim(($deployment->securityGuard?->fname ?? '').' '.($deployment->securityGuard?->lname ?? '')) }}</flux:table.cell>
                                        <flux:table.cell>{{ $deployment->securityGuard?->contact1 }}</flux:table.cell>
                                        <flux:table.cell>{{ $deployment->schedule_type?->label() }}</flux:table.cell>
                                    </flux:table.row>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="4">
                                            <div class="py-8 text-center text-sm text-zinc-500">{{ __('No guards assigned to this client.') }}</div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            </flux:table.rows>
                        </flux:table>
                    </section>
                @endif

                @if ($activeTab === 'edit')
                    <section class="space-y-6">
                        @if (! $canEdit)
                            <flux:text>{{ __('You do not have permission to edit this client.') }}</flux:text>
                        @else
                            <form wire:submit="save" class="space-y-6">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <flux:input wire:model="name" label="{{ __('Full Name') }}" />
                                    <flux:input wire:model="contact1" label="{{ __('Contact 1') }}" />
                                    <flux:input wire:model="contact2" label="{{ __('Contact 2') }}" />
                                    <flux:input wire:model="email" label="{{ __('Email') }}" />
                                    <flux:input wire:model="idNo" label="{{ __('ID Number') }}" />
                                    <flux:input wire:model="tin" label="{{ __('TIN') }}" />
                                    <flux:input wire:model="address" label="{{ __('Address') }}" />

                                    <flux:field>
                                        <flux:label>{{ __('Category') }}</flux:label>
                                        <x-searchable-select
                                            wire:model="categoryId"
                                            :options="$categories"
                                            placeholder="Select Category"
                                        />
                                        <flux:error name="categoryId" />
                                    </flux:field>

                                    <flux:select wire:model="scheduleType" label="{{ __('Schedule') }}">
                                        <flux:select.option value="">{{ __('Select Schedule') }}</flux:select.option>
                                        @foreach ($scheduleTypes as $scheduleType)
                                            <flux:select.option value="{{ $scheduleType->value }}">{{ $scheduleType->label() }}</flux:select.option>
                                        @endforeach
                                    </flux:select>

                                    <flux:select wire:model="billingCycle" label="{{ __('Billing Cycle') }}">
                                        <flux:select.option value="">{{ __('Select Billing Cycle') }}</flux:select.option>
                                        @foreach ($billingCycles as $billingCycle)
                                            <flux:select.option value="{{ $billingCycle->value }}">{{ $billingCycle->label() }}</flux:select.option>
                                        @endforeach
                                    </flux:select>

                                    <flux:input wire:model="noGuards" type="number" step="0.01" label="{{ __('Number of Guards') }}" />
                                    <flux:input wire:model="amount" type="number" step="0.01" label="{{ __('Billing Amount') }}" />
                                    <flux:input wire:model="billStart" type="date" label="{{ __('Billing Start Date') }}" />
                                </div>

                                <flux:button type="submit" variant="primary">{{ __('Update Profile') }}</flux:button>
                            </form>
                        @endif
                    </section>
                @endif

                @if ($activeTab === 'documents')
                    <section class="space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <flux:heading size="lg">{{ __('Documents') }}</flux:heading>

                            @if ($canEdit)
                                <flux:button wire:click="openUpload" variant="primary" size="sm">{{ __('Upload New') }}</flux:button>
                            @endif
                        </div>

                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('S/N') }}</flux:table.column>
                                <flux:table.column>{{ __('Category') }}</flux:table.column>
                                <flux:table.column>{{ __('File') }}</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @forelse ($client->activeDocuments as $doc)
                                    <flux:table.row :key="$doc->id">
                                        <flux:table.cell>{{ $loop->iteration }}</flux:table.cell>
                                        <flux:table.cell>{{ $doc->type === 0 ? __('License') : __('Other') }}</flux:table.cell>
                                        <flux:table.cell>
                                            <flux:button :href="route('client-documents.download', $doc)" size="sm" variant="ghost">
                                                {{ $doc->title ?: $doc->original_name }}
                                            </flux:button>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="3">
                                            <div class="py-8 text-center text-sm text-zinc-500">{{ __('No documents uploaded for this client.') }}</div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            </flux:table.rows>
                        </flux:table>
                    </section>
                @endif
            </div>
        </div>

        <flux:modal wire:model.self="showUploadModal" class="md:w-[34rem]">
            <form wire:submit="saveDocument" class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Add Document') }}</flux:heading>
                    <flux:text class="mt-2">{{ __('Upload a private document for this client.') }}</flux:text>
                </div>

                <flux:select wire:model="documentType" label="{{ __('Category') }}">
                    <flux:select.option value="">{{ __('Select Category') }}</flux:select.option>
                    <flux:select.option value="0">{{ __('License') }}</flux:select.option>
                    <flux:select.option value="1">{{ __('Other') }}</flux:select.option>
                </flux:select>

                <flux:input wire:model="documentTitle" label="{{ __('Title') }}" />

                <flux:field>
                    <flux:label>{{ __('File') }}</flux:label>
                    <input
                        wire:model="document"
                        type="file"
                        accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx"
                        class="block w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 file:me-4 file:rounded file:border-0 file:bg-zinc-100 file:px-3 file:py-1.5 file:text-sm file:font-medium dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:file:bg-zinc-800"
                    />
                    <flux:error name="document" />
                </flux:field>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button type="button" variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">{{ __('Submit') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</section>
