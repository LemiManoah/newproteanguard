<?php

namespace App\Livewire\Operations;

use App\Models\ClientCategory;
use App\Services\AuditService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Client Categories')]
class ClientCategoriesPage extends Component
{
    public bool $showFormModal = false;

    public ?int $editingId = null;

    public string $name = '';

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    protected AuditService $audit;

    public function boot(TenantContext $tenant, PermissionService $permissions, AuditService $audit): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
        $this->audit = $audit;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function create(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'mange_client_categories'), Response::HTTP_FORBIDDEN);

        $this->reset('editingId', 'name');
        $this->showFormModal = true;
    }

    public function edit(int $categoryId): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'mange_client_categories'), Response::HTTP_FORBIDDEN);

        $category = ClientCategory::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($categoryId);

        $this->editingId = $category->getKey();
        $this->name = (string) $category->name;
        $this->showFormModal = true;
    }

    public function save(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'mange_client_categories'), Response::HTTP_FORBIDDEN);

        $validated = $this->validate();

        $category = $this->editingId
            ? ClientCategory::query()
                ->where('businessId', $this->tenant->businessId())
                ->findOrFail($this->editingId)
            : new ClientCategory;

        $category->forceFill([
            'name' => $validated['name'],
            'status' => true,
            'userId' => $this->tenant->user()->getKey(),
            'businessId' => $this->tenant->businessId(),
        ]);
        $category->save();

        $this->audit->record("Saved client category {$category->name}", $this->tenant->user());

        $this->showFormModal = false;
        $this->reset('editingId', 'name');
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'mange_client_categories'), Response::HTTP_FORBIDDEN);

        return view('livewire.operations.client-categories-page', [
            'categories' => ClientCategory::query()
                ->withCount('clients')
                ->where('businessId', $this->tenant->businessId())
                ->orderBy('name')
                ->get(),
        ]);
    }
}
