<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Http\Requests\StoreReportRequest;
use App\Mail\ReportReceivedMail;
use App\Models\Report;
use App\Services\AttachmentService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index()
    {
        return Inertia::render('Form');
    }

    public function store(StoreReportRequest $request, AttachmentService $attachmentService)
    {
        $report = Report::create([...$request->validated(), 'status' => ReportStatus::LOCAL]);

        if ($request->hasFile('uploadedFiles')) {
            $attachmentService->saveReportAttachments($report, $request->file('uploadedFiles'));
        }

        // Schedule email to be sent in 3 minutes
        Mail::to(config('mail.report_to'))->later(Carbon::now()->addMinutes(3), new ReportReceivedMail($report));
    }
}
