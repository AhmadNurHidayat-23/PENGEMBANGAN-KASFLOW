<?php

namespace Tests\Feature;

use App\Models\CashTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashFlowTest extends TestCase
{
    use RefreshDatabase;

    private function data(array $overrides = []): array
    {
        return array_merge(['type' => 'income', 'category' => 'Operasional',
            'description' => 'Dana awal', 'amount' => 1000000, 'transaction_date' => '2026-11-03'], $overrides);
    }

    public function test_empty_dashboard_and_forms_are_accessible(): void
    {
        $this->get('/')->assertOk()->assertSee('Belum ada transaksi')->assertSee('Rp 0');
        $this->get('/transactions')->assertOk()->assertSee('Riwayat Transaksi');
        $this->get('/transactions/create')->assertOk()->assertSee('Simpan Transaksi');
    }

    public function test_full_lifecycle_recalculates_financial_totals(): void
    {
        $this->post('/transactions', $this->data())->assertRedirect('/transactions')->assertSessionHas('status');
        $this->post('/transactions', $this->data(['type' => 'expense', 'amount' => 250000]))->assertRedirect('/transactions');
        $this->get('/')->assertViewHas('income', 1000000)->assertViewHas('expense', 250000)->assertViewHas('balance', 750000);
        $expense = CashTransaction::where('type', 'expense')->firstOrFail();
        $this->get('/transactions/'.$expense->id.'/edit')->assertOk()->assertSee('Simpan Perubahan');
        $this->put('/transactions/'.$expense->id, $this->data(['type' => 'expense', 'amount' => 1200000]))->assertRedirect('/transactions');
        $this->get('/')->assertViewHas('balance', -200000)->assertSee('Rp -200.000');
        $this->put('/transactions/'.$expense->id, $this->data(['type' => 'income', 'amount' => 500000]))->assertRedirect('/transactions');
        $this->get('/')->assertViewHas('income', 1500000)->assertViewHas('expense', 0)->assertViewHas('balance', 1500000);
        $this->delete('/transactions/'.$expense->id)->assertRedirect('/transactions')->assertSessionHas('status', 'Transaksi berhasil dihapus.');
        $this->assertDatabaseCount('cash_transactions', 1);
        $this->get('/')->assertViewHas('balance', 1000000);
    }

    public function test_invalid_amounts_and_fields_are_rejected_in_indonesian(): void
    {
        foreach ([0, -1, '1.5', 'abc', 1000000000000] as $amount) {
            $this->post('/transactions', $this->data(['amount' => $amount]))->assertSessionHasErrors('amount');
        }
        $this->post('/transactions', $this->data(['amount' => 0]))
            ->assertSessionHasErrors(['amount' => 'Nominal harus lebih dari nol.']);
        $this->post('/transactions', [])->assertSessionHasErrors(['type', 'category', 'description', 'amount', 'transaction_date']);
        $this->post('/transactions', $this->data(['type' => 'invalid', 'category' => 'invalid',
            'description' => str_repeat('x', 256), 'transaction_date' => '2026-02-30']))
            ->assertSessionHasErrors(['type', 'category', 'description', 'transaction_date']);
        $this->assertDatabaseCount('cash_transactions', 0);
    }

    public function test_invalid_update_keeps_original_values(): void
    {
        $transaction = CashTransaction::create($this->data());
        $this->put('/transactions/'.$transaction->id, $this->data(['amount' => -1]))->assertSessionHasErrors('amount');
        $this->assertSame(1000000, $transaction->fresh()->amount);
    }

    public function test_search_filters_and_pagination_preserve_query(): void
    {
        CashTransaction::create($this->data(['description' => 'Unik biaya perjalanan', 'category' => 'Transportasi', 'type' => 'expense']));
        CashTransaction::create($this->data(['description' => 'Dana bulan lalu', 'transaction_date' => '2026-10-31']));
        CashTransaction::create($this->data(['description' => 'Dana lain', 'transaction_date' => '2025-11-01']));
        $this->get('/transactions?q=perjalanan&type=expense&month=2026-11')->assertOk()
            ->assertSee('Unik biaya perjalanan')->assertDontSee('Dana bulan lalu')->assertDontSee('Dana lain');
        $this->get('/transactions?q=Transportasi')->assertViewHas('transactions', fn ($items) => $items->total() === 1);
        $this->get('/transactions?type=income&month=2026-10')->assertViewHas('transactions', fn ($items) => $items->total() === 1);
        $this->get('/transactions?q=TidakAda')->assertSee('Tidak ada transaksi yang sesuai');
        for ($i = 0; $i < 12; $i++) {
            CashTransaction::create($this->data(['description' => 'Catatan '.$i]));
        }
        $this->get('/transactions?type=income&q=Catatan')->assertSee('Berikutnya')->assertSee('type=income')->assertSee('q=Catatan');
        $this->get('/transactions?type=income&q=Catatan&page=2')->assertViewHas('transactions', fn ($items) => $items->count() === 2);
        $this->get('/')->assertViewHas('transactions', fn ($items) => $items->count() === 5 && $items->first()->description === 'Catatan 11');
    }

    public function test_invalid_filters_are_rejected(): void
    {
        $this->from('/transactions')->get('/transactions?type=other&month=2026-99')->assertSessionHasErrors(['type', 'month']);
    }

    public function test_integer_money_large_values_and_escaped_description(): void
    {
        $this->post('/transactions', $this->data(['amount' => 999999999999, 'description' => '<script>alert(1)</script>']))->assertRedirect();
        $this->get('/')->assertViewHas('income', 999999999999)->assertSee('Rp 999.999.999.999')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_missing_transactions_return_indonesian_not_found_page(): void
    {
        $this->get('/transactions/999/edit')->assertNotFound()->assertSee('Halaman tidak ditemukan');
        $this->put('/transactions/999', $this->data())->assertNotFound();
        $this->delete('/transactions/999')->assertNotFound();
    }
}
