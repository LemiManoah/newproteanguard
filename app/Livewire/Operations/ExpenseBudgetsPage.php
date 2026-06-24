<?php

namespace App\Livewire\Operations;

use App\Models\ExpenseBudget;
use App\Models\ExpenseCategory;
use App\Models\FinancialYear;
use App\Services\ExpenseService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Expense Budgets')]
class ExpenseBudgetsPage extends Component
{
    use WithPagination;

    public ?int $yearFilter = null;

    public bool $showFormModal = false;

    public ?int $editingId = null;

    public ?int $categoryId = null;

    public ?int $yearId = null;

    public ?string $amount = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    protected ExpenseService $expenses;

    public function boot(TenantContext $tenant, PermissionService $permissions, ExpenseService $expenses): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
        $this->expenses = $expenses;
    }

    public function updatedYearFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorizeRecord();
        $this->reset('editingId', 'categoryId', 'amount');
        $this->yearId = FinancialYear::query()
            ->where('businessId', $this->tenant->businessId())
            ->where('Active', true)
            ->value('id');
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeEdit();

        $budget = ExpenseBudget::query()->where('businessId', $this->tenant->businessId())->findOrFail($id);

        $this->editingId = $budget->getKey();
        $this->categoryId = $budget->categoryId;
        $this->yearId = $budget->yearId;
        $this->amount = (string) $budget->amount;
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->editingId ? $this->authorizeEdit() : $this->authorizeRecord();

        $validated = $this->validate([
            'categoryId' => ['required', 'integer'],
            'yearId' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        ExpenseCategory::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['categoryId']);
        FinancialYear::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['yearId']);

        $budget = $this->editingId
            ? ExpenseBudget::query()->where('businessId', $this->tenant->businessId())->findOrFail($this->editingId)
            : ExpenseBudget::query()->firstOrNew([
                'businessId' => $this->tenant->businessId(),
                'categoryId' => $validated['categoryId'],
                'yearId' => $validated['yearId'],
            ]);

        $budget->forceFill([
            'categoryId' => $validated['categoryId'],
            'yearId' => $validated['yearId'],
            'amount' => $validated['amount'],
            'status' => true,
            'businessId' => $this->tenant->businessId(),
            'userId' => $this->tenant->user()->getKey(),
        ])->save();

        $this->showFormModal = false;
        Flux::toast(variant: 'success', text: __('Expense budget saved.'));
    }

    public function delete(int $id): void
    {
        $this->authorizeDelete();

        ExpenseBudget::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($id)
            ->forceFill(['status' => false])
            ->save();

        Flux::toast(variant: 'success', text: __('Expense budget deleted.'));
    }

    public function render(): View
    {
        $this->authorizeView();

        $businessId = $this->tenant->businessId();

        return view('livewire.operations.expense-budgets-page', [
            'budgets' => $this->expenses->budgetsQuery($businessId)
                ->when($this->yearFilter, fn ($query) => $query->where('yearId', $this->yearFilter))
                ->orderByDesc('yearId')
                ->paginate(15),
            'categories' => ExpenseCategory::query()->where('businessId', $businessId)->where('status', true)->orderBy('name')->get(),
            'financialYears' => FinancialYear::query()->where('businessId', $businessId)->where('status', true)->orderByDesc('start')->get(),
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
