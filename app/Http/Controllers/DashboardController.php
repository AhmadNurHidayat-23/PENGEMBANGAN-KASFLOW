<?php

namespace App\Http\Controllers;

use App\Services\CashFlowSummary;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private CashFlowSummary $summary) {}

    public function __invoke(): View
    {
        $totals = $this->summary->totals();

        return view('dashboard', [
            'income'       => $totals['income'],
            'expense'      => $totals['expense'],
            'balance'      => $totals['balance'],
            'transactions' => $this->summary->recent(),
        ]);
    }
}
