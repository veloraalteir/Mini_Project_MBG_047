@extends('layouts.app')
@section('title', 'Beranda')
@section('content')
<div class="hero">
    <h1>Halo, {{ auth()->user()->name }} 👋</h1>
    <p>Anda masuk sebagai <b>{{ ucfirst(auth()->user()->role) }}</b>. Aplikasi ini membantu pelaporan dan pemantauan keamanan makanan program MBG secara terstruktur.</p>
</div>

<div class="grid">
    @if(auth()->user()->role === 'pelapor')
        <a class="card menu-card" href="{{ url('/reports/create') }}">
            <h3>📝 Buat Laporan</h3>
            <p class="muted">Laporkan kejadian setelah konsumsi makanan, lengkap dengan bukti pendukung.</p>
        </a>
        <a class="card menu-card" href="{{ url('/reports') }}">
            <h3>📋 Laporan Saya</h3>
            <p class="muted">Pantau perkembangan dan perbaiki laporan bila diminta.</p>
        </a>
    @else
        <a class="card menu-card" href="{{ url('/review') }}">
            <h3>🔎 Tinjau Laporan</h3>
            <p class="muted">Lihat laporan masuk, keterkaitannya, dan tindak lanjutnya.</p>
        </a>
        <a class="card menu-card" href="{{ url('/rekap') }}">
            <h3>📊 Rekap</h3>
            <p class="muted">Gambaran umum kondisi laporan.</p>
        </a>
    @endif
    @if(auth()->user()->role === 'koordinator')
        <a class="card menu-card" href="{{ url('/master/schools') }}">
            <h3>🏫 Data Sekolah</h3>
            <p class="muted">Kelola data pendukung sekolah.</p>
        </a>
        <a class="card menu-card" href="{{ url('/master/distributions') }}">
            <h3>🚚 Data Distribusi</h3>
            <p class="muted">Kelola distribusi makanan per sekolah.</p>
        </a>
    @endif
</div>
@endsection