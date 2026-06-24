<?php

namespace App\Livewire\Operations;

use App\Models\ExpenseCategory;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Expense Categories')]
class ExpenseCategoriesPage extends Component
{
    use WithPagination;

    public ?string $search = null;

    public bool $showFormModal = false;

    public ?int $editingId = null;

    public ?string $name = null;

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

    public function create(): void
    {
        $this->authorizeRecord();
        $this->reset('editingId', 'name');
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeEdit();

        $category = ExpenseCategory::query()->where('businessId', $this->tenant->businessId())->findOrFail($id);
        $this->editingId = $category->getKey();
        $this->name = $category->name;
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->editingId ? $this->authorizeEdit() : $this->authorizeRecord();

        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('expense_categories', 'name')
                    ->where('businessId', $this->tenant->businessId())
                    ->ignore($this->editingId),
            ],
        ]);

        $category = $this->editingId
            ? ExpenseCategory::query()->where('businessId', $this->tenant->businessId())->findOrFail($this->editingId)
            : new ExpenseCategory;

        $category->forceFill([
            'name' => $validated['name'],
            'status' => true,
            'businessId' => $this->tenant->businessId(),
            'userId' => $this->tenant->user()->getKey(),
        ])->save();

        $this->showFormModal = false;
        Flux::toast(variant: 'success', text: __('Expense category saved.'));
    }

    public function delete(int $id): void
    {
        $this->authorizeDelete();

        ExpenseCategory::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($id)
            ->forceFill(['status' => false])
            ->save();

        Flux::toast(variant: 'success', text: __('Expense category deleted.'));
    }

    public function render(): View
    {
        $this->authorizeView();

        return view('livewire.operations.expense-categories-page', [
            'categories' => ExpenseCategory::query()
                ->withCount('expenses')
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->when($this->search, fn ($query) => $query->where('name', 'like', '%'.trim((string) $this->search).'%'))
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    protected function authorizeView(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'view_expenses'), Response::HTTP_FORBIDDEN);
    }

    protected function authorizeRecord(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'record_expenses'), Response::HTTP_FORBIDDEN);
    }

    protected function authorizeEdit(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'edit_expenses'), Response::HTTP_FORBIDDEN);
    }

    protected function authorizeDelete(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'delete_expenses'), Response::HTTP_FORBIDDEN);
    }
}
