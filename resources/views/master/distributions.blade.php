@extends('layouts.app')
@section('title', 'Data Distribusi')
@section('content')
    <h1>Data Distribusi</h1>
    <p class="muted">Satu distribusi = penyaluran makanan pada tanggal tertentu ke satu sekolah.</p>

    <form method="POST" action="{{ route('master.distributions.store') }}" class="card">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="school_id">Sekolah</label>
                <select name="school_id" id="school_id" required>
                    <option value="">-- Pilih sekolah --</option>
                    @foreach($schools as $s)
                        <option value="{{ $s->id }}" @selected(old('school_id') == $s->id)>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal distribusi</label>
                <input type="date" id="tanggal" name="tanggal" max="{{ now()->toDateString() }}" value="{{ old('tanggal') }}" required>
            </div>
        </div>
        <div class="form-group">
            <label for="menu">Menu</label>
            <input type="text" id="menu" name="menu" value="{{ old('menu') }}" required>
        </div>
        <button class="btn btn-primary">+ Tambah Distribusi</button>
    </form>

    <form method="GET" action="{{ route('master.distributions.index') }}" class="card filter-bar">
        <select name="school_id">
            <option value="">Semua sekolah</option>
            @foreach($schools as $s)
                <option value="{{ $s->id }}" @selected(request('school_id') == $s->id)>{{ $s->nama }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary">Filter</button>
        <a class="btn" href="{{ route('master.distributions.index') }}">Reset</a>
    </form>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Tanggal</th><th>Sekolah</th><th>Menu</th><th>Laporan</th><th></th></tr></thead>
                <tbody>
                @forelse($distributions as $d)
                    <tr>
                        <td>{{ $d->tanggal->format('d M Y') }}</td>
                        <td>{{ $d->school->nama }}</td>
                        <td>{{ $d->menu }}</td>
                        <td>{{ $d->reports_count }}</td>
                        <td>
                            <form method="POST" action="{{ route('master.distributions.destroy', $d) }}"
                                  onsubmit="return confirm('Hapus distribusi ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Belum ada distribusi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $distributions->links() }}
    </div>
@endsection