<?php

namespace App\Livewire\Operations;

use App\Models\Client;
use App\Models\ClientGuard;
use App\Models\ClientCategory;
use App\Models\SecurityGuard;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Clients')]
class ClientsPage extends Component
{
    use WithPagination;

    public ?string $search = null;

    public ?int $categoryId = null;

    public ?string $allocation = null;

    public bool $showFormerModal = false;

    public ?int $formerClientId = null;

    public ?string $leftDate = null;

    public ?string $leftReason = null;

    public ?string $formerClientName = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    public function boot(TenantContext $tenant, PermissionService $permissions): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'categoryId', 'allocation'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'categoryId', 'allocation']);
        $this->resetPage();
    }

    public function markFormer(int $id): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'delete_clients'), Response::HTTP_FORBIDDEN);

        $client = Client::query()
            ->where('businessId', $this->tenant->businessId())
            ->where('status', true)
            ->findOrFail($id);

        $this->formerClientId = $client->getKey();
        $this->formerClientName = $client->name;
        $this->leftDate = now()->toDateString();
        $this->leftReason = null;
        $this->showFormerModal = true;
    }

    public function confirmFormer(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'delete_clients'), Response::HTTP_FORBIDDEN);

        $validated = $this->validate([
            'formerClientId' => ['required', 'integer'],
            'leftDate' => ['required', 'date'],
            'leftReason' => ['required', 'string', 'max:255'],
        ]);

        $client = Client::query()
            ->where('businessId', $this->tenant->businessId())
            ->where('status', true)
            ->findOrFail($validated['formerClientId']);

        DB::transaction(function () use ($client, $validated): void {
            $guardIds = $client->activeGuards()->pluck('guardId');

            $client->forceFill([
                'status' => false,
                'assigned' => false,
                'actual_guards' => 0,
                'left_date' => $validated['leftDate'],
                'left_reason' => $validated['leftReason'],
            ])->save();

            ClientGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('clientId', $client->getKey())
                ->where('status', true)
                ->update([
                    'status' => false,
                    'to' => $validated['leftDate'],
                ]);

            SecurityGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->whereIn('id', $guardIds)
                ->update(['assigned' => false]);
        });

        $this->showFormerModal = false;
        $this->reset(['formerClientId', 'formerClientName', 'leftDate', 'leftReason']);

        Flux::toast(variant: 'success', text: __('Client moved to former clients.'));
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'view_clients'), Response::HTTP_FORBIDDEN);

        return view('livewire.operations.clients-page', [
            'clients' => Client::query()
                ->with('category')
                ->withCount('activeGuards')
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
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
                ->when($this->allocation, function ($query): void {
                    match ($this->allocation) {
                        'unassigned' => $query->whereDoesntHave('activeGuards'),
                        'under' => $query->whereRaw('(select count(*) from client_guards where client_guards.clientId = clients.id and client_guards.status = 1) < clients.no_guards'),
                        'full' => $query->whereRaw('(select count(*) from client_guards where client_guards.clientId = clients.id and client_guards.status = 1) >= clients.no_guards'),
                        default => null,
                    };
                })
                ->orderBy('name')
                ->paginate(15),
            'categories' => ClientCategory::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('name')
                ->get(),
            'canDelete' => $this->permissions->can($this->tenant->user(), 'delete_clients'),
        ]);
    }
}
