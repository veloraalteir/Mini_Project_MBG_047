<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="card">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    <div class="form-row">
        <div class="form-group">
            <label for="school_id">Sekolah</label>
            <select name="school_id" id="school_id" required>
                <option value="">-- Pilih sekolah --</option>
                @foreach($schools as $s)
                    <option value="{{ $s->id }}" @selected(old('school_id', $report->school_id) == $s->id)>{{ $s->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="distribution_id">Distribusi Makanan</label>
            <select name="distribution_id" id="distribution_id" required>
                <option value="">-- Pilih sekolah dulu --</option>
            </select>
            <div class="hint">Daftar diambil otomatis dari server saat sekolah dipilih.</div>
        </div>
    </div>

    <div class="form-group">
        <label for="judul">Judul laporan</label>
        <input type="text" id="judul" name="judul" maxlength="150" value="{{ old('judul', $report->judul) }}" required>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="waktu_kejadian">Waktu kejadian</label>
            <input type="datetime-local" id="waktu_kejadian" name="waktu_kejadian"
                   max="{{ now()->format('Y-m-d\TH:i') }}"
                   value="{{ old('waktu_kejadian', $report->waktu_kejadian?->format('Y-m-d\TH:i')) }}" required>
        </div>
        <div class="form-group">
            <label for="jumlah_terdampak">Perkiraan jumlah orang terdampak</label>
            <input type="number" id="jumlah_terdampak" name="jumlah_terdampak" min="1"
                   value="{{ old('jumlah_terdampak', $report->jumlah_terdampak) }}" required>
        </div>
    </div>

    <div class="form-group">
        <label for="deskripsi">Deskripsi kejadian (min. 20 karakter)</label>
        <textarea id="deskripsi" name="deskripsi" required>{{ old('deskripsi', $report->deskripsi) }}</textarea>
        <div class="hint">Ceritakan keluhan yang dialami. Jangan menyebut diagnosis atau menuduh pihak tertentu.</div>
    </div>

    <div class="form-group">
        <label for="evidence">Bukti pendukung (opsional)</label>
        <input type="file" id="evidence" name="evidence" accept=".jpg,.jpeg,.png,.pdf">
        <div class="hint">JPG, JPEG, PNG, atau PDF. Maksimal 2 MB.
            @if($report->evidence_path) File saat ini: <b>{{ $report->evidence_name }}</b> (unggah file baru untuk mengganti). @endif
        </div>
    </div>

    <button class="btn btn-primary">Simpan Laporan</button>
    <a class="btn" href="{{ url('/reports') }}">Batal</a>
</form>

@push('scripts')
<script>
    const schoolSel = document.getElementById('school_id');
    const distSel = document.getElementById('distribution_id');
    const baseUrl = "{{ url('/schools') }}";
    let selected = "{{ old('distribution_id', $report->distribution_id) }}";

    async function loadDistributions(schoolId) {
        if (!schoolId) {
            distSel.innerHTML = '<option value="">-- Pilih sekolah dulu --</option>';
            return;
        }
        distSel.innerHTML = '<option value="">Memuat...</option>';
        try {
            const res = await fetch(`${baseUrl}/${schoolId}/distributions`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const items = await res.json();

            distSel.innerHTML = '';
            if (items.length === 0) {
                distSel.innerHTML = '<option value="">Belum ada distribusi</option>';
                return;
            }
            distSel.appendChild(new Option('-- Pilih distribusi --', ''));
            items.forEach(i => {
                const opt = new Option(i.label, i.id); // textContent aman dari XSS
                if (String(i.id) === String(selected)) opt.selected = true;
                distSel.appendChild(opt);
            });
        } catch (e) {
            distSel.innerHTML = '<option value="">Gagal memuat distribusi</option>';
        }
    }

    schoolSel.addEventListener('change', () => {
        selected = '';
        loadDistributions(schoolSel.value);
    });

    if (schoolSel.value) loadDistributions(schoolSel.value);
</script>
@endpush