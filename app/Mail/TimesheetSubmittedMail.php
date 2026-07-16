<?php

namespace App\Mail;

use App\Models\Timesheet;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class TimesheetSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $timesheet;

    public function __construct(Timesheet $timesheet)
    {
        $this->timesheet = $timesheet;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Timesheet Submitted'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.timesheet-submitted'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
