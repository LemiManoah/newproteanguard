<?php

namespace App\Livewire\Operations;

use App\Enums\InventoryMovementType;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Unit;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Inventory Items')]
class InventoryItemsPage extends Component
{
    use WithPagination;

    public ?string $search = null;

    public ?int $categoryFilter = null;

    public bool $showFormModal = false;

    public ?int $editingId = null;

    public ?int $categoryId = null;

    public ?int $unitId = null;

    public ?string $name = null;

    public ?string $quantity = null;

    public ?string $openingStock = null;

    public ?string $buyingPrice = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    public function boot(TenantContext $tenant, PermissionService $permissions): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'categoryFilter'], true)) {
            $this->resetPage();
        }
    }

    public function create(): void
    {
        $this->authorizeInventory();
        $this->reset('editingId', 'categoryId', 'unitId', 'name', 'quantity', 'openingStock', 'buyingPrice');
        $this->quantity = '0';
        $this->openingStock = '0';
        $this->buyingPrice = '0';
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeInventory();

        $item = InventoryItem::query()->where('businessId', $this->tenant->businessId())->findOrFail($id);
        $this->editingId = $item->getKey();
        $this->categoryId = $item->categoryId;
        $this->unitId = $item->unitId;
        $this->name = $item->name;
        $this->quantity = (string) $item->quantity;
        $this->openingStock = (string) $item->opening_stock;
        $this->buyingPrice = (string) $item->buying_price;
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->authorizeInventory();

        $validated = $this->validate([
            'categoryId' => ['required', 'integer'],
            'unitId' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255', Rule::unique('inventory_items', 'name')->where('businessId', $this->tenant->businessId())->ignore($this->editingId)],
            'quantity' => ['required', 'numeric', 'min:0'],
            'openingStock' => ['required', 'numeric', 'min:0'],
            'buyingPrice' => ['required', 'numeric', 'min:0'],
        ]);

        InventoryCategory::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['categoryId']);
        Unit::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['unitId']);

        $item = $this->editingId
            ? InventoryItem::query()->where('businessId', $this->tenant->businessId())->findOrFail($this->editingId)
            : new InventoryItem;

        $item->forceFill([
            'categoryId' => $validated['categoryId'],
            'unitId' => $validated['unitId'],
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'opening_stock' => $validated['openingStock'],
            'buying_price' => $validated['buyingPrice'],
            'last_buying_price' => $validated['buyingPrice'],
            'status' => true,
            'businessId' => $this->tenant->businessId(),
            'userId' => $this->tenant->user()->getKey(),
        ])->save();

        if (! $this->editingId && (float) $validated['openingStock'] > 0) {
            $movement = new InventoryMovement;
            $movement->forceFill([
                'date' => now()->toDateString(),
                'itemId' => $item->getKey(),
                'quantity_in' => $validated['openingStock'],
                'quantity_out' => 0,
                'description' => 'Opening stock',
                'type' => InventoryMovementType::Opening->value,
                'businessId' => $this->tenant->businessId(),
                'userId' => $this->tenant->user()->getKey(),
            ])->save();
        }

        $this->showFormModal = false;
        Flux::toast(variant: 'success', text: __('Inventory item saved.'));
    }

    public function delete(int $id): void
    {
        $this->authorizeInventory();

        InventoryItem::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($id)
            ->forceFill(['status' => false])
            ->save();

        Flux::toast(variant: 'success', text: __('Inventory item deleted.'));
    }

    public function render(): View
    {
        $this->authorizeInventory();

        $businessId = $this->tenant->businessId();

        return view('livewire.operations.inventory-items-page', [
            'items' => InventoryItem::query()
                ->with(['category', 'unit'])
                ->where('businessId', $businessId)
                ->where('status', true)
                ->when($this->search, fn ($query) => $query->where('name', 'like', '%'.trim((string) $this->search).'%'))
                ->when($this->categoryFilter, fn ($query) => $query->where('categoryId', $this->categoryFilter))
                ->orderBy('name')
                ->paginate(15),
            'categories' => InventoryCategory::query()->where('businessId', $businessId)->where('status', true)->orderBy('name')->get(),
            'units' => Unit::query()->where('businessId', $businessId)->where('status', true)->orderBy('symbol')->get(),
            'itemsCount' => InventoryItem::query()->where('businessId', $businessId)->where('status', true)->count(),
            'stockValue' => InventoryItem::query()->where('businessId', $businessId)->where('status', true)->get()->sum(fn (InventoryItem $item) => (float) $item->quantity * (float) $item->last_buying_price),
        ]);
    }

    protected function authorizeInventory(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_inventory'), Response::HTTP_FORBIDDEN);
    }
}
