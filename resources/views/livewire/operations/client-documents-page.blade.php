<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Client Documents') }}</flux:heading>
            <flux:text>{{ __('Private documents attached to client records.') }}</flux:text>
        </div>

        @if ($canUpload)
            <flux:button wire:click="openUpload" variant="primary">{{ __('Upload Document') }}</flux:button>
        @endif
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Client') }}</flux:table.column>
            <flux:table.column>{{ __('Title') }}</flux:table.column>
            <flux:table.column>{{ __('Original File') }}</flux:table.column>
            <flux:table.column>{{ __('Uploaded') }}</flux:table.column>
            <flux:table.column>{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($documents as $document)
                <flux:table.row :key="$document->id">
                    <flux:table.cell>{{ $document->client?->name }}</flux:table.cell>
                    <flux:table.cell>{{ $document->title }}</flux:table.cell>
                    <flux:table.cell>{{ $document->original_name }}</flux:table.cell>
                    <flux:table.cell>{{ $document->created_at?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button :href="route('client-documents.download', $document)" size="sm">{{ __('Download') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model.self="showUploadModal" class="md:w-[34rem]">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Upload Client Document') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Files are stored on the private client document disk.') }}</flux:text>
            </div>

            <flux:select wire:model="clientId" label="{{ __('Client') }}" placeholder="{{ __('Choose client') }}">
                @foreach ($clients as $client)
                    <flux:select.option value="{{ $client->id }}">{{ $client->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="title" label="{{ __('Title') }}" />

            <flux:select wire:model="type" label="{{ __('Type') }}">
                <flux:select.option value="0">{{ __('Profile Photo') }}</flux:select.option>
                <flux:select.option value="1">{{ __('ID') }}</flux:select.option>
                <flux:select.option value="2">{{ __('LC Letter') }}</flux:select.option>
                <flux:select.option value="3">{{ __('Other') }}</flux:select.option>
            </flux:select>

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

            @if ($document)
                <div class="rounded-md border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-700">
                    {{ $document->getClientOriginalName() }}
                </div>
            @endif

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Upload') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
