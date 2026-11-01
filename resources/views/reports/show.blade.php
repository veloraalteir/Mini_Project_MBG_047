@extends('layouts.app')
@section('title', 'Detail Laporan')
@section('content')
    <p><a href="{{ route('reports.index') }}">← Kembali ke Laporan Saya</a></p>

    @include('reports._detail')

    @if($report->isEditable())
        <a class="btn btn-primary" href="{{ route('reports.edit', $report) }}">✏️ Ubah Laporan</a>
    @else
        <div class="alert alert-info">Laporan tidak dapat diubah pada status "{{ $report->status_label }}".</div>
    @endif
@endsection