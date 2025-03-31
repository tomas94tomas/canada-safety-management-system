<?php

namespace App\Mail;

use App\Models\Email;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class ReportReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Report $report)
    {
    }

    public function envelope(): Envelope
    {
        $currentDate = now()->format('Y-m-d'); // Format date as YYYY-MM-DD
        $subject = "New POA SMS Report [{$currentDate}]";

        // Retrieve all email addresses from the database
        $addresses = Email::all()->pluck('email')->map(function ($email) {
            return new Address($email); // Convert each string email to an Address instance
        });

        return new Envelope(
            from: new Address('safety-poa.no-reply@fltechnics.com', 'Safety POA No-Reply'),
            to: $addresses->toArray(), // Pass the email addresses to the `to` method
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.report-received',
            with: [
                'attachments' => $this->report->attachments,
                'sms_number' => $this->report->sms_number, // Ensure this is included
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
