@extends('layouts.app')
@section('title', 'Transaksi')
@section('content')
    <div class="page-heading"><div><p class="eyebrow">02 / BUKU KAS</p><h1>Riwayat Transaksi</h1><p class="subtitle">Semua pemasukan dan pengeluaran, dalam satu catatan.</p></div><a class="button" href="{{ route('transactions.create') }}">+ Tambah Transaksi</a></div>
    <form class="filters" method="GET" action="{{ route('transactions.index') }}">
        <div class="search-field"><label for="q">Pencarian</label><input id="q" name="q" type="search" maxlength="100" placeholder="Cari keterangan atau kategori…" value="{{ request('q') }}"></div>
        <div><label for="type">Jenis transaksi</label><select id="type" name="type"><option value="">Semua jenis</option value="income" @selected(request('type') === 'income')>Pemasukan</option><option value="expense" @selected(request('type') === 'expense')>Pengeluaran</option></select></div>
        <div><label for="month">Bulan</label><input type="month" id="month" name="month" value="{{ request('month') }}"></div>
        <button class="button" type="submit">Terapkan</button><a class="text-link reset" href="{{ route('transactions.index') }}">Reset</a>
    </form>
    <section class="ledger"><div class="section-heading"><h2>Daftar Transaksi</h2><span class="muted">{{ $transactions->total() }} transaksi ditemukan</span></div><x-transaction-table :transactions="$transactions" :editable="true" /></section>
    {{ $transactions->links('components.pagination') }}
@endsection
