<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Customer::with(['spocUser', 'backupSpoc']);

        // Customer Filter
        if (!empty($this->filters['customer_id'])) {

            $query->whereIn(
                'id',
                (array) $this->filters['customer_id']
            );
        }

        // Status Filter
        if (!empty($this->filters['status'])) {

            $query->whereIn(
                'status',
                (array) $this->filters['status']
            );
        } else {

            $query->where('status', 'Active');
        }

        // Domain Filter
        if (!empty($this->filters['domain'])) {

            $query->whereIn(
                'domain',
                (array) $this->filters['domain']
            );
        } else {

            $query->where('domain', 'IT');
        }

        // Created By Filter
        if (!empty($this->filters['created_by'])) {

            $query->whereIn(
                'created_by',
                (array) $this->filters['created_by']
            );
        }

        return $query->get()->map(function ($customer) {

            return [
                'customer'      => $customer->customer,
                'contact'       => $customer->contact,
                'email'         => $customer->email,
                'mobile'        => $customer->mobile,
                'status'        => $customer->status,
                'domain'        => $customer->domain,
                'spoc'          => $customer->spocUser?->name,
                'backup_spoc'   => $customer->backupSpoc?->name,
                'created_at'    => optional($customer->created_at)->format('d-M-Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Contact Person',
            'Email',
            'Mobile',
            'Status',
            'Domain',
            'SPOC',
            'Backup SPOC',
            'Created Date',
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
