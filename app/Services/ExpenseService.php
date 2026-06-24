<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\ExpenseBudget;
use Illuminate\Database\Eloquent\Builder;

class ExpenseService
{
    public function expensesQuery(int $businessId): Builder
    {
        return Expense::query()
            ->with(['category', 'mode', 'financialYear'])
            ->where('businessId', $businessId);
    }

    public function budgetsQuery(int $businessId): Builder
    {
        return ExpenseBudget::query()
            ->with(['category', 'financialYear'])
            ->where('businessId', $businessId)
            ->where('status', true);
    }

    public function totalExpenses(int $businessId): float
    {
        return (float) Expense::query()
            ->where('businessId', $businessId)
            ->sum('amount');
    }

    public function monthExpenses(int $businessId): float
    {
        return (float) Expense::query()
            ->where('businessId', $businessId)
            ->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->sum('amount');
    }

    public function activeBudgetTotal(int $businessId): float
    {
        return (float) ExpenseBudget::query()
            ->where('businessId', $businessId)
            ->where('status', true)
            ->sum('amount');
    }
}
