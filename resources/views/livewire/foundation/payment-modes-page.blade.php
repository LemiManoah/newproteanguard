<section class="flex h-full w-full flex-1 flex-col gap-6">
    <flux:heading size="xl">{{ __('Payment Modes') }}</flux:heading>

    <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Type') }}</th>
                    <th class="px-4 py-3">{{ __('Account') }}</th>
                    <th class="px-4 py-3">{{ __('Opening Balance') }}</th>
                    <th class="px-4 py-3">{{ __('Default') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach ($paymentModes as $paymentMode)
                    <tr>
                        <td class="px-4 py-3">{{ $paymentMode->name }}</td>
                        <td class="px-4 py-3">{{ $paymentMode->type?->label() }}</td>
                        <td class="px-4 py-3">{{ $paymentMode->account }}</td>
                        <td class="px-4 py-3">{{ number_format((float) $paymentMode->opening_balance) }}</td>
                        <td class="px-4 py-3">
                            @if ($paymentMode->is_default)
                                {{ __('Yes') }}
                            @elseif ($canEdit)
                                <button type="button" wire:click="setDefault({{ $paymentMode->id }})" class="rounded border border-zinc-300 px-3 py-1 text-xs dark:border-zinc-600">
                                    {{ __('Set') }}
                                </button>
                            @else
                                {{ __('No') }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
