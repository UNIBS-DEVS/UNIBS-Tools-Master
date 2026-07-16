<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;
use App\Exports\ReviewReportExport;


class ReviewReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $reviews;
    public $user;
    public $type; // pdf or excel

    public function __construct($reviews, $user, $type)
    {
        $this->reviews = $reviews;
        $this->user = $user;
        $this->type = $type;
    }

    public function build()
    {
        $mail = $this->subject('Reviews Report')
            ->view('emails.reviews-report', [
                'user' => $this->user
            ]);

        // ✅ PDF Attachment
        if ($this->type === 'pdf') {

            $pdf = Pdf::loadView('reviews.pdf', [
                'reviews' => $this->reviews
            ])->setPaper('a4', 'landscape');

            $mail->attachData(
                $pdf->output(),
                'Reviews Report.pdf',
                ['mime' => 'application/pdf']
            );
        }

        // ✅ Excel Attachment
        if ($this->type === 'excel') {

            $excel = Excel::raw(
                new ReviewReportExport($this->reviews),
                ExcelExcel::XLSX
            );

            $mail->attachData(
                $excel,
                'Reviews Report.xlsx',
                ['mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            );
        }

        return $mail;
    }
}
