<form method="GET" action="{{ $action }}" class="card filter-bar">
    <input type="text" name="q" placeholder="Cari judul / deskripsi..." value="{{ request('q') }}">
    <select name="school_id">
        <option value="">Semua sekolah</option>
        @foreach($schools as $s)
            <option value="{{ $s->id }}" @selected(request('school_id') == $s->id)>{{ $s->nama }}</option>
        @endforeach
    </select>
    <select name="status">
        <option value="">Semua status</option>
        @foreach(\App\Models\Report::LABELS as $key => $label)
            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
        @endforeach
    </select>
    <button class="btn btn-primary">Terapkan</button>
    <a class="btn" href="{{ $action }}">Reset</a>
</form>