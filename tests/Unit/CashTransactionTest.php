<?php

namespace Tests\Unit;

use App\Models\CashTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashTransactionTest extends TestCase
{
    use RefreshDatabase;

    private function make(array $attrs = []): CashTransaction
    {
        return CashTransaction::create(array_merge([
            'type'             => 'income',
            'category'         => 'Operasional',
            'description'      => 'Test',
            'amount'           => 100_000,
            'transaction_date' => '2026-11-01',
        ], $attrs));
    }

    public function test_amount_formatted_returns_rupiah_with_dot_thousands(): void
    {
        $t = $this->make(['amount' => 1_250_000]);

        $this->assertSame('Rp 1.250.000', $t->amountFormatted());
    }

    public function test_amount_formatted_handles_zero(): void
    {
        // Amount must be at least 1 per validation; we set directly to test the helper.
        $t = $this->make();
        $t->amount = 0;

        $this->assertSame('Rp 0', $t->amountFormatted());
    }

    public function test_amount_formatted_handles_large_max_value(): void
    {
        $t = $this->make(['amount' => 999_999_999_999]);

        $this->assertSame('Rp 999.999.999.999', $t->amountFormatted());
    }

    public function test_scope_for_month_filters_by_year_and_month(): void
    {
        $this->make(['transaction_date' => '2026-11-15']);
        $this->make(['transaction_date' => '2026-11-01']);
        $this->make(['transaction_date' => '2026-10-31']); // different month

        $this->assertCount(2, CashTransaction::forMonth('2026-11')->get());
        $this->assertCount(1, CashTransaction::forMonth('2026-10')->get());
        $this->assertCount(0, CashTransaction::forMonth('2025-11')->get());
    }

    public function test_scope_for_month_returns_empty_query_for_malformed_input(): void
    {
        $this->make(['transaction_date' => '2026-11-01']);

        $this->assertCount(0, CashTransaction::forMonth('')->get());
        $this->assertCount(0, CashTransaction::forMonth('11-2026')->get());
        $this->assertCount(0, CashTransaction::forMonth('2026')->get());
    }

    public function test_scope_for_month_can_be_chained_with_other_scopes(): void
    {
        $this->make(['type' => 'income',  'amount' => 100_000, 'transaction_date' => '2026-11-01']);
        $this->make(['type' => 'expense', 'amount' => 50_000,  'transaction_date' => '2026-11-02']);
        $this->make(['type' => 'income',  'amount' => 200_000, 'transaction_date' => '2026-10-31']);

        $novemberIncome = CashTransaction::forMonth('2026-11')->income()->get();

        $this->assertCount(1, $novemberIncome);
        $this->assertSame(100_000, $novemberIncome->first()->amount);
    }
}
