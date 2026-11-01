<?php

namespace App\Observers;

use App\Models\Report;
use App\Models\ReportStatusLog;
use Illuminate\Support\Facades\Auth;

class ReportObserver
{
    // Dipanggil otomatis setiap kali model Report berhasil di-update
    public function updated(Report $report): void
    {
        if ($report->wasChanged('status')) {
            ReportStatusLog::create([
                'report_id' => $report->id,
                'from_status' => $report->getOriginal('status'),
                'to_status' => $report->status,
                'user_id' => Auth::id(), // null bila diubah dari luar request (mis. tinker)
            ]);
        }
    }
}