<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Sale::select(
            'client_contact',
            'company',
            'email',
            'mobile',
            'location',
            'requirement',
            'type',
            'source',
            'follow_up_date',
            'status',
            'remarks',
            'created_at'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Client Contact',
            'Company',
            'Email',
            'Mobile',
            'Location',
            'Requirement',
            'Type',
            'Source',
            'Follow Up Date',
            'Status',
            'Remarks',
            'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [

            // Heading Row
            1 => [

                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],

                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => [
                        'rgb' => '198754', // Green
                    ],
                ],
            ],
        ];
    }
}
