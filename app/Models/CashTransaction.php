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
}
