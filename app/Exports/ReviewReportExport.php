<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReviewReportExport implements FromCollection, WithHeadings
{
    private $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($row) {

            // ✅ Format duration
            $duration = '-';
            if (!empty($row->duration)) {
                $h = floor($row->duration / 3600);
                $m = floor(($row->duration % 3600) / 60);
                $s = $row->duration % 60;
                $duration = "{$h}h {$m}m {$s}s";
            }

            return [
                'Name' => $row->contactUser->name ?? '-',
                'From'     => $row->from_number ?? '-',
                'To'       => $row->to_number ?? '-',
                'Date'     => $row->call_date ?? '-',
                'Time'     => $row->call_time ?? '-',
                'Duration' => $duration,
                'Type'     => ucfirst($row->type ?? '-'),
                'Note'     => $row->notes ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Name',
            'From',
            'To',
            'Date',
            'Time',
            'Duration',
            'Type',
            'Note',
        ];
    }
}
