@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="auth-box">
    <form method="POST" action="{{ route('register') }}" class="card">
        @csrf
        <h1>Daftar Pelapor</h1>
        <p class="muted">Akun yang dibuat di sini otomatis berperan sebagai pelapor.</p>
        <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="form-group">
            <label for="password">Password (min. 8 karakter)</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Ulangi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>
        <button class="btn btn-primary btn-block">Daftar</button>
        <p class="muted center">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
    </form>
</div>
@endsection