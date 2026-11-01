<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') — MBG Lapor</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <a href="{{ url('/') }}" class="brand">🍱 MBG Lapor</a>
        @auth
            <nav class="nav">
                <a href="{{ url('/') }}">Beranda</a>
                @if(auth()->user()->role === 'pelapor')
                    <a href="{{ url('/reports') }}">Laporan Saya</a>
                    <a href="{{ url('/reports/create') }}">Buat Laporan</a>
                @else
                    <a href="{{ url('/review') }}">Tinjau Laporan</a>
                    <a href="{{ url('/rekap') }}">Rekap</a>
                @endif
                @if(auth()->user()->role === 'koordinator')
                    <a href="{{ url('/master/schools') }}">Sekolah</a>
                    <a href="{{ url('/master/distributions') }}">Distribusi</a>
                @endif
            </nav>
            <div class="user-box">
                <span>{{ auth()->user()->name }}</span>
                <span class="role-chip">{{ auth()->user()->role }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-light btn-sm">Keluar</button>
                </form>
            </div>
        @else
            <nav class="nav"></nav>
            <div class="user-box">
                <a class="btn btn-light btn-sm" href="{{ route('login') }}">Masuk</a>
                <a class="btn btn-light btn-sm" href="{{ route('register') }}">Daftar</a>
            </div>
        @endauth
    </div>
</header>

<main class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <b>Periksa kembali:</b>
            <ul>
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<footer class="footer">Mini Project Proyek 3 — D3 Teknik Informatika · Data pada aplikasi ini hanya data dummy</footer>

@stack('scripts')
</body>
</html>