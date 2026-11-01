@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="auth-box">
    <form method="POST" action="{{ route('login') }}" class="card">
        @csrf
        <h1>Masuk</h1>
        <p class="muted">Masuk untuk melapor atau memantau laporan MBG.</p>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <label class="check"><input type="checkbox" name="remember"> Ingat saya</label>
        <button class="btn btn-primary btn-block">Masuk</button>
        <p class="muted center">Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai pelapor</a></p>
    </form>

    <div class="card">
        <h3>Akun demo</h3>
        <div class="table-wrap">
            <table>
                <tr><th>Role</th><th>Email</th><th>Password</th></tr>
                <tr><td>Pelapor</td><td>pelapor@mbg.test</td><td>password</td></tr>
                <tr><td>Pelapor 2</td><td>pelapor2@mbg.test</td><td>password</td></tr>
                <tr><td>Pemantau</td><td>pemantau@mbg.test</td><td>password</td></tr>
                <tr><td>Koordinator</td><td>koordinator@mbg.test</td><td>password</td></tr>
            </table>
        </div>
    </div>
</div>
@endsection