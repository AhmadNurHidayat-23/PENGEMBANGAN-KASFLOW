@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">01 / DASHBOARD</p><h1>Ringkasan Keuangan</h1><p class="subtitle">Kelola pemasukan dan pengeluaran kas kecil dengan lebih mudah.</p></div>
        <a class="button" href="{{ route('transactions.create') }}">+ Tambah Transaksi</a>
    </div>
    <section class="stats" aria-label="Statistik keuangan keseluruhan">
        <div class="stat"><p>Total Pemasukan <span class="income" aria-hidden="true">↗</span></p><strong><x-rupiah :amount="$income" /></strong><span>Seluruh pemasukan tercatat</span></div>
        <div class="stat"><p>Total Pengeluaran <span class="expense" aria-hidden="true">↘</span></p><strong><x-rupiah :amount="$expense" /></strong><span>Seluruh pengeluaran tercatat</span></div>
        <div class="stat balance"><p>Saldo Kas <span aria-hidden="true">=</span></p><strong @class(['expense' => $balance < 0])><x-rupiah :amount="$balance" /></strong><span>Pemasukan dikurangi pengeluaran</span></div>
    </section>
    <section class="ledger" aria-labelledby="latest-heading">
        <div class="section-heading"><div><h2 id="latest-heading">Transaksi Terbaru</h2><p>Lima catatan terakhir dari buku kas Anda.</p></div><a class="text-link" href="{{ route('transactions.index') }}">Lihat Semua Transaksi <span aria-hidden="true">↗</span></a></div>
        <x-transaction-table :transactions="$transactions" />
    </section>
    <div class="dashboard-note"><span>CATATAN</span><p>Saldo dihitung otomatis dari seluruh transaksi. Setiap perubahan catatan langsung diperhitungkan.</p></div>
@endsection
