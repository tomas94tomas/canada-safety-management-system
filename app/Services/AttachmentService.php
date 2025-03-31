<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Report;

class AttachmentService
{

    public function saveReportAttachments(Report $report, $attachments)
    {
        foreach($attachments as $attachment) {
            $file = $attachment->store('report_attachments');

            Attachment::create([
               'file_path' => $file,
                'file_original_name' => $attachment->getClientOriginalName(),
                'file_hashed_name' => str_replace('report_attachments/', '', $file),
                'file_mime_type' => $attachment->getClientMimeType(),
                'report_id' => $report->id,
                'main_source' => 'POA',
            ]);
        }
    }
}
