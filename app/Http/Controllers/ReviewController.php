<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportForwarding;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reports = Report::with(['user', 'school', 'distribution'])
            ->search($request->only('q', 'status', 'school_id'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('review.index', [
            'reports' => $reports,
            'schools' => School::orderBy('nama')->get(),
        ]);
    }

    public function show(Request $request, Report $report)
    {
        $report->load(['user', 'school', 'distribution', 'forwardings.user', 'statusLogs']);

        // Keterkaitan: laporan lain pada distribusi yang sama
        $related = Report::with('user')
            ->where('distribution_id', $report->distribution_id)
            ->where('id', '!=', $report->id)
            ->latest()
            ->get();

        $schoolTotal = Report::where('school_id', $report->school_id)->count();

        return view('review.show', [
            'report' => $report,
            'related' => $related,
            'schoolTotal' => $schoolTotal,
            'allowed' => $report->transitionsFor($request->user()),
        ]);
    }

    public function transition(Request $request, Report $report)
    {
        $user = $request->user();

        $data = $request->validate([
            'to' => ['required', Rule::in(array_keys(Report::LABELS))],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'pihak' => ['nullable', Rule::in(array_keys(ReportForwarding::PIHAK))],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);
        $to = $data['to'];

        // BR-05a: perubahan harus sesuai alur status
        if (! in_array($to, Report::TRANSITIONS[$report->status] ?? [], true)) {
            return back()->with('error', 'Perubahan status dari "' . $report->status_label . '" ke "' . Report::LABELS[$to] . '" tidak sesuai alur.');
        }

        // BR-05b: perubahan harus sesuai kewenangan role
        abort_unless(in_array($to, $report->transitionsFor($user), true), 403, 'Anda tidak berwenang melakukan perubahan ini.');

        // BR-06: catatan wajib bila meminta dilengkapi / menandai perlu diteruskan
        if (in_array($to, [Report::PERLU_DILENGKAPI, Report::PERLU_DITERUSKAN], true) && blank($data['catatan'] ?? null)) {
            throw ValidationException::withMessages(['catatan' => 'Catatan wajib diisi untuk perubahan status ini.']);
        }
        // BR-06: pihak tujuan wajib saat mencatat penerusan
        if ($to === Report::DITERUSKAN && blank($data['pihak'] ?? null)) {
            throw ValidationException::withMessages(['pihak' => 'Pihak tujuan penerusan wajib dipilih.']);
        }

        DB::transaction(function () use ($report, $user, $data, $to) {
            if ($to === Report::DITERUSKAN) {
                $report->forwardings()->create([
                    'user_id' => $user->id,
                    'pihak' => $data['pihak'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'forwarded_at' => now(),
                ]);
            }

            if ($user->role === 'pemantau' && ! blank($data['catatan'] ?? null)) {
                $report->catatan_pemantau = $data['catatan'];
            }

            $report->status = $to;
            $report->save();
        });

        return redirect()->route('review.show', $report)->with('success', 'Status laporan diperbarui menjadi "' . Report::LABELS[$to] . '".');
    }
}