<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="description" content="PENGEMBANGAN KASFLOW — Pengelolaan Kas Kecil Berbasis Web">
    <title>@yield('title') · PENGEMBANGAN KASFLOW — Pengelolaan Kas Kecil Berbasis Web</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#konten">Lewati ke konten</a>
    <header class="site-header">
        <div class="header-inner">
            <a href="{{ route('dashboard') }}" class="brand" aria-label="KasFlow — Beranda">kasflow<span>.</span></a>
            <nav aria-label="Navigasi utama">
                <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')]) @if(request()->routeIs('dashboard')) aria-current="page" @endif>Dashboard</a>
                <a href="{{ route('transactions.index') }}" @class(['active' => request()->routeIs('transactions.*')]) @if(request()->routeIs('transactions.*')) aria-current="page" @endif>Transaksi</a>
            </nav>
            <span class="header-note">BUKU KAS DIGITAL</span>
        </div>
    </header>
    <main id="konten" class="container">
        @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
        @if($errors->any())
            <div class="error-summary" role="alert"><strong>Periksa kembali isian Anda.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        @yield('content')
    </main>
    <footer class="container site-footer"><span>kasflow. <span class="footer-description">Catatan rapi, keuangan terkendali.</span></span><span>Demo lokal · Pengelolaan kas kecil</span></footer>
</body>
</html>
