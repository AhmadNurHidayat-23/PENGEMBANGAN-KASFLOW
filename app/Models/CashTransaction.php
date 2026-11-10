<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    public const CATEGORIES = ['Operasional', 'Transportasi', 'Konsumsi', 'Perlengkapan', 'Lainnya'];

    protected $fillable = ['type', 'category', 'description', 'amount', 'transaction_date'];

    protected function casts(): array
    {
        return ['amount' => 'integer', 'transaction_date' => 'date'];
    }

    public function typeLabel(): string
    {
        return $this->type === 'income' ? 'Pemasukan' : 'Pengeluaran';
    }

    public function isIncome(): bool
    {
        return $this->type === 'income';
    }

    public function isExpense(): bool
    {
        return $this->type === 'expense';
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to transactions within a given year-month string (Y-m format).
     *
     * Silently returns an empty query when the format is invalid so callers
     * do not need to guard before applying the scope.
     */
    public function scopeForMonth($query, string $month)
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            return $query->whereRaw('0 = 1');
        }

        [$year, $monthNumber] = explode('-', $month);

        return $query
            ->whereYear('transaction_date', (int) $year)
            ->whereMonth('transaction_date', (int) $monthNumber);
    }

    /**
     * Return the amount formatted as a human-readable Rupiah string.
     *
     * Negative amounts receive a leading minus sign; e.g. -Rp 200.000.
     */
    public function amountFormatted(): string
    {
        $abs = abs($this->amount);
        $formatted = 'Rp ' . number_format($abs, 0, ',', '.');

        return $this->amount < 0 ? '-' . $formatted : $formatted;
    }
}
