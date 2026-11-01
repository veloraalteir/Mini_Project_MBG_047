@extends('layouts.app')
@section('title', 'Data Sekolah')
@section('content')
    <h1>Data Sekolah</h1>
    <p class="muted">Data pendukung yang dipakai pelapor saat memilih sekolah.</p>

    <form method="POST" action="{{ route('master.schools.store') }}" class="card">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="nama">Nama sekolah</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat (opsional)</label>
                <input type="text" id="alamat" name="alamat" value="{{ old('alamat') }}">
            </div>
        </div>
        <button class="btn btn-primary">+ Tambah Sekolah</button>
    </form>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Nama</th><th>Alamat</th><th>Distribusi</th><th>Laporan</th><th></th></tr></thead>
                <tbody>
                @forelse($schools as $s)
                    <tr>
                        <td>{{ $s->nama }}</td>
                        <td>{{ $s->alamat ?? '-' }}</td>
                        <td>{{ $s->distributions_count }}</td>
                        <td>{{ $s->reports_count }}</td>
                        <td>
                            <form method="POST" action="{{ route('master.schools.destroy', $s) }}"
                                  onsubmit="return confirm('Hapus sekolah ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Belum ada sekolah.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $schools->links() }}
    </div>
@endsection