@extends('layouts.app')
@section('title', 'Ubah Laporan')
@section('content')
    <h1>Ubah Laporan</h1>
    @if($report->status === 'perlu_dilengkapi')
        <div class="alert alert-warn">Pemantau meminta Anda melengkapi laporan. Setelah disimpan, laporan akan ditinjau ulang.</div>
    @endif
    @if($report->catatan_pemantau)
        <div class="alert alert-info"><b>Catatan Pemantau:</b><br>{!! nl2br(e($report->catatan_pemantau)) !!}</div>
    @endif
    @include('reports._form', ['action' => route('reports.update', $report), 'method' => 'PUT'])
@endsection