<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SourcingReportMail extends Mailable
{
    public $filePath;
    public $type;

    public function __construct($filePath, $type)
    {
        $this->filePath = $filePath;
        $this->type = $type;
    }

    public function build()
    {
        $reportName = match ($this->type) {
            'daily_summary'      => 'Daily Summary Report',
            'interview_schedule' => 'Interview Schedule Report',
            'closures'           => 'Closures Report',
            'customer_status'    => 'Customer Status Report',
            default              => 'Sourcing Report',
        };

        return $this
            ->subject($reportName)
            ->view('emails.sourcing-report', [
                'reportName' => $reportName
            ])
            ->attach(
                storage_path('app/public/' . $this->filePath)
            );
    }
}
