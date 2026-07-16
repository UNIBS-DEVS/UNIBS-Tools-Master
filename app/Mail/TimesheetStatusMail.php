<?php

namespace App\Mail;

use App\Models\Timesheet;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TimesheetStatusMail extends Mailable
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
            subject: 'Timesheet ' . ucfirst($this->timesheet->status),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.timesheet-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
