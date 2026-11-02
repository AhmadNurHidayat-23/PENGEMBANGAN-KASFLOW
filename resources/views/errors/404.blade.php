@extends('layouts.app')
@section('title', 'Halaman Tidak Ditemukan')
@section('content')
<div class="empty-state"><p class="eyebrow">404</p><h1>Halaman tidak ditemukan</h1><p>Halaman atau transaksi yang Anda cari sudah tidak tersedia.</p><a class="button" href="{{ route('dashboard') }}">Kembali ke Dashboard</a></div>
@endsection
