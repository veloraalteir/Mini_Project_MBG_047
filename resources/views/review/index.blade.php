@extends('layouts.app')
@section('title', 'Tinjau Laporan')
@section('content')
    <div class="page-head">
        <div>
            <h1>Tinjau Laporan</h1>
            <p class="muted">Semua laporan masuk. Peninjauan bukan penetapan penyebab atau pihak bertanggung jawab.</p>
        </div>
    </div>

    @include('partials.filter', ['action' => route('review.index')])

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Judul</th><th>Pelapor</th><th>Sekolah</th><th>Distribusi</th><th>Status</th><th>Masuk</th><th></th></tr>
                </thead>
                <tbody>
                @forelse($reports as $r)
                    <tr>
                        <td>{{ $r->judul }}</td>
                        <td>{{ $r->user->name }}</td>
                        <td>{{ $r->school->nama }}</td>
                        <td>{{ $r->distribution->tanggal->format('d M Y') }}</td>
                        <td><span class="badge badge-{{ $r->status }}">{{ $r->status_label }}</span></td>
                        <td>{{ $r->created_at->format('d M Y') }}</td>
                        <td><a class="btn btn-sm btn-primary" href="{{ route('review.show', $r) }}">Buka</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Tidak ada laporan yang cocok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $reports->links() }}
    </div>
@endsection