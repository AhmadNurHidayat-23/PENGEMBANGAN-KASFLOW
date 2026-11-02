@extends('layouts.app')
@section('title', 'Sesi Berakhir')
@section('content')
<div class="empty-state"><p class="eyebrow">419</p><h1>Sesi telah berakhir</h1><p>Muat ulang halaman untuk melanjutkan pencatatan transaksi.</p><a class="button" href="{{ route('transactions.index') }}">Kembali ke Transaksi</a></div>
@endsection
