<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\School;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'school_id' => ['nullable', 'exists:schools,id'],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ]);

        $base = Report::query()
            ->when($request->school_id, fn ($q, $v) => $q->where('school_id', $v))
            ->when($request->dari, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->sampai, fn ($q, $v) => $q->whereDate('created_at', '<=', $v));

        $total = (clone $base)->count();
        $selesai = (clone $base)->where('status', Report::SELESAI)->count();
        $diproses = $total - $selesai;
        $perluDiteruskan = (clone $base)->where('status', Report::PERLU_DITERUSKAN)->count();

        $rows = (clone $base)
            ->selectRaw("school_id,
                COUNT(*) as total,
                SUM(status = 'perlu_diteruskan') as perlu_diteruskan,
                SUM(status = 'diteruskan') as diteruskan,
                SUM(status = 'selesai') as selesai")
            ->groupBy('school_id')
            ->orderByDesc('total')
            ->with('school')
            ->get();

        return view('rekap.index', [
            'schools' => School::orderBy('nama')->get(),
            'total' => $total,
            'diproses' => $diproses,
            'selesai' => $selesai,
            'perluDiteruskan' => $perluDiteruskan,
            'rows' => $rows,
        ]);
    }
}