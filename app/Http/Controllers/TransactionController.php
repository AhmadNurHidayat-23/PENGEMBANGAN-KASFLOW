<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\CashTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::in(['income', 'expense'])],
            'month' => ['nullable', 'date_format:Y-m'],
        ], [
            'q.string' => 'Pencarian harus berupa teks.', 'q.max' => 'Pencarian maksimal 100 karakter.',
            'type.in' => 'Filter jenis transaksi tidak valid.', 'month.date_format' => 'Filter bulan tidak valid.',
        ]);
        $query = CashTransaction::query();
        if ($search = $filters['q'] ?? null) {
            $query->where(function ($query) use ($search) {
                $query->where('description', 'like', '%'.$search.'%')->orWhere('category', 'like', '%'.$search.'%');
            });
        }
        if ($type = $filters['type'] ?? null) {
            $query->where('type', $type);
        }
        if ($month = $filters['month'] ?? null) {
            [$year, $monthNumber] = explode('-', $month);
            $query->whereYear('transaction_date', $year)->whereMonth('transaction_date', $monthNumber);
        }

        return view('transactions.index', ['transactions' => $query->orderByDesc('transaction_date')->orderByDesc('id')->paginate(10)->withQueryString()]);
    }

    public function create(): View
    {
        return view('transactions.form', ['transaction' => new CashTransaction]);
    }

    public function store(TransactionRequest $request): RedirectResponse
    {
        CashTransaction::create($request->validated());

        return to_route('transactions.index')->with('status', 'Transaksi berhasil disimpan.');
    }

    public function edit(CashTransaction $transaction): View
    {
        return view('transactions.form', compact('transaction'));
    }

    public function update(TransactionRequest $request, CashTransaction $transaction): RedirectResponse
    {
        $transaction->update($request->validated());

        return to_route('transactions.index')->with('status', 'Transaksi berhasil diubah.');
    }

    public function destroy(CashTransaction $transaction): RedirectResponse
    {
        $transaction->delete();

        return to_route('transactions.index')->with('status', 'Transaksi berhasil dihapus.');
    }
}
