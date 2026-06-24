<?php

namespace App\Livewire\Operations;

use App\Models\Client;
use App\Models\ClientCategory;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Former Clients')]
class FormerClientsPage extends Component
{
    use WithPagination;

    public ?string $search = null;

    public ?int $categoryId = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    public function boot(TenantContext $tenant, PermissionService $permissions): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'categoryId'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'categoryId']);
        $this->resetPage();
    }

    public function restore(int $id): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'edit_clients'), Response::HTTP_FORBIDDEN);

        $client = Client::query()
            ->where('businessId', $this->tenant->businessId())
            ->where('status', false)
            ->findOrFail($id);

        $client->forceFill([
            'status' => true,
            'left_date' => null,
            'left_reason' => null,
        ])->save();

        Flux::toast(variant: 'success', text: __('Client restored successfully.'));
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'view_clients'), Response::HTTP_FORBIDDEN);

        return view('livewire.operations.former-clients-page', [
            'clients' => Client::query()
                ->with('category')
                ->withCount('activeGuards')
                ->where('businessId', $this->tenant->businessId())
                ->where('status', false)
                ->when($this->categoryId, fn ($query) => $query->where('categoryId', $this->categoryId))
                ->when($this->search, function ($query): void {
                    $search = '%'.trim((string) $this->search).'%';

                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where('name', 'like', $search)
                            ->orWhere('contact1', 'like', $search)
                            ->orWhere('contact2', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
                })
                ->orderByDesc('left_date')
                ->orderBy('name')
                ->paginate(15),
            'categories' => ClientCategory::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('name')
                ->get(),
            'canRestore' => $this->permissions->can($this->tenant->user(), 'edit_clients'),
        ]);
    }
}
