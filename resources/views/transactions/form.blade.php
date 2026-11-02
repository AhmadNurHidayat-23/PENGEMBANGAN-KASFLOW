@extends('layouts.app')
@section('title', $transaction->exists ? 'Edit Transaksi' : 'Tambah Transaksi')
@section('content')
    <a class="back-link" href="{{ route('transactions.index') }}">← Kembali ke transaksi</a>
    <div class="page-heading"><div><p class="eyebrow">BUKU KAS / {{ $transaction->exists ? 'EDIT' : 'CATAT BARU' }}</p><h1>{{ $transaction->exists ? 'Edit Transaksi' : 'Tambah Transaksi' }}</h1><p class="subtitle">Catat dengan teliti. Setiap Rupiah berarti.</p></div></div>
    <div class="form-layout">
        <form class="transaction-form" method="POST" action="{{ $transaction->exists ? route('transactions.update', $transaction) : route('transactions.store') }}">
            @csrf
            @if($transaction->exists) @method('PUT') @endif
            <div class="form-grid">
                <div class="field"><label for="type">Jenis transaksi</label><select id="type" name="type" required @error('type') aria-invalid="true" @enderror><option value="">Pilih jenis transaksi</option><option value="income" @selected(old('type', $transaction->type) === 'income')>Pemasukan</option><option value="expense" @selected(old('type', $transaction->type) === 'expense')>Pengeluaran</option></select>@error('type')<p class="field-error">{{ $message }}</p>@enderror</div>
                <div class="field"><label for="transaction_date">Tanggal transaksi</label><input type="date" id="transaction_date" name="transaction_date" min="1900-01-01" max="9999-12-31" value="{{ old('transaction_date', $transaction->transaction_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required @error('transaction_date') aria-invalid="true" @enderror>@error('transaction_date')<p class="field-error">{{ $message }}</p>@enderror</div>
                <div class="field"><label for="category">Kategori</label><select id="category" name="category" required @error('category') aria-invalid="true" @enderror><option value="">Pilih kategori</option>@foreach(\App\Models\CashTransaction::CATEGORIES as $category)<option @selected(old('category', $transaction->category) === $category)>{{ $category }}</option>@endforeach</select>@error('category')<p class="field-error">{{ $message }}</p>@enderror</div>
                <div class="field"><label for="amount">Nominal (Rp)</label><input type="number" id="amount" name="amount" min="1" max="999999999999" step="1" inputmode="numeric" placeholder="Contoh: 150000" value="{{ old('amount', $transaction->amount) }}" required aria-describedby="amount-help" @error('amount') aria-invalid="true" @enderror><p id="amount-help" class="field-help">Rupiah penuh, tanpa titik atau koma.</p>@error('amount')<p class="field-error">{{ $message }}</p>@enderror</div>
                <div class="field full"><label for="description">Keterangan</label><textarea id="description" name="description" rows="3" maxlength="255" placeholder="Contoh: Pembelian alat tulis kantor" required @error('description') aria-invalid="true" @enderror>{{ old('description', $transaction->description) }}</textarea><p class="field-help">Maksimal 255 karakter. Semua kolom wajib diisi.</p>@error('description')<p class="field-error">{{ $message }}</p>@enderror</div>
            </div>
            <div class="form-actions"><a class="button secondary" href="{{ route('transactions.index') }}">Batal</a><button class="button" type="submit">{{ $transaction->exists ? 'Simpan Perubahan' : 'Simpan Transaksi' }}</button></div>
        </form>
        <aside class="form-note"><p class="eyebrow">PENCATATAN KAS</p><h2>Rapi sejak awal.</h2><p>Pilih jenis transaksi yang sesuai. Pemasukan menambah saldo, pengeluaran menguranginya.</p><p>Anda dapat mengubah atau menghapus catatan kapan saja dari halaman Transaksi.</p></aside>
    </div>
@endsection
