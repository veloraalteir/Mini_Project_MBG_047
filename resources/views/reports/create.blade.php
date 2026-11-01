@extends('layouts.app')
@section('title', 'Buat Laporan')
@section('content')
    <h1>Buat Laporan Kejadian</h1>
    <p class="muted">Isi data sesuai kejadian yang Anda ketahui. Laporan akan ditinjau oleh Pemantau Komunitas.</p>
    @include('reports._form', ['action' => route('reports.store'), 'method' => 'POST'])
@endsection