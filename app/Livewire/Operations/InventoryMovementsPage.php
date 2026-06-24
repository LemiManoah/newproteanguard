<?php

namespace App\Livewire\Operations;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Inventory Movements')]
class InventoryMovementsPage extends Component
{
    use WithPagination;

    public ?int $itemFilter = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    public function boot(TenantContext $tenant, PermissionService $permissions): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
    }

    public function updatedItemFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $this->authorizeInventory();

        $businessId = $this->tenant->businessId();

        return view('livewire.operations.inventory-movements-page', [
            'movements' => InventoryMovement::query()
                ->with('item.unit')
                ->where('businessId', $businessId)
                ->when($this->itemFilter, fn ($query) => $query->where('itemId', $this->itemFilter))
                ->latest('date')
                ->paginate(15),
            'items' => InventoryItem::query()->where('businessId', $businessId)->where('status', true)->orderBy('name')->get(),
        ]);
    }

    protected function authorizeInventory(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_inventory'), Response::HTTP_FORBIDDEN);
    }
}
