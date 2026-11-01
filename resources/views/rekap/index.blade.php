@extends('layouts.app')
@section('title', 'Rekap Laporan')
@section('content')
    <h1>Rekap Laporan</h1>
    <p class="muted">Ringkasan sederhana dari data laporan yang tersimpan.</p>

    <form method="GET" action="{{ route('rekap') }}" class="card filter-bar">
        <select name="school_id">
            <option value="">Semua sekolah</option>
            @foreach($schools as $s)
                <option value="{{ $s->id }}" @selected(request('school_id') == $s->id)>{{ $s->nama }}</option>
            @endforeach
        </select>
        <input type="date" name="dari" value="{{ request('dari') }}" title="Dari tanggal masuk">
        <input type="date" name="sampai" value="{{ request('sampai') }}" title="Sampai tanggal masuk">
        <button class="btn btn-primary">Terapkan</button>
        <a class="btn" href="{{ route('rekap') }}">Reset</a>
    </form>

    <div class="stats">
        <div class="stat"><b>{{ $total }}</b><span>Total laporan</span></div>
        <div class="stat orange"><b>{{ $diproses }}</b><span>Masih diproses</span></div>
        <div class="stat green"><b>{{ $selesai }}</b><span>Selesai</span></div>
        <div class="stat purple"><b>{{ $perluDiteruskan }}</b><span>Menunggu diteruskan</span></div>
    </div>

    <div class="card">
        <h2>Rekap per sekolah</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Sekolah</th><th>Total</th><th>Masih diproses</th><th>Perlu diteruskan</th><th>Sudah diteruskan</th><th>Selesai</th></tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r->school->nama }}</td>
                        <td><b>{{ (int) $r->total }}</b></td>
                        <td>{{ (int) $r->total - (int) $r->selesai }}</td>
                        <td>{{ (int) $r->perlu_diteruskan }}</td>
                        <td>{{ (int) $r->diteruskan }}</td>
                        <td>{{ (int) $r->selesai }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Tidak ada data pada filter ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection