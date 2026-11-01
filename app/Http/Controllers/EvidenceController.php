<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvidenceController extends Controller
{
    // File bukti disimpan privat; hanya pemilik laporan dan petugas yang boleh membuka
    public function show(Request $request, Report $report)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        abort_unless(
            $report->user_id === $user->id || $user->hasRole('pemantau', 'koordinator'),
            403,
            'Anda tidak berhak membuka bukti ini.'
        );
        abort_unless($report->evidence_path && Storage::disk('local')->exists($report->evidence_path), 404);

        return response()->file(
            Storage::disk('local')->path($report->evidence_path),
            ['Content-Disposition' => 'inline; filename="' . $report->evidence_name . '"']
        );
    }
}