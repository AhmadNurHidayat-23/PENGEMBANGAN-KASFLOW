<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totals = CashTransaction::query()->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS income, COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS expense")->first();

        return view('dashboard', [
            'income' => (int) $totals->income,
            'expense' => (int) $totals->expense,
            'balance' => (int) $totals->income - (int) $totals->expense,
            'transactions' => CashTransaction::orderByDesc('transaction_date')->orderByDesc('id')->limit(5)->get(),
        ]);
    }
}
