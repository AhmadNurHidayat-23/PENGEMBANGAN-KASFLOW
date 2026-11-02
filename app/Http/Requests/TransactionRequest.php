<?php

namespace App\Http\Requests;

use App\Models\CashTransaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['income', 'expense'])],
            'category' => ['required', Rule::in(CashTransaction::CATEGORIES)],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'transaction_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:1900-01-01', 'before_or_equal:9999-12-31'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'in' => ':attribute yang dipilih tidak valid.',
            'string' => ':attribute harus berupa teks.',
            'description.max' => 'Keterangan maksimal 255 karakter.',
            'amount.integer' => 'Nominal harus berupa bilangan bulat dalam Rupiah.',
            'amount.min' => 'Nominal harus lebih dari nol.',
            'amount.max' => 'Nominal maksimal Rp 999.999.999.999.',
            'transaction_date.date_format' => 'Tanggal transaksi harus berupa tanggal yang valid.',
            'transaction_date.after_or_equal' => 'Tanggal transaksi paling awal 1 Januari 1900.',
            'transaction_date.before_or_equal' => 'Tanggal transaksi paling akhir 31 Desember 9999.',
        ];
    }

    public function attributes(): array
    {
        return ['type' => 'Jenis transaksi', 'category' => 'Kategori', 'description' => 'Keterangan',
            'amount' => 'Nominal', 'transaction_date' => 'Tanggal transaksi'];
    }
}
