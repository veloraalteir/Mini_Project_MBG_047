<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    // Hak akses dijaga oleh middleware role dan pengecekan kepemilikan di controller
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => ['required', 'exists:schools,id'],
            'distribution_id' => ['required', 'exists:distributions,id'],
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'min:20', 'max:2000'],
            'waktu_kejadian' => ['required', 'date', 'before_or_equal:now'],
            'jumlah_terdampak' => ['required', 'integer', 'min:1', 'max:10000'],
            'evidence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'school_id' => 'Sekolah',
            'distribution_id' => 'Distribusi',
            'judul' => 'Judul',
            'deskripsi' => 'Deskripsi',
            'waktu_kejadian' => 'Waktu kejadian',
            'jumlah_terdampak' => 'Jumlah terdampak',
            'evidence' => 'Bukti pendukung',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'exists' => ':attribute tidak valid.',
            'integer' => ':attribute harus berupa angka.',
            'date' => ':attribute bukan tanggal yang valid.',
            'judul.max' => 'Judul maksimal 150 karakter.',
            'deskripsi.min' => 'Deskripsi minimal 20 karakter.',
            'deskripsi.max' => 'Deskripsi maksimal 2000 karakter.',
            'jumlah_terdampak.min' => 'Jumlah terdampak minimal 1.',
            'waktu_kejadian.before_or_equal' => 'Waktu kejadian tidak boleh di masa depan.',
            'evidence.mimes' => 'Bukti harus berupa file JPG, JPEG, PNG, atau PDF.',
            'evidence.max' => 'Ukuran bukti maksimal 2 MB.',
            'evidence.uploaded' => 'Upload bukti gagal. Pastikan ukuran file maksimal 2 MB.',
        ];
    }
}