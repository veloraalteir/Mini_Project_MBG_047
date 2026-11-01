<div class="card">
    <div class="page-head">
        <div>
            <h1>{{ $report->judul }}</h1>
            <span class="muted">Dibuat {{ $report->created_at->format('d M Y H:i') }}</span>
        </div>
        <span class="badge badge-{{ $report->status }}">{{ $report->status_label }}</span>
    </div>

    <dl class="detail">
        <dt>Sekolah</dt><dd>{{ $report->school->nama }}</dd>
        <dt>Distribusi</dt><dd>{{ $report->distribution->tanggal->format('d M Y') }} — {{ $report->distribution->menu }}</dd>
        <dt>Waktu kejadian</dt><dd>{{ $report->waktu_kejadian->format('d M Y H:i') }}</dd>
        <dt>Jumlah terdampak</dt><dd>{{ $report->jumlah_terdampak }} orang (menurut pelapor)</dd>
        <dt>Deskripsi</dt><dd>{!! nl2br(e($report->deskripsi)) !!}</dd>
        <dt>Bukti pendukung</dt>
        <dd>
            @if($report->evidence_path)
                <a href="{{ route('evidence.show', $report) }}" target="_blank">📎 {{ $report->evidence_name }}</a>
            @else
                <span class="muted">Tidak ada</span>
            @endif
        </dd>
    </dl>
</div>

@if($report->catatan_pemantau)
    <div class="alert alert-info"><b>Catatan Pemantau:</b><br>{!! nl2br(e($report->catatan_pemantau)) !!}</div>
@endif

@if($report->forwardings->isNotEmpty())
    <div class="card">
        <h3>Informasi sudah diteruskan</h3>
        <ul class="timeline">
            @foreach($report->forwardings as $f)
                <li><b>{{ $f->pihak_label }}</b> · {{ $f->forwarded_at->format('d M Y H:i') }}
                    @if($f->keterangan)<br><span class="muted">{{ $f->keterangan }}</span>@endif
                </li>
            @endforeach
        </ul>
    </div>
@endif

@if($report->statusLogs->isNotEmpty())
    <div class="card">
        <h3>Riwayat status</h3>
        <ul class="timeline">
            @foreach($report->statusLogs as $log)
                <li>
                    {{ $log->from_status ? \App\Models\Report::LABELS[$log->from_status] : '—' }}
                    → <b>{{ \App\Models\Report::LABELS[$log->to_status] }}</b>
                    <span class="muted"> · {{ $log->created_at->format('d M Y H:i') }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif