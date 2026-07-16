<?php

namespace App\Exports;

use App\Models\Tender;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TendersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        $filters = session('tender_filters', []);

        $query = Tender::with([
            'primaryUser',
            'secondaryUser',
        ]);

        if (!empty($filters['tender_num'])) {
            $query->where('tender_num', 'like', '%' . $filters['tender_num'] . '%');
        }

        if (!empty($filters['primary_user_id'])) {
            $query->where('primary_user_id', $filters['primary_user_id']);
        }

        if (!empty($filters['secondary_user_id'])) {
            $query->where('secondary_user_id', $filters['secondary_user_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->where('status', 'Pending');
        }

        if (!empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        return $query->latest()->get();
    }

    public function map($tender): array
    {
        return [
            $tender->tender_num,
            $tender->primaryUser?->name,
            $tender->secondaryUser?->name,
            // $tender->submission_date,

            // Format submission_date
            Carbon::parse($tender->submission_date)
                ->format('j M, Y'),

            $tender->type,
            $tender->status,
            // $tender->due_date,

            // Format due_date
            Carbon::parse($tender->due_date)
                ->format('j M, Y'),

            $tender->estimated_value,
            $tender->state,
            $tender->department,
            $tender->bid_price,
            $tender->platform,

            // Format Created At
            Carbon::parse($tender->created_at)
                ->format('j M, Y h:i A'),
        ];
    }

    public function headings(): array
    {
        return [
            'Tender Number',
            'Primary User',
            'Secondary User',
            'Submission Date',
            'Type',
            'Status',
            'Due Date',
            'Estimated Value',
            'State',
            'Department',
            'Bid Price',
            'Platform',
            'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => [
                        'rgb' => '198754',
                    ],
                ],
            ],
        ];
    }
}
