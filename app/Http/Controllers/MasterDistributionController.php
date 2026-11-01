<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\School;
use Illuminate\Http\Request;

class MasterDistributionController extends Controller
{
    public function index(Request $request)
    {
        $distributions = Distribution::with('school')
            ->withCount('reports')
            ->when($request->school_id, fn ($q, $v) => $q->where('school_id', $v))
            ->orderByDesc('tanggal')
            ->paginate(10)
            ->withQueryString();

        return view('master.distributions', [
            'distributions' => $distributions,
            'schools' => School::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'school_id' => ['required', 'exists:schools,id'],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'menu' => ['required', 'string', 'max:200'],
        ]);

        Distribution::create($data);

        return back()->with('success', 'Distribusi berhasil ditambahkan.');
    }

    public function destroy(Distribution $distribution)
    {
        // BR-07: distribusi yang sudah dilaporkan tidak boleh dihapus
        if ($distribution->reports()->exists()) {
            return back()->with('error', 'Distribusi tidak dapat dihapus karena sudah dikaitkan dengan laporan.');
        }

        $distribution->delete();

        return back()->with('success', 'Distribusi berhasil dihapus.');
    }
}