<?php

namespace App\Livewire\Operations;

use App\Models\InventoryItem;
use App\Models\InventoryStockUsage;
use App\Models\SecurityGuard;
use App\Services\InventoryService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

#[Title('Stock Usage')]
class InventoryStockUsagesPage extends Component
{
    use WithPagination;

    public bool $showFormModal = false;

    public ?string $date = null;

    public ?int $itemId = null;

    public ?int $guardId = null;

    public ?string $quantity = null;

    public ?string $description = null;

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
        $this->reset('itemId', 'guardId', 'quantity', 'description');
        $this->date = now()->toDateString();
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->authorizeInventory();

        $validated = $this->validate([
            'date' => ['required', 'date'],
            'itemId' => ['required', 'integer'],
            'guardId' => ['nullable', 'integer'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        InventoryItem::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['itemId']);

        if ($validated['guardId']) {
            SecurityGuard::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['guardId']);
        }

        $usage = new InventoryStockUsage;
        $usage->forceFill([
            'date' => $validated['date'],
            'itemId' => $validated['itemId'],
            'guardId' => $validated['guardId'],
            'quantity' => $validated['quantity'],
            'description' => $validated['description'],
            'businessId' => $this->tenant->businessId(),
            'userId' => $this->tenant->user()->getKey(),
        ]);

        try {
            $this->inventory->useStock($usage);
        } catch (RuntimeException $exception) {
            $this->addError('quantity', $exception->getMessage());

            return;
        }

        $this->showFormModal = false;
        Flux::toast(variant: 'success', text: __('Stock usage recorded.'));
    }

    public function delete(int $id): void
    {
        $this->authorizeInventory();

        $usage = InventoryStockUsage::query()->where('businessId', $this->tenant->businessId())->findOrFail($id);
        $this->inventory->deleteUsage($usage);

        Flux::toast(variant: 'success', text: __('Stock usage deleted.'));
    }

    public function render(): View
    {
        $this->authorizeInventory();

        $businessId = $this->tenant->businessId();

        return view('livewire.operations.inventory-stock-usages-page', [
            'usages' => InventoryStockUsage::query()
                ->with(['item.unit', 'securityGuard'])
                ->where('businessId', $businessId)
                ->latest('date')
                ->paginate(15),
            'items' => InventoryItem::query()->where('businessId', $businessId)->where('status', true)->orderBy('name')->get(),
            'guards' => SecurityGuard::query()->where('businessId', $businessId)->where('status', true)->orderBy('fname')->orderBy('lname')->get(),
        ]);
    }

    protected function authorizeInventory(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_inventory'), Response::HTTP_FORBIDDEN);
    }
}
