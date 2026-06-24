<?php

namespace App\Livewire\Operations;

use App\Models\InventoryItem;
use App\Models\InventoryStockin;
use App\Models\PaymentMode;
use App\Models\StockInCart;
use App\Services\InventoryService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Stock In')]
class InventoryStockInsPage extends Component
{
    use WithPagination;

    public ?string $date = null;

    public ?int $itemId = null;

    public ?string $quantity = null;

    public ?string $buyingPrice = null;

    public ?int $paymentMode = null;

    public ?string $paid = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    protected InventoryService $inventory;

    public function boot(TenantContext $tenant, PermissionService $permissions, InventoryService $inventory): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
        $this->inventory = $inventory;
    }

    public function create(): void
    {
        $this->authorizeInventory();
        $this->reset('itemId', 'quantity', 'buyingPrice');
    }

    public function mount(): void
    {
        $this->date = now()->toDateString();
    }

    public function addToCart(): void
    {
        $this->authorizeInventory();

        $validated = $this->validate([
            'itemId' => ['required', 'integer'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'buyingPrice' => ['required', 'numeric', 'min:0'],
        ]);

        InventoryItem::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['itemId']);

        $cartItem = StockInCart::query()->firstOrNew([
            'businessId' => $this->tenant->businessId(),
            'userId' => $this->tenant->user()->getKey(),
            'itemId' => $validated['itemId'],
        ]);

        $cartItem->forceFill([
            'quantity' => (float) $cartItem->quantity + (float) $validated['quantity'],
            'buying_price' => $validated['buyingPrice'],
            'businessId' => $this->tenant->businessId(),
            'userId' => $this->tenant->user()->getKey(),
        ])->save();

        $this->reset('itemId', 'quantity', 'buyingPrice');
        Flux::toast(variant: 'success', text: __('Item added to cart.'));
    }

    public function removeCartItem(int $id): void
    {
        $this->authorizeInventory();

        StockInCart::query()
            ->where('businessId', $this->tenant->businessId())
            ->where('userId', $this->tenant->user()->getKey())
            ->findOrFail($id)
            ->delete();

        Flux::toast(variant: 'success', text: __('Cart item removed.'));
    }

    public function clearCart(): void
    {
        $this->authorizeInventory();

        StockInCart::query()
            ->where('businessId', $this->tenant->businessId())
            ->where('userId', $this->tenant->user()->getKey())
            ->delete();

        Flux::toast(variant: 'success', text: __('Stock in cart cleared.'));
    }

    public function save(): void
    {
        $this->authorizeInventory();

        $validated = $this->validate([
            'date' => ['required', 'date'],
            'paymentMode' => ['nullable', 'integer'],
            'paid' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($validated['paymentMode']) {
            PaymentMode::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['paymentMode']);
        }

        $stockIn = new InventoryStockin;
        $stockIn->forceFill([
            'date' => $validated['date'],
            'paid' => $validated['paid'] ?: 0,
            'paymentMode' => $validated['paymentMode'],
            'businessId' => $this->tenant->businessId(),
            'userId' => $this->tenant->user()->getKey(),
        ]);

        try {
            $this->inventory->stockInFromCart($stockIn);
        } catch (\RuntimeException $exception) {
            $this->addError('cart', $exception->getMessage());

            return;
        }

        $this->reset('paymentMode', 'paid');
        $this->date = now()->toDateString();
        Flux::toast(variant: 'success', text: __('Stock in cart submitted.'));
    }

    public function delete(int $id): void
    {
        $this->authorizeInventory();

        $stockIn = InventoryStockin::query()->with('details')->where('businessId', $this->tenant->businessId())->findOrFail($id);
        $this->inventory->deleteStockIn($stockIn);

        Flux::toast(variant: 'success', text: __('Stock in record deleted.'));
    }

    public function render(): View
    {
        $this->authorizeInventory();

        $businessId = $this->tenant->businessId();

        return view('livewire.operations.inventory-stock-ins-page', [
            'stockIns' => InventoryStockin::query()
                ->with(['details.item', 'mode'])
                ->where('businessId', $businessId)
                ->latest('date')
                ->paginate(15),
            'items' => InventoryItem::query()->where('businessId', $businessId)->where('status', true)->orderBy('name')->get(),
            'paymentModes' => PaymentMode::query()->where('businessId', $businessId)->where('status', true)->orderBy('name')->get(),
            'cartItems' => StockInCart::query()
                ->with('item.unit')
                ->where('businessId', $businessId)
                ->where('userId', $this->tenant->user()->getKey())
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    protected function authorizeInventory(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_inventory'), Response::HTTP_FORBIDDEN);
    }
}
