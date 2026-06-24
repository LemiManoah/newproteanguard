<?php

namespace App\Livewire\Operations;

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

#[Title('Inventory Units')]
class InventoryUnitsPage extends Component
{
    use WithPagination;

    public ?string $search = null;

    public bool $showFormModal = false;

    public ?int $editingId = null;

    public ?string $symbol = null;

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
        $this->reset('editingId', 'symbol');
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeInventory();
        $unit = Unit::query()->where('businessId', $this->tenant->businessId())->findOrFail($id);
        $this->editingId = $unit->getKey();
        $this->symbol = $unit->symbol;
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->authorizeInventory();

        $validated = $this->validate([
            'symbol' => ['required', 'string', 'max:50', Rule::unique('units', 'symbol')->where('businessId', $this->tenant->businessId())->ignore($this->editingId)],
        ]);

        $unit = $this->editingId
            ? Unit::query()->where('businessId', $this->tenant->businessId())->findOrFail($this->editingId)
            : new Unit;

        $unit->forceFill([
            'symbol' => $validated['symbol'],
            'status' => true,
            'businessId' => $this->tenant->businessId(),
            'userId' => $this->tenant->user()->getKey(),
        ])->save();

        $this->showFormModal = false;
        Flux::toast(variant: 'success', text: __('Inventory unit saved.'));
    }

    public function delete(int $id): void
    {
        $this->authorizeInventory();

        Unit::query()->where('businessId', $this->tenant->businessId())->findOrFail($id)->forceFill(['status' => false])->save();

        Flux::toast(variant: 'success', text: __('Inventory unit deleted.'));
    }

    public function render(): View
    {
        $this->authorizeInventory();

        return view('livewire.operations.inventory-units-page', [
            'units' => Unit::query()
                ->withCount('items')
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->when($this->search, fn ($query) => $query->where('symbol', 'like', '%'.trim((string) $this->search).'%'))
                ->orderBy('symbol')
                ->paginate(15),
        ]);
    }

    protected function authorizeInventory(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_inventory'), Response::HTTP_FORBIDDEN);
    }
}
