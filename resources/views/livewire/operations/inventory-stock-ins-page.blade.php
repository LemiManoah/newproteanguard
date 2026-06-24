<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Stock In') }}</flux:heading>
            <flux:text>{{ __('Record new inventory stock received.') }}</flux:text>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1fr_1.2fr]">
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <form wire:submit="addToCart" class="space-y-4">
                <flux:heading size="lg">{{ __('Add Item to Cart') }}</flux:heading>

                <flux:field>
                    <flux:label>{{ __('Item') }}</flux:label>
                    <x-searchable-select wire:model="itemId" :options="$items" placeholder="Select item" />
                    <flux:error name="itemId" />
                </flux:field>

                <div class="grid gap-3 md:grid-cols-2">
                    <flux:input wire:model="quantity" type="number" step="0.01" min="0.01" label="{{ __('Quantity') }}" />
                    <flux:input wire:model="buyingPrice" type="number" step="0.01" min="0" label="{{ __('Buying Price') }}" />
                </div>

                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary" icon="plus">{{ __('Add to Cart') }}</flux:button>
                </div>
            </form>
        </div>

        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="mb-4 flex items-start justify-between gap-4">
                <div>
                    <flux:heading size="lg">{{ __('Stock In Cart') }}</flux:heading>
                    <flux:text>{{ __('Review items before submitting stock in.') }}</flux:text>
                </div>

                @if ($cartItems->isNotEmpty())
                    <flux:button wire:click="clearCart" wire:confirm="{{ __('Clear all cart items?') }}" size="sm" variant="ghost">{{ __('Clear') }}</flux:button>
                @endif
            </div>

            <flux:error name="cart" />

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Item') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Qty') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Price') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Total') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($cartItems as $cartItem)
                        <flux:table.row :key="$cartItem->id">
                            <flux:table.cell variant="strong">{{ $cartItem->item?->name }}</flux:table.cell>
                            <flux:table.cell align="end">{{ number_format((float) $cartItem->quantity, 2) }} {{ $cartItem->item?->unit?->symbol }}</flux:table.cell>
                            <flux:table.cell align="end">{{ number_format((float) $cartItem->buying_price, 2) }}</flux:table.cell>
                            <flux:table.cell align="end">{{ number_format((float) $cartItem->quantity * (float) $cartItem->buying_price, 2) }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <flux:button wire:click="removeCartItem({{ $cartItem->id }})" size="sm" variant="danger">{{ __('Remove') }}</flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5">
                                <div class="py-6 text-center text-sm text-zinc-500">{{ __('No items in the stock in cart.') }}</div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

            <form wire:submit="save" class="mt-5 grid gap-3 md:grid-cols-[1fr_1fr_1fr_auto] md:items-end">
                <flux:input wire:model="date" type="date" label="{{ __('Date') }}" />

                <flux:field>
                    <flux:label>{{ __('Payment Mode') }}</flux:label>
                    <x-searchable-select wire:model="paymentMode" :options="$paymentModes" placeholder="Select payment mode" />
                    <flux:error name="paymentMode" />
                </flux:field>

                <flux:input wire:model="paid" type="number" step="0.01" min="0" label="{{ __('Amount Paid') }}" />

                <flux:button type="submit" variant="primary">{{ __('Submit Stock') }}</flux:button>
            </form>
        </div>
    </div>

    <flux:table :paginate="$stockIns" pagination:scroll-to>
        <flux:table.columns>
            <flux:table.column>{{ __('S/N') }}</flux:table.column>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column>{{ __('Items') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Total') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Paid') }}</flux:table.column>
            <flux:table.column>{{ __('Payment Mode') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Action') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($stockIns as $stockIn)
                <flux:table.row :key="$stockIn->id">
                    <flux:table.cell>{{ $stockIns->firstItem() + $loop->index }}</flux:table.cell>
                    <flux:table.cell>{{ $stockIn->date?->toFormattedDateString() }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="space-y-1">
                            @foreach ($stockIn->details as $detail)
                                <div>
                                    <span class="font-medium">{{ $detail->item?->name }}</span>
                                    <span class="text-xs text-zinc-500">({{ number_format((float) $detail->quantity, 2) }})</span>
                                </div>
                            @endforeach
                        </div>
                    </flux:table.cell>
                    <flux:table.cell align="end">{{ number_format((float) $stockIn->total, 2) }}</flux:table.cell>
                    <flux:table.cell align="end">{{ number_format((float) $stockIn->paid, 2) }}</flux:table.cell>
                    <flux:table.cell>{{ $stockIn->mode?->name }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button wire:click="delete({{ $stockIn->id }})" wire:confirm="{{ __('Are you sure you want to delete this stock in record?') }}" size="sm" variant="danger">{{ __('Delete') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="7"><div class="py-8 text-center text-sm text-zinc-500">{{ __('No stock in records found.') }}</div></flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
