<?php

namespace Tests\Feature;

use App\Models\CashTransaction;
use App\Services\CashFlowSummary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashFlowSummaryTest extends TestCase
{
    use RefreshDatabase;

    private CashFlowSummary $summary;

    protected function setUp(): void
    {
        parent::setUp();
        $this->summary = app(CashFlowSummary::class);
    }

    private function income(int $amount, string $date = '2026-11-01', string $category = 'Operasional'): CashTransaction
    {
        return CashTransaction::create([
            'type'             => 'income',
            'category'         => $category,
            'description'      => 'Pemasukan',
            'amount'           => $amount,
            'transaction_date' => $date,
        ]);
    }

    private function expense(int $amount, string $date = '2026-11-01', string $category = 'Operasional'): CashTransaction
    {
        return CashTransaction::create([
            'type'             => 'expense',
            'category'         => $category,
            'description'      => 'Pengeluaran',
            'amount'           => $amount,
            'transaction_date' => $date,
        ]);
    }

    public function test_totals_return_zero_when_no_transactions_exist(): void
    {
        $result = $this->summary->totals();

        $this->assertSame(['income' => 0, 'expense' => 0, 'balance' => 0], $result);
    }

    public function test_totals_accumulate_income_and_expense_correctly(): void
    {
        $this->income(1_000_000);
        $this->income(500_000);
        $this->expense(300_000);

        $result = $this->summary->totals();

        $this->assertSame(1_500_000, $result['income']);
        $this->assertSame(300_000,   $result['expense']);
        $this->assertSame(1_200_000, $result['balance']);
    }

    public function test_balance_is_negative_when_expenses_exceed_income(): void
    {
        $this->income(200_000);
        $this->expense(500_000);

        $result = $this->summary->totals();

        $this->assertSame(-300_000, $result['balance']);
    }

    public function test_totals_return_integers_not_strings_or_floats(): void
    {
        $this->income(999_999_999_999);
        $this->expense(1);

        $result = $this->summary->totals();

        $this->assertIsInt($result['income']);
        $this->assertIsInt($result['expense']);
        $this->assertIsInt($result['balance']);
    }

    public function test_recent_returns_at_most_five_in_newest_first_order(): void
    {
        for ($i = 1; $i <= 7; $i++) {
            $this->income(100_000 * $i, '2026-11-0' . min($i, 9));
        }

        $recent = $this->summary->recent();

        $this->assertCount(5, $recent);
        // Newest date first: day 7 → day 3
        $this->assertSame('2026-11-07', $recent->first()->transaction_date->toDateString());
    }

    public function test_recent_respects_custom_limit(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->income(1000);
        }

        $this->assertCount(3, $this->summary->recent(3));
        $this->assertCount(10, $this->summary->recent(10));
    }

    public function test_expenses_by_category_sums_per_category_for_given_month(): void
    {
        $this->expense(400_000, '2026-11-05', 'Transportasi');
        $this->expense(200_000, '2026-11-10', 'Transportasi');
        $this->expense(150_000, '2026-11-15', 'Konsumsi');
        // Different month – should be excluded
        $this->expense(999_000, '2026-10-31', 'Transportasi');
        // Income – should be excluded
        $this->income(500_000, '2026-11-01', 'Operasional');

        $result = $this->summary->expensesByCategory('2026-11');

        $this->assertCount(2, $result);
        $this->assertSame('Transportasi', $result[0]->category);
        $this->assertSame(600_000, (int) $result[0]->total);
        $this->assertSame('Konsumsi', $result[1]->category);
        $this->assertSame(150_000, (int) $result[1]->total);
    }

    public function test_expenses_by_category_returns_empty_collection_for_malformed_month(): void
    {
        $this->expense(100_000);

        $this->assertCount(0, $this->summary->expensesByCategory('11-2026'));
        $this->assertCount(0, $this->summary->expensesByCategory(''));
        $this->assertCount(0, $this->summary->expensesByCategory('2026'));
        $this->assertCount(0, $this->summary->expensesByCategory('2026-13'));
    }

    public function test_expenses_by_category_returns_empty_when_no_expenses_in_month(): void
    {
        $this->income(500_000, '2026-11-01');

        $this->assertCount(0, $this->summary->expensesByCategory('2026-11'));
    }

    public function test_dashboard_uses_summary_service_data(): void
    {
        $this->income(2_000_000);
        $this->expense(750_000);

        $this->get('/')->assertOk()
            ->assertViewHas('income', 2_000_000)
            ->assertViewHas('expense', 750_000)
            ->assertViewHas('balance', 1_250_000);
    }
}
