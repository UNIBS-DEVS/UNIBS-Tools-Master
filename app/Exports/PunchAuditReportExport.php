<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PunchAuditReportExport implements FromCollection, WithHeadings
{
    protected $reports;

    public function __construct($reports)
    {
        $this->reports = $reports;
    }

    public function collection()
    {
        return collect($this->reports)->map(function ($report) {

            return [

                'Name' => $report['user']->name,

                'Month' => $report['attendance_date']->format('F Y'),

                'Day' => $report['day'],

                'Date' => $report['attendance_date']->format('d/m/Y'),

                'Worked Hours' => number_format($report['worked_hours_decimal'], 2),

                'Shortage' => number_format($report['shortage'], 2),

                'First In' => optional($report['first_in'])->format('h:i A'),

                'Last Out' => optional($report['last_out'])->format('h:i A'),

            ];
        });
    }

    public function headings(): array
    {
        return [
            'Name',
            'Month',
            'Day',
            'Date',
            'Worked Hours',
            'Shortage',
            'First In',
            'Last Out',
        ];
    }
}
