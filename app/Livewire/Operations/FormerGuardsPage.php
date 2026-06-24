<?php

namespace App\Livewire\Operations;

use App\Models\SecurityGuard;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Former Guards')]
class FormerGuardsPage extends Component
{
    use WithPagination;

    public ?string $search = null;

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

    public function restore(int $id): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'edit_guards'), Response::HTTP_FORBIDDEN);

        $guard = SecurityGuard::query()
            ->where('businessId', $this->tenant->businessId())
            ->where('status', false)
            ->findOrFail($id);

        $guard->forceFill([
            'status' => true,
            'left_date' => null,
            'left_reason' => null,
        ])->save();

        Flux::toast(variant: 'success', text: __('Guard restored to active guards.'));
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'view_guards'), Response::HTTP_FORBIDDEN);

        return view('livewire.operations.former-guards-page', [
            'guards' => SecurityGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', false)
                ->when($this->search, function ($query): void {
                    $search = '%'.trim((string) $this->search).'%';

                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where('fname', 'like', $search)
                            ->orWhere('lname', 'like', $search)
                            ->orWhere('code', 'like', $search)
                            ->orWhere('contact1', 'like', $search)
                            ->orWhere('contact2', 'like', $search);
                    });
                })
                ->orderByDesc('left_date')
                ->paginate(15),
            'canEdit' => $this->permissions->can($this->tenant->user(), 'edit_guards'),
        ]);
    }
}
