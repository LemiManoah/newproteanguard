<?php

namespace App\Livewire\Operations;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialYear;
use App\Models\PaymentMode;
use App\Services\ExpenseService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\Response;

#[Title('Expenses')]
class ExpensesPage extends Component
{
    use WithPagination;

    public ?string $search = null;

    public ?int $categoryFilter = null;

    public ?int $modeFilter = null;

    public ?int $yearFilter = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public bool $showFormModal = false;

    public ?int $editingId = null;

    public ?int $categoryId = null;

    public ?int $modeId = null;

    public ?int $yearId = null;

    public ?string $date = null;

    public ?string $amount = null;

    public ?string $description = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    protected ExpenseService $expenses;

    public function boot(TenantContext $tenant, PermissionService $permissions, ExpenseService $expenses): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
        $this->expenses = $expenses;
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'categoryFilter', 'modeFilter', 'yearFilter', 'startDate', 'endDate'], true)) {
            $this->resetPage();
        }
    }

    public function create(): void
    {
        $this->authorizeRecord();
        $this->reset('editingId', 'categoryId', 'modeId', 'amount', 'description');
        $this->date = now()->toDateString();
        $this->yearId = FinancialYear::query()
            ->where('businessId', $this->tenant->businessId())
            ->where('Active', true)
            ->value('id');
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeEdit();

        $expense = Expense::query()->where('businessId', $this->tenant->businessId())->findOrFail($id);

        $this->editingId = $expense->getKey();
        $this->categoryId = $expense->categoryId;
        $this->modeId = $expense->modeId;
        $this->yearId = $expense->yearId;
        $this->date = $expense->date?->toDateString();
        $this->amount = (string) $expense->amount;
        $this->description = $expense->description;
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->editingId ? $this->authorizeEdit() : $this->authorizeRecord();

        $validated = $this->validate([
            'categoryId' => ['required', 'integer'],
            'modeId' => ['required', 'integer'],
            'yearId' => ['required', 'integer'],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        ExpenseCategory::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['categoryId']);
        PaymentMode::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['modeId']);
        FinancialYear::query()->where('businessId', $this->tenant->businessId())->where('status', true)->findOrFail($validated['yearId']);

        $expense = $this->editingId
            ? Expense::query()->where('businessId', $this->tenant->businessId())->findOrFail($this->editingId)
            : new Expense;

        $expense->forceFill([
            'categoryId' => $validated['categoryId'],
            'modeId' => $validated['modeId'],
            'yearId' => $validated['yearId'],
            'date' => $validated['date'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'businessId' => $this->tenant->businessId(),
            'userId' => $this->tenant->user()->getKey(),
        ])->save();

        $this->showFormModal = false;
        Flux::toast(variant: 'success', text: __('Expense saved.'));
    }

    public function delete(int $id): void
    {
        $this->authorizeDelete();

        Expense::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($id)
            ->delete();

        Flux::toast(variant: 'success', text: __('Expense deleted.'));
    }

    public function render(): View
    {
        $this->authorizeView();

        $businessId = $this->tenant->businessId();

        return view('livewire.operations.expenses-page', [
            'expenses' => $this->expenses->expensesQuery($businessId)
                ->when($this->search, function ($query): void {
                    $search = '%'.trim((string) $this->search).'%';
                    $query->where(fn ($query) => $query
                        ->where('description', 'like', $search)
                        ->orWhereHas('category', fn ($query) => $query->where('name', 'like', $search))
                        ->orWhereHas('mode', fn ($query) => $query->where('name', 'like', $search)));
                })
                ->when($this->categoryFilter, fn ($query) => $query->where('categoryId', $this->categoryFilter))
                ->when($this->modeFilter, fn ($query) => $query->where('modeId', $this->modeFilter))
                ->when($this->yearFilter, fn ($query) => $query->where('yearId', $this->yearFilter))
                ->when($this->startDate, fn ($query) => $query->whereDate('date', '>=', $this->startDate))
                ->when($this->endDate, fn ($query) => $query->whereDate('date', '<=', $this->endDate))
                ->latest('date')
                ->paginate(15),
            'categories' => ExpenseCategory::query()->where('businessId', $businessId)->where('status', true)->orderBy('name')->get(),
            'paymentModes' => PaymentMode::query()->where('businessId', $businessId)->where('status', true)->orderBy('name')->get(),
            'financialYears' => FinancialYear::query()->where('businessId', $businessId)->where('status', true)->orderByDesc('start')->get(),
            'totalExpenses' => $this->expenses->totalExpenses($businessId),
            'monthExpenses' => $this->expenses->monthExpenses($businessId),
            'budgetTotal' => $this->expenses->activeBudgetTotal($businessId),
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
