<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SalesReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fileName;
    public $type;

    public function __construct($fileName, $type)
    {
        $this->fileName = $fileName;
        $this->type = $type;
    }

    public function build()
    {
        $reportName = match ($this->type) {
            'daily_summary'   => 'Daily Summary Report',
            'weekly_summary'  => 'Weekly Summary Report',
            'follow_up'       => 'Follow Up Report',
            'closures'        => 'Closures Report',
            'licence_report'  => 'Licence Report',
            'tender_report'   => 'Tender Report',
            default           => 'Sales Report',
        };

        return $this->subject($reportName)
            ->view('emails.sales-report')
            ->attach(
                storage_path('app/public/' . $this->fileName),
                [
                    'as' => $this->fileName,
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );
    }
}
