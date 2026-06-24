<?php

namespace App\Livewire\Operations;

use App\Models\InventoryCategory;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Inventory Categories')]
class InventoryCategoriesPage extends Component
{
    use WithPagination;

    public ?string $search = null;

    public bool $showFormModal = false;

    public ?int $editingId = null;

    public ?string $name = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    public function boot(TenantContext $tenant, PermissionService $permissions): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorizeInventory();
        $this->reset('editingId', 'name');
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeInventory();
        $category = InventoryCategory::query()->where('businessId', $this->tenant->businessId())->findOrFail($id);
        $this->editingId = $category->getKey();
        $this->name = $category->name;
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->authorizeInventory();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('inventory_categories', 'name')->where('businessId', $this->tenant->businessId())->ignore($this->editingId)],
        ]);

        $category = $this->editingId
            ? InventoryCategory::query()->where('businessId', $this->tenant->businessId())->findOrFail($this->editingId)
            : new InventoryCategory;

        $category->forceFill([
            'name' => $validated['name'],
            'status' => true,
            'businessId' => $this->tenant->businessId(),
            'userId' => $this->tenant->user()->getKey(),
        ])->save();

        $this->showFormModal = false;
        Flux::toast(variant: 'success', text: __('Inventory category saved.'));
    }

    public function delete(int $id): void
    {
        $this->authorizeInventory();

        InventoryCategory::query()->where('businessId', $this->tenant->businessId())->findOrFail($id)->forceFill(['status' => false])->save();

        Flux::toast(variant: 'success', text: __('Inventory category deleted.'));
    }

    public function render(): View
    {
        $this->authorizeInventory();

        return view('livewire.operations.inventory-categories-page', [
            'categories' => InventoryCategory::query()
                ->withCount('items')
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->when($this->search, fn ($query) => $query->where('name', 'like', '%'.trim((string) $this->search).'%'))
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    protected function authorizeInventory(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_inventory'), Response::HTTP_FORBIDDEN);
    }
}
