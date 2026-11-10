<?php

namespace App\Services;

use App\Models\CashTransaction;
use Illuminate\Support\Collection;

/**
 * Aggregates income, expense, and balance totals from cash transactions.
 *
 * Encapsulates the summary logic previously inlined in DashboardController,
 * enabling isolated unit testing without an HTTP layer.
 */
class CashFlowSummary
{
    /**
     * Calculate total income, expense, and balance for all transactions.
     *
     * Uses a single aggregating query to avoid N+1 issues.
     *
     * @return array{income: int, expense: int, balance: int}
     */
    public function totals(): array
    {
        $row = CashTransaction::query()
            ->selectRaw(
                "COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS income,
                 COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS expense"
            )
            ->first();

        $income  = (int) $row->income;
        $expense = (int) $row->expense;

        return [
            'income'  => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
        ];
    }

    /**
     * Return the five most recent transactions ordered by date then insertion order.
     */
    public function recent(int $limit = 5): Collection
    {
        return CashTransaction::orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Return per-category spending breakdown for a given month (Y-m format).
     *
     * Only expense transactions are included. Returns an empty collection when
     * the month string is malformed or no data exists.
     *
     * @return Collection<int, object{category: string, total: int}>
     */
    public function expensesByCategory(string $month): Collection
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            return collect();
        }

        [$year, $monthNumber] = explode('-', $month);

        return CashTransaction::query()
            ->selectRaw('category, COALESCE(SUM(amount), 0) AS total')
            ->where('type', 'expense')
            ->whereYear('transaction_date', (int) $year)
            ->whereMonth('transaction_date', (int) $monthNumber)
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
    }
}
