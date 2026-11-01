<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class MasterSchoolController extends Controller
{
    public function index()
    {
        return view('master.schools', [
            'schools' => School::withCount(['distributions', 'reports'])->orderBy('nama')->paginate(10),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150', 'unique:schools,nama'],
            'alamat' => ['nullable', 'string', 'max:255'],
        ]);

        School::create($data);

        return back()->with('success', 'Sekolah berhasil ditambahkan.');
    }

    public function destroy(School $school)
    {
        // BR-07: sekolah yang sudah dipakai tidak boleh dihapus
        if ($school->distributions()->exists() || $school->reports()->exists()) {
            return back()->with('error', 'Sekolah tidak dapat dihapus karena sudah memiliki distribusi atau laporan.');
        }

        $school->delete();

        return back()->with('success', 'Sekolah berhasil dihapus.');
    }
}