<?php

namespace App\Livewire\Operations;

use App\Enums\AvailabilityStatus;
use App\Enums\GunOwnerType;
use App\Models\Gun;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Guns')]
class GunsPage extends Component
{
    use WithPagination;

    public ?string $search = null;

    public ?string $availability = null;

    public bool $showFormModal = false;

    public ?int $editingId = null;

    public ?string $type = null;

    public ?string $serialNumber = null;

    public ?string $markNumber = null;

    public ?int $bullets = null;

    public ?int $owner = null;

    public ?string $vendorName = null;

    public ?string $vendorContact = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    public function boot(TenantContext $tenant, PermissionService $permissions): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'availability'], true)) {
            $this->resetPage();
        }
    }

    public function create(): void
    {
        $this->authorizeArmory();
        $this->reset('editingId', 'type', 'serialNumber', 'markNumber', 'bullets', 'owner', 'vendorName', 'vendorContact');
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeArmory();

        $gun = Gun::query()->where('businessId', $this->tenant->businessId())->findOrFail($id);

        $this->editingId = $gun->getKey();
        $this->type = $gun->type;
        $this->serialNumber = $gun->serial_number;
        $this->markNumber = $gun->mark_number;
        $this->bullets = $gun->bullets;
        $this->owner = $gun->owner?->value;
        $this->vendorName = $gun->vendor_name;
        $this->vendorContact = $gun->vendor_contact;
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->authorizeArmory();

        $validated = $this->validate([
            'type' => ['required', 'string', 'max:255'],
            'serialNumber' => ['required', 'string', 'max:255'],
            'markNumber' => ['required', 'string', 'max:255'],
            'bullets' => ['required', 'integer', 'min:0'],
            'owner' => ['required', 'integer', Rule::in(array_column(GunOwnerType::cases(), 'value'))],
            'vendorName' => ['nullable', 'string', 'max:255'],
            'vendorContact' => ['nullable', 'string', 'max:255'],
        ]);

        $gun = $this->editingId
            ? Gun::query()->where('businessId', $this->tenant->businessId())->findOrFail($this->editingId)
            : new Gun;

        $gun->forceFill([
            'type' => $validated['type'],
            'serial_number' => $validated['serialNumber'],
            'mark_number' => $validated['markNumber'],
            'bullets' => $validated['bullets'],
            'owner' => $validated['owner'],
            'vendor_name' => $validated['vendorName'],
            'vendor_contact' => $validated['vendorContact'],
            'available' => $gun->exists ? $gun->available->value : AvailabilityStatus::Available->value,
            'status' => true,
            'businessId' => $this->tenant->businessId(),
            'userId' => $this->tenant->user()->getKey(),
        ])->save();

        $this->showFormModal = false;
        Flux::toast(variant: 'success', text: __('Gun saved.'));
    }

    public function delete(int $id): void
    {
        $this->authorizeArmory();

        Gun::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($id)
            ->forceFill(['status' => false])
            ->save();

        Flux::toast(variant: 'success', text: __('Gun deleted.'));
    }

    public function render(): View
    {
        $this->authorizeArmory();

        $baseQuery = Gun::query()->where('businessId', $this->tenant->businessId())->where('status', true);

        return view('livewire.operations.guns-page', [
            'guns' => (clone $baseQuery)
                ->when($this->search, function ($query): void {
                    $search = '%'.trim((string) $this->search).'%';
                    $query->where(fn ($query) => $query
                        ->where('type', 'like', $search)
                        ->orWhere('serial_number', 'like', $search)
                        ->orWhere('mark_number', 'like', $search));
                })
                ->when($this->availability !== null && $this->availability !== '', fn ($query) => $query->where('available', $this->availability))
                ->orderBy('mark_number')
                ->paginate(15),
            'allGunsCount' => (clone $baseQuery)->count(),
            'assignedGunsCount' => (clone $baseQuery)->where('available', AvailabilityStatus::Unavailable->value)->count(),
            'availableGunsCount' => (clone $baseQuery)->where('available', AvailabilityStatus::Available->value)->count(),
            'availableBulletsCount' => (clone $baseQuery)->get()->sum(fn (Gun $gun) => $gun->available_bullets),
            'owners' => GunOwnerType::cases(),
        ]);
    }

    protected function authorizeArmory(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_guns'), Response::HTTP_FORBIDDEN);
    }
}
