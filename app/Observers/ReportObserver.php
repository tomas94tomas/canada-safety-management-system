<?php

namespace App\Observers;

use App\Models\Report;

class ReportObserver
{
    /**
     * Handle the Report "created" event.
     */
    public function created(Report $report): void
    {
        $report->syncOriginal();

        $report->sms_number = "SMS-POA-" . str_pad($report->id, 10, 0, STR_PAD_LEFT);

        $report->saveQuietly();
    }
}
