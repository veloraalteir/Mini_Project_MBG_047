<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    public const DIAJUKAN = 'diajukan';
    public const DITINJAU = 'ditinjau';
    public const PERLU_DILENGKAPI = 'perlu_dilengkapi';
    public const PERLU_DITERUSKAN = 'perlu_diteruskan';
    public const DITERUSKAN = 'diteruskan';
    public const SELESAI = 'selesai';

    public const LABELS = [
        self::DIAJUKAN => 'Diajukan',
        self::DITINJAU => 'Sedang Ditinjau',
        self::PERLU_DILENGKAPI => 'Perlu Dilengkapi',
        self::PERLU_DITERUSKAN => 'Perlu Diteruskan',
        self::DITERUSKAN => 'Sudah Diteruskan',
        self::SELESAI => 'Selesai',
    ];

    // Alur yang diperbolehkan: status sekarang => daftar status tujuan
    public const TRANSITIONS = [
        self::DIAJUKAN => [self::DITINJAU],
        self::DITINJAU => [self::PERLU_DILENGKAPI, self::PERLU_DITERUSKAN, self::SELESAI],
        self::PERLU_DILENGKAPI => [self::DIAJUKAN], // hanya lewat perbaikan oleh pelapor
        self::PERLU_DITERUSKAN => [self::DITERUSKAN],
        self::DITERUSKAN => [self::SELESAI],
        self::SELESAI => [],
    ];

    protected $fillable = [
        'user_id', 'school_id', 'distribution_id', 'judul', 'deskripsi',
        'waktu_kejadian', 'jumlah_terdampak', 'status', 'catatan_pemantau',
        'evidence_path', 'evidence_name',
    ];

    protected function casts(): array
    {
        return ['waktu_kejadian' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(Distribution::class);
    }

    public function forwardings(): HasMany
    {
        return $this->hasMany(ReportForwarding::class)->orderBy('id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ReportStatusLog::class)->orderBy('id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? $this->status;
    }

    // BR-04: pelapor hanya boleh mengubah laporan pada status ini
    public function isEditable(): bool
    {
        return in_array($this->status, [self::DIAJUKAN, self::PERLU_DILENGKAPI], true);
    }

    // BR-05: status tujuan yang boleh dipilih oleh user tertentu
    public function transitionsFor(User $user): array
    {
        $next = self::TRANSITIONS[$this->status] ?? [];

        return array_values(array_filter($next, function (string $to) use ($user) {
            return match (true) {
                $this->status === self::PERLU_DILENGKAPI => false, // hanya pelapor lewat edit
                $to === self::DITERUSKAN, $this->status === self::DITERUSKAN => $user->role === User::KOORDINATOR,
                default => $user->role === User::PEMANTAU,
            };
        }));
    }

    // Pencarian + filter, dipakai di daftar laporan
    public function scopeSearch(Builder $query, array $f): Builder
    {
        return $query
            ->when($f['q'] ?? null, function ($q, $v) {
                $q->where(function ($w) use ($v) {
                    $w->where('judul', 'like', "%{$v}%")->orWhere('deskripsi', 'like', "%{$v}%");
                });
            })
            ->when($f['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($f['school_id'] ?? null, fn ($q, $v) => $q->where('school_id', $v));
    }
}