<?php

namespace App\Livewire\Operations;

use App\Models\BulletUsage;
use App\Models\Gun;
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

#[Title('Bullet Usage')]
class BulletUsagePage extends Component
{
    use WithPagination;

    public bool $showFormModal = false;

    public ?int $gunId = null;

    public ?int $guardId = null;

    public ?string $date = null;

    public ?int $quantity = null;

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
        $this->reset('gunId', 'guardId', 'quantity', 'description');
        $this->date = now()->toDateString();
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->authorizeArmory();

        $validated = $this->validate([
            'gunId' => ['required', 'integer'],
            'guardId' => ['required', 'integer'],
            'date' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Gun::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['gunId']);
        SecurityGuard::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['guardId']);

        $usage = new BulletUsage;
        $usage->forceFill([
            'gunId' => $validated['gunId'],
            'guardId' => $validated['guardId'],
            'date' => $validated['date'],
            'quantity' => $validated['quantity'],
            'description' => $validated['description'],
            'businessId' => $this->tenant->businessId(),
            'userId' => $this->tenant->user()->getKey(),
        ]);

        try {
            $this->guns->recordUsage($usage);
        } catch (RuntimeException $exception) {
            $this->addError('quantity', $exception->getMessage());

            return;
        }

        $this->showFormModal = false;
        Flux::toast(variant: 'success', text: __('Bullet usage recorded.'));
    }

    public function delete(int $id): void
    {
        $this->authorizeArmory();

        $usage = BulletUsage::query()->where('businessId', $this->tenant->businessId())->findOrFail($id);
        $this->guns->deleteUsage($usage);

        Flux::toast(variant: 'success', text: __('Bullet usage deleted.'));
    }

    public function render(): View
    {
        $this->authorizeArmory();

        return view('livewire.operations.bullet-usage-page', [
            'used' => BulletUsage::query()
                ->with(['gun', 'securityGuard'])
                ->where('businessId', $this->tenant->businessId())
                ->latest('date')
                ->paginate(15),
            'guns' => Gun::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('mark_number')
                ->get(),
            'guards' => SecurityGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('fname')
                ->get(),
        ]);
    }

    protected function authorizeArmory(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'manage_guns'), Response::HTTP_FORBIDDEN);
    }
}
