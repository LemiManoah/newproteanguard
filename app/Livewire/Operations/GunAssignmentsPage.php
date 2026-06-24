<?php

namespace App\Livewire\Operations;

use App\Enums\AvailabilityStatus;
use App\Models\Gun;
use App\Models\GunAssignment;
use App\Models\SecurityGuard;
use App\Services\GunService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use RuntimeException;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Gun Assignment')]
class GunAssignmentsPage extends Component
{
    use WithPagination;

    public ?int $guardId = null;

    public ?int $gunId = null;

    public ?string $startDate = null;

    public ?string $description = null;

    public ?int $editingId = null;

    public bool $showFormModal = false;

    public bool $showRemoveModal = false;

    public ?int $removingId = null;

    public ?string $endDate = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    protected GunService $guns;

    public function boot(TenantContext $tenant, PermissionService $permissions, GunService $guns): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
        $this->guns = $guns;
    }

    public function create(): void
    {
        $this->authorizeArmory();
        $this->reset('guardId', 'gunId', 'startDate', 'description', 'editingId');
        $this->startDate = now()->toDateString();
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeArmory();

        $assignment = GunAssignment::query()->where('businessId', $this->tenant->businessId())->findOrFail($id);
        $this->editingId = $assignment->getKey();
        $this->startDate = $assignment->start_date?->toDateString();
        $this->description = $assignment->description;
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->authorizeArmory();

        if ($this->editingId) {
            $validated = $this->validate([
                'startDate' => ['required', 'date', 'before_or_equal:today'],
                'description' => ['nullable', 'string', 'max:255'],
            ]);

            GunAssignment::query()
                ->where('businessId', $this->tenant->businessId())
                ->findOrFail($this->editingId)
                ->forceFill([
                    'start_date' => $validated['startDate'],
                    'description' => $validated['description'],
                ])
                ->save();
        } else {
            $validated = $this->validate([
                'guardId' => ['required', 'integer'],
                'gunId' => ['required', 'integer'],
                'startDate' => ['required', 'date', 'before_or_equal:today'],
                'description' => ['nullable', 'string', 'max:255'],
            ]);

            SecurityGuard::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['guardId']);
            $gun = Gun::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->where('available', AvailabilityStatus::Available->value)
                ->findOrFail($validated['gunId']);

            try {
                $this->guns->assignGun($gun, $validated['guardId'], $validated['startDate'], $validated['description'], $this->tenant->businessId(), $this->tenant->user()->getKey());
            } catch (RuntimeException $exception) {
                $this->addError('gunId', $exception->getMessage());

                return;
            }
        }

        $this->showFormModal = false;
        Flux::toast(variant: 'success', text: __('Gun assignment saved.'));
    }

    public function confirmRemove(int $id): void
    {
        $this->authorizeArmory();
        $this->removingId = $id;
        $this->endDate = now()->toDateString();
        $this->showRemoveModal = true;
    }

    public function remove(): void
    {
        $this->authorizeArmory();

        $validated = $this->validate([
            'removingId' => ['required', 'integer'],
            'endDate' => ['required', 'date'],
        ]);

        $assignment = GunAssignment::query()
            ->with('gun')
            ->where('businessId', $this->tenant->businessId())
            ->where('status', true)
            ->findOrFail($validated['removingId']);

        $this->guns->removeAssignment($assignment, $validated['endDate']);

        $this->showRemoveModal = false;
        $this->reset('removingId', 'endDate');
        Flux::toast(variant: 'success', text: __('Gun assignment removed.'));
    }

    public function render(): View
    {
        $this->authorizeArmory();

        return view('livewire.operations.gun-assignments-page', [
            'assigned' => GunAssignment::query()
                ->with(['gun', 'securityGuard'])
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->latest('start_date')
                ->paginate(15),
            'guards' => SecurityGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('fname')
                ->get(),
            'availableGuns' => Gun::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->where('available', AvailabilityStatus::Available->value)
                ->orderBy('mark_number')
                ->get(),
        ]);
    }

    protected function authorizeArmory(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_guns'), Response::HTTP_FORBIDDEN);
    }
}
