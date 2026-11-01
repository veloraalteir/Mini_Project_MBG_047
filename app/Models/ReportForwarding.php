<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportForwarding extends Model
{
    public const PIHAK = [
        'sekolah' => 'Sekolah',
        'sppg' => 'SPPG',
        'faskes' => 'Fasilitas Kesehatan',
        'instansi' => 'Instansi Terkait Lain',
    ];

    protected $fillable = ['report_id', 'user_id', 'pihak', 'keterangan', 'forwarded_at'];

    protected function casts(): array
    {
        return ['forwarded_at' => 'datetime'];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPihakLabelAttribute(): string
    {
        return self::PIHAK[$this->pihak] ?? $this->pihak;
    }
}