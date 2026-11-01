@extends('layouts.app')
@section('title', 'Laporan Saya')
@section('content')
    <div class="page-head">
        <div>
            <h1>Laporan Saya</h1>
            <p class="muted">Hanya laporan milik Anda yang tampil di sini.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('reports.create') }}">+ Buat Laporan</a>
    </div>

    @include('partials.filter', ['action' => route('reports.index')])

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Judul</th><th>Sekolah</th><th>Distribusi</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                @forelse($reports as $r)
                    <tr>
                        <td>{{ $r->judul }}</td>
                        <td>{{ $r->school->nama }}</td>
                        <td>{{ $r->distribution->tanggal->format('d M Y') }}</td>
                        <td><span class="badge badge-{{ $r->status }}">{{ $r->status_label }}</span></td>
                        <td>{{ $r->created_at->format('d M Y') }}</td>
                        <td class="actions">
                            <a class="btn btn-sm" href="{{ route('reports.show', $r) }}">Detail</a>
                            @if($r->isEditable())
                                <a class="btn btn-sm btn-primary" href="{{ route('reports.edit', $r) }}">Ubah</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada laporan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $reports->links() }}
    </div>
@endsection