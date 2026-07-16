<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\Tender;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport implements FromCollection, WithHeadings, WithStyles
{
    protected $type;
    protected $ids;

    public function __construct($type, $ids = [])
    {
        $this->type = $type;
        $this->ids = $ids;
    }

    public function collection()
    {
        switch ($this->type) {

            case 'daily_summary':

                $query = Sale::with('creator')
                    ->whereDate('created_at', today());

                if (!empty($this->ids)) {
                    $query->whereIn('id', $this->ids);
                }

                return $query->get()->map(function ($sale) {
                    return [
                        'Sales Associate' => $sale->creator?->name,
                        'Client Contact' => $sale->client_contact,
                        'Company' => $sale->company,
                        'Mobile' => $sale->mobile,
                        'Email' => $sale->email,
                        'Status' => $sale->status,
                        'Requirement' => $sale->requirement,
                        'Type' => $sale->type,
                        'Source' => $sale->source,
                        'Follow Up Date' => $sale->follow_up_date,
                    ];
                });

            case 'follow_up':

                $query = Sale::with('creator')
                    ->whereNotNull('follow_up_date');

                if (!empty($this->ids)) {
                    $query->whereIn('id', $this->ids);
                }

                return $query->get()->map(function ($sale) {
                    return [
                        'Sales Associate' => $sale->creator?->name,
                        'Client Contact' => $sale->client_contact,
                        'Company' => $sale->company,
                        'Mobile' => $sale->mobile,
                        'Email' => $sale->email,
                        'Status' => $sale->status,
                        'Requirement' => $sale->requirement,
                        'Type' => $sale->type,
                        'Source' => $sale->source,
                        'Follow Up Date' => $sale->follow_up_date,
                    ];
                });

            case 'closures':

                $query = Sale::with('creator')
                    ->where('status', 'Won');

                if (!empty($this->ids)) {
                    $query->whereIn('id', $this->ids);
                }

                return $query->get()->map(function ($sale) {
                    return [
                        'Month' => $sale->created_at->format('M Y'),
                        'Sales Associate' => $sale->creator?->name,
                        'Client Contact' => $sale->client_contact,
                        'Company' => $sale->company,
                        'Email' => $sale->email,
                        'Mobile' => $sale->mobile,
                        'Requirement' => $sale->requirement,
                        'Type' => $sale->type,
                        'Source' => $sale->source,
                        'Status' => $sale->status,
                        'Follow Up Date' => $sale->follow_up_date,
                        'Create Date' => $sale->created_at->format('d-m-Y'),
                    ];
                });

            case 'licence_report':

                $query = Sale::with('creator')
                    ->whereIn('type', [
                        'Microsoft',
                        'Zoho',
                        'Tally',
                        'Google'
                    ]);

                if (!empty($this->ids)) {
                    $query->whereIn('id', $this->ids);
                }

                return $query->get()->map(function ($sale) {
                    return [
                        'Month' => $sale->created_at->format('M Y'),
                        'Sales Associate' => $sale->creator?->name,
                        'Client Contact' => $sale->client_contact,
                        'Company' => $sale->company,
                        'Email' => $sale->email,
                        'Mobile' => $sale->mobile,
                        'Requirement' => $sale->requirement,
                        'Type' => $sale->type,
                        'Source' => $sale->source,
                        'Status' => $sale->status,
                        'Follow Up Date' => $sale->follow_up_date,
                        'Create Date' => $sale->created_at->format('d-m-Y'),
                    ];
                });

            case 'weekly_summary':

                return Sale::selectRaw('source, type, COUNT(*) as total')
                    ->whereIn('source', ['IndiaMart', 'Justdial'])
                    ->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])
                    ->groupBy('source', 'type')
                    ->get()
                    ->map(function ($sale) {
                        return [
                            'Source' => $sale->source,
                            'Type' => $sale->type,
                            'Count' => $sale->total,
                        ];
                    });

            case 'tender_report':

                $query = Tender::with([
                    'primaryUser',
                    'secondaryUser',
                ]);

                if (!empty($this->ids)) {
                    $query->whereIn('id', $this->ids);
                }

                return $query->get()->map(function ($tender) {
                    return [
                        'Month' => $tender->created_at->format('M Y'),
                        'Tender Number' => $tender->tender_num,
                        'Primary User' => $tender->primaryUser?->name,
                        'Secondary User' => $tender->secondaryUser?->name,
                        'Submission Date' => $tender->submission_date
                            ? $tender->submission_date->format('j M, Y')
                            : '-',
                        'Type' => $tender->type,
                        'Status' => $tender->status,
                        'Due Date' => $tender->due_date
                            ? $tender->due_date->format('j M, Y')
                            : '-',
                        'Estimated Value' => $tender->estimated_value,
                        'State' => $tender->state,
                        'Department' => $tender->department,
                        'Bid Price' => $tender->bid_price,
                        'Platform' => $tender->platform,
                        'Create Date' => $tender->created_at->format('j M, Y h:i A'),
                    ];
                });

            default:
                return collect();
        }
    }

    public function headings(): array
    {
        switch ($this->type) {

            case 'weekly_summary':

                return [
                    'Source',
                    'Type',
                    'Count'
                ];

            case 'closures':
            case 'licence_report':

                return [
                    'Month',
                    'Sales Associate',
                    'Client Contact',
                    'Company',
                    'Email',
                    'Mobile',
                    'Requirement',
                    'Type',
                    'Source',
                    'Status',
                    'Follow Up Date',
                    'Create Date',
                ];

            case 'tender_report':

                return [
                    'Month',
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
                    'Create Date',
                ];

            default:

                return [
                    'Sales Associate',
                    'Client Contact',
                    'Company',
                    'Mobile',
                    'Email',
                    'Status',
                    'Requirement',
                    'Type',
                    'Source',
                    'Follow Up Date',
                ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => [
                        'rgb' => '198754'
                    ]
                ]
            ]
        ];
    }
}
