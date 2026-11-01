<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Models\Distribution;
use App\Models\Report;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = Report::with(['school', 'distribution'])
            ->where('user_id', Auth::id()) // hanya laporan milik sendiri
            ->search($request->only('q', 'status', 'school_id'))
            ->latest()
            ->paginate(8)
            ->withQueryString(); // filter tetap aktif saat pindah halaman

        return view('reports.index', [
            'reports' => $reports,
            'schools' => School::orderBy('nama')->get(),
        ]);
    }

    public function create()
    {
        return view('reports.create', [
            'report' => new Report(),
            'schools' => School::orderBy('nama')->get(),
        ]);
    }

    public function store(ReportRequest $request)
    {
        $data = $request->validated();
        $this->ensureBusinessRules($data);

        unset($data['evidence']);
        if ($request->hasFile('evidence')) {
            $data['evidence_path'] = $request->file('evidence')->store('evidence', 'local');
            $data['evidence_name'] = $request->file('evidence')->getClientOriginalName();
        }

        // user_id dan status ditentukan server, bukan dari input pengguna
        $data['user_id'] = Auth::id();
        $data['status'] = Report::DIAJUKAN;

        Report::create($data);

        return redirect()->route('reports.index')->with('success', 'Laporan berhasil dikirim.');
    }

    public function show(Report $report)
    {
        $this->authorizeOwner($report);
        $report->load(['school', 'distribution', 'forwardings', 'statusLogs']);

        return view('reports.show', compact('report'));
    }

    public function edit(Report $report)
    {
        $this->authorizeOwner($report);
        $this->authorizeEditable($report);

        return view('reports.edit', [
            'report' => $report,
            'schools' => School::orderBy('nama')->get(),
        ]);
    }

    public function update(ReportRequest $request, Report $report)
    {
        $this->authorizeOwner($report);
        $this->authorizeEditable($report);

        $data = $request->validated();
        $this->ensureBusinessRules($data, $report);

        unset($data['evidence']);
        if ($request->hasFile('evidence')) {
            if ($report->evidence_path) {
                Storage::disk('local')->delete($report->evidence_path);
            }
            $data['evidence_path'] = $request->file('evidence')->store('evidence', 'local');
            $data['evidence_name'] = $request->file('evidence')->getClientOriginalName();
        }

        // Setelah diperbaiki, laporan kembali masuk antrean peninjauan
        if ($report->status === Report::PERLU_DILENGKAPI) {
            $data['status'] = Report::DIAJUKAN;
        }

        $report->update($data);

        return redirect()->route('reports.show', $report)->with('success', 'Laporan berhasil diperbarui.');
    }

    // ---------- Authorization & Business Rule ----------

    private function authorizeOwner(Report $report): void
    {
        abort_unless($report->user_id === Auth::id(), 403, 'Anda tidak berhak mengakses laporan ini.');
    }

    // BR-04
    private function authorizeEditable(Report $report): void
    {
        abort_unless($report->isEditable(), 403, 'Laporan tidak dapat diubah pada status "' . $report->status_label . '".');
    }

    private function ensureBusinessRules(array $data, ?Report $current = null): void
    {
        // BR-01: distribusi harus milik sekolah yang dipilih
        $distribution = Distribution::where('id', $data['distribution_id'])
            ->where('school_id', $data['school_id'])
            ->first();

        if (! $distribution) {
            throw ValidationException::withMessages([
                'distribution_id' => 'Distribusi tidak sesuai dengan sekolah yang dipilih.',
            ]);
        }

        // BR-02: kejadian tidak boleh terjadi sebelum makanan didistribusikan
        if (\Carbon\Carbon::parse($data['waktu_kejadian'])->lt($distribution->tanggal->copy()->startOfDay())) {
            throw ValidationException::withMessages([
                'waktu_kejadian' => 'Waktu kejadian tidak boleh sebelum tanggal distribusi (' . $distribution->tanggal->format('d M Y') . ').',
            ]);
        }

        // BR-03: satu pelapor hanya boleh punya satu laporan aktif per distribusi
        $duplicate = Report::where('user_id', Auth::id())
            ->where('distribution_id', $distribution->id)
            ->where('status', '!=', Report::SELESAI)
            ->when($current, fn ($q) => $q->where('id', '!=', $current->id))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'distribution_id' => 'Anda sudah memiliki laporan yang masih diproses untuk distribusi ini.',
            ]);
        }
    }
}