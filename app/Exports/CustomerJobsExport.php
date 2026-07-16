<?php

namespace App\Exports;

use App\Models\CustomerJob;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerJobsExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $jobs = CustomerJob::with('customer');

        // Customer Filter
        if (!empty($this->filters['customer_id'])) {

            $jobs->whereIn(
                'customer_id',
                (array) $this->filters['customer_id']
            );
        }

        // Skill Filter
        if (!empty($this->filters['skill'])) {

            $skill = trim($this->filters['skill']);

            $jobs->whereRaw(
                "LOWER(skill) REGEXP ?",
                ['(^|[[:space:],-])' . strtolower($skill) . '([[:space:],-]|$)']
            );
        }

        // Position Filter
        if (!empty($this->filters['job_position'])) {

            $jobs->where(
                'position',
                'LIKE',
                '%' . $this->filters['job_position'] . '%'
            );
        }

        // Status Filter
        $statuses = $this->filters['status'] ?? ['Open'];

        $jobs->whereIn('status', (array) $statuses);

        return $jobs->latest()->get()->map(function ($job) {

            return [
                'Customer'   => $job->customer?->customer,
                'Position'   => $job->position,
                'Skill'      => $job->skill,
                'Experience' => $job->experience,
                'Status'     => $job->status,
                'Budget'     => $job->budget,
                'Location'   => $job->location,
                'Count'      => $job->count,
                'Remarks'    => $job->remarks,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Customer',
            'Position',
            'Skill',
            'Experience',
            'Status',
            'Budget',
            'Location',
            'Count',
            'Remarks',
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
