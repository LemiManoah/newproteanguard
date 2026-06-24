<?php

namespace App\Livewire\Operations;

use App\Models\BulletAddition;
use App\Models\Gun;
use App\Services\GunService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Bullet Additions')]
class BulletAdditionsPage extends Component
{
    use WithPagination;

    public bool $showFormModal = false;

    public ?int $gunId = null;

    public ?string $date = null;

    public ?int $quantity = null;

    public ?string $broughtBy = null;

    public ?string $description = null;

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
        $this->reset('gunId', 'quantity', 'broughtBy', 'description');
        $this->date = now()->toDateString();
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->authorizeArmory();

        $validated = $this->validate([
            'gunId' => ['required', 'integer'],
            'date' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1'],
            'broughtBy' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Gun::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['gunId']);

        $addition = new BulletAddition;
        $addition->forceFill([
            'gunId' => $validated['gunId'],
            'date' => $validated['date'],
            'quantity' => $validated['quantity'],
            'brought_by' => $validated['broughtBy'],
            'description' => $validated['description'],
            'businessId' => $this->tenant->businessId(),
            'userId' => $this->tenant->user()->getKey(),
        ]);

        $this->guns->addBullets($addition);

        $this->showFormModal = false;
        Flux::toast(variant: 'success', text: __('Bullets added.'));
    }

    public function delete(int $id): void
    {
        $this->authorizeArmory();

        $addition = BulletAddition::query()->where('businessId', $this->tenant->businessId())->findOrFail($id);
        $this->guns->deleteAddition($addition);

        Flux::toast(variant: 'success', text: __('Bullet addition deleted.'));
    }

    public function render(): View
    {
        $this->authorizeArmory();

        return view('livewire.operations.bullet-additions-page', [
            'additions' => BulletAddition::query()
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
