<?php

namespace App\Livewire\Operations;

use App\Models\Gun;
use App\Models\GunMaintenance;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Maintenance Records')]
class GunMaintenancesPage extends Component
{
    use WithPagination;

    public bool $showFormModal = false;

    public ?int $gunId = null;

    public ?string $date = null;

    public ?string $workBy = null;

    public ?string $description = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    public function boot(TenantContext $tenant, PermissionService $permissions): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
    }

    public function create(): void
    {
        $this->authorizeArmory();
        $this->reset('gunId', 'workBy', 'description');
        $this->date = now()->toDateString();
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->authorizeArmory();

        $validated = $this->validate([
            'gunId' => ['required', 'integer'],
            'date' => ['required', 'date'],
            'workBy' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Gun::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['gunId']);

        $record = new GunMaintenance;
        $record->forceFill([
            'gunId' => $validated['gunId'],
            'date' => $validated['date'],
            'work_by' => $validated['workBy'],
            'description' => $validated['description'],
            'businessId' => $this->tenant->businessId(),
            'userId' => $this->tenant->user()->getKey(),
        ])->save();

        $this->showFormModal = false;
        Flux::toast(variant: 'success', text: __('Maintenance record saved.'));
    }

    public function delete(int $id): void
    {
        $this->authorizeArmory();

        GunMaintenance::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($id)
            ->delete();

        Flux::toast(variant: 'success', text: __('Maintenance record deleted.'));
    }

    public function render(): View
    {
        $this->authorizeArmory();

        return view('livewire.operations.gun-maintenances-page', [
            'records' => GunMaintenance::query()
                ->with('gun')
                ->where('businessId', $this->tenant->businessId())
                ->latest('date')
                ->paginate(15),
            'guns' => Gun::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('mark_number')
                ->get(),
        ]);
    }

    protected function authorizeArmory(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_guns'), Response::HTTP_FORBIDDEN);
    }
}
