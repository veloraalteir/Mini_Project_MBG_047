@extends('layouts.app')
@section('title', 'Tinjau Laporan')
@section('content')
    <p><a href="{{ route('review.index') }}">← Kembali ke daftar</a></p>

    <div class="card">
        <dl class="detail">
            <dt>Pelapor</dt><dd>{{ $report->user->name }} ({{ $report->user->email }})</dd>
        </dl>
    </div>

    @include('reports._detail')

    <div class="card">
        <h3>🔗 Keterkaitan laporan</h3>
        <p>
            Sekolah <b>{{ $report->school->nama }}</b> memiliki <b>{{ $schoolTotal }}</b> laporan.
            Pada distribusi yang sama terdapat <b>{{ $related->count() }}</b> laporan lain.
        </p>
        @if($related->isNotEmpty())
            <ul class="timeline">
                @foreach($related as $x)
                    <li>
                        <a href="{{ route('review.show', $x) }}">{{ $x->judul }}</a>
                        — {{ $x->user->name }}
                        <span class="badge badge-{{ $x->status }}">{{ $x->status_label }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if(count($allowed))
        <div class="card">
            <h3>Tindakan</h3>
            <form method="POST" action="{{ route('review.transition', $report) }}">
                @csrf
                <div class="form-group">
                    <label for="to">Ubah status menjadi</label>
                    <select name="to" id="to" required>
                        @foreach($allowed as $s)
                            <option value="{{ $s }}">{{ \App\Models\Report::LABELS[$s] }}</option>
                        @endforeach
                    </select>
                </div>

                @if(auth()->user()->role === 'pemantau')
                    <div class="form-group">
                        <label for="catatan">Catatan</label>
                        <textarea name="catatan" id="catatan">{{ old('catatan') }}</textarea>
                        <div class="hint">Wajib diisi bila memilih "Perlu Dilengkapi" atau "Perlu Diteruskan".</div>
                    </div>
                @endif

                @if(auth()->user()->role === 'koordinator')
                    <div id="forward-box" style="display:none">
                        <div class="form-group">
                            <label for="pihak">Diteruskan kepada</label>
                            <select name="pihak" id="pihak">
                                <option value="">-- Pilih pihak --</option>
                                @foreach(\App\Models\ReportForwarding::PIHAK as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan penerusan</label>
                            <textarea name="keterangan" id="keterangan"></textarea>
                        </div>
                    </div>
                @endif

                <button class="btn btn-primary">Simpan</button>
            </form>
        </div>
    @else
        <div class="alert alert-info">Tidak ada tindakan yang tersedia untuk Anda pada status "{{ $report->status_label }}".</div>
    @endif
@endsection

@push('scripts')
<script>
    const to = document.getElementById('to');
    const box = document.getElementById('forward-box');
    if (to && box) {
        const toggle = () => box.style.display = (to.value === 'diteruskan') ? 'block' : 'none';
        to.addEventListener('change', toggle);
        toggle();
    }
</script>
@endpush