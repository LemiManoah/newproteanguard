<?php

namespace App\Livewire\Operations;

use App\Models\ClientGuard;
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

#[Title('Security Guards')]
class SecurityGuardsPage extends Component
{
    use WithPagination;

    public ?string $search = null;

    public ?string $from = null;

    public ?string $to = null;

    public ?string $deployment = null;

    public ?string $fileStatus = null;

    public bool $showFormerModal = false;

    public ?int $formerGuardId = null;

    public ?string $formerGuardName = null;

    public ?string $leftDate = null;

    public ?string $leftReason = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    public function boot(TenantContext $tenant, PermissionService $permissions): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'from', 'to', 'deployment', 'fileStatus'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'from', 'to', 'deployment', 'fileStatus']);
        $this->resetPage();
    }

    public function markFormer(int $id): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'edit_guards'), Response::HTTP_FORBIDDEN);

        $guard = SecurityGuard::query()
            ->where('businessId', $this->tenant->businessId())
            ->where('status', true)
            ->findOrFail($id);

        $this->formerGuardId = $guard->getKey();
        $this->formerGuardName = trim($guard->fname.' '.$guard->lname);
        $this->leftDate = now()->toDateString();
        $this->leftReason = null;
        $this->showFormerModal = true;
    }

    public function confirmFormer(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'edit_guards'), Response::HTTP_FORBIDDEN);

        $validated = $this->validate([
            'formerGuardId' => ['required', 'integer'],
            'leftDate' => ['required', 'date'],
            'leftReason' => ['required', 'string', 'max:255'],
        ]);

        $guard = SecurityGuard::query()
            ->where('businessId', $this->tenant->businessId())
            ->where('status', true)
            ->findOrFail($validated['formerGuardId']);

        DB::transaction(function () use ($guard, $validated): void {
            $guard->forceFill([
                'status' => false,
                'assigned' => false,
                'left_date' => $validated['leftDate'],
                'left_reason' => $validated['leftReason'],
            ])->save();

            ClientGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('guardId', $guard->getKey())
                ->where('status', true)
                ->update([
                    'status' => false,
                    'to' => $validated['leftDate'],
                ]);
        });

        $this->showFormerModal = false;
        $this->reset(['formerGuardId', 'formerGuardName', 'leftDate', 'leftReason']);

        Flux::toast(variant: 'success', text: __('Guard moved to former guards.'));
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'view_guards'), Response::HTTP_FORBIDDEN);

        $baseQuery = SecurityGuard::query()
            ->where('businessId', $this->tenant->businessId())
            ->where('status', true);

        return view('livewire.operations.security-guards-page', [
            'guards' => (clone $baseQuery)
                ->withCount('activeClients')
                ->when($this->from, fn ($query) => $query->whereDate('join_date', '>=', $this->from))
                ->when($this->to, fn ($query) => $query->whereDate('join_date', '<=', $this->to))
                ->when($this->search, function ($query): void {
                    $search = '%'.trim((string) $this->search).'%';

                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where('fname', 'like', $search)
                            ->orWhere('lname', 'like', $search)
                            ->orWhere('code', 'like', $search)
                            ->orWhere('contact1', 'like', $search)
                            ->orWhere('contact2', 'like', $search)
                            ->orWhere('id_no', 'like', $search);
                    });
                })
                ->when($this->deployment, function ($query): void {
                    match ($this->deployment) {
                        'deployed' => $query->whereHas('activeClients'),
                        'undeployed' => $query->whereDoesntHave('activeClients'),
                        default => null,
                    };
                })
                ->when($this->fileStatus, function ($query): void {
                    match ($this->fileStatus) {
                        'verified' => $query->where('doc_verified', true),
                        'incomplete' => $query->where('doc_verified', false),
                        default => null,
                    };
                })
                ->orderBy('fname')
                ->paginate(15),
            'activeCount' => (clone $baseQuery)->count(),
            'deployedCount' => (clone $baseQuery)->whereHas('activeClients')->count(),
            'undeployedCount' => (clone $baseQuery)->whereDoesntHave('activeClients')->count(),
            'incompleteFilesCount' => (clone $baseQuery)->where('doc_verified', false)->count(),
            'canEdit' => $this->permissions->can($this->tenant->user(), 'edit_guards'),
        ]);
    }
}
