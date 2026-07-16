<?php

namespace App\Exports;

use App\Models\Candidate;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SourcingReportExport implements FromCollection, WithHeadings, WithStyles
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

            /*
            |--------------------------------------------------------------------------
            | Daily Summary
            |--------------------------------------------------------------------------
            */
            case 'daily_summary':

                $query = Candidate::with([
                    'customer',
                    'customerJob',
                    'creator'
                ])
                    ->whereDate('created_at', today());

                if (!empty($this->ids)) {
                    $query->whereIn('id', $this->ids);
                }

                return $query->get()->map(function ($candidate) {

                    return [
                        'Recruiter'       => $candidate->creator?->name,
                        'Customer Name'   => $candidate->customer?->customer,
                        'Job'             => $candidate->customerJob?->position,
                        'Skill'           => $candidate->customerJob?->skill,
                        'Candidate Name'  => $candidate->candidate_name,
                        'Mobile'          => $candidate->mobile,
                        'Email'           => $candidate->email,
                        'Notice Period'   => $candidate->notice_period,
                        'Status'          => $candidate->status,
                        'Interview Date'  => $candidate->interview_date,
                        'Interview Level' => $candidate->interview_level,
                    ];
                });

                /*
            |--------------------------------------------------------------------------
            | Interview Schedule
            |--------------------------------------------------------------------------
            */
            case 'interview_schedule':

                $query = Candidate::with([
                    'customer',
                    'customerJob',
                    'creator'
                ])
                    ->where('status', 'Under Interview');

                if (!empty($this->ids)) {
                    $query->whereIn('id', $this->ids);
                }

                return $query
                    ->orderBy('interview_date')
                    ->get()
                    ->map(function ($candidate) {

                        return [
                            'Recruiter'       => $candidate->creator?->name,
                            'Customer Name'   => $candidate->customer?->customer,
                            'Job'             => $candidate->customerJob?->position,
                            'Skill'           => $candidate->customerJob?->skill,
                            'Candidate Name'  => $candidate->candidate_name,
                            'Mobile'          => $candidate->mobile,
                            'Email'           => $candidate->email,
                            'Notice Period'   => $candidate->notice_period,
                            'Status'          => $candidate->status,
                            'Interview Date'  => $candidate->interview_date,
                            'Interview Level' => $candidate->interview_level,
                        ];
                    });

                /*
            |--------------------------------------------------------------------------
            | Closures
            |--------------------------------------------------------------------------
            */
            case 'closures':

                $query = Candidate::with([
                    'customer',
                    'customerJob',
                    'creator'
                ])
                    ->where('status', 'Joined');

                if (!empty($this->ids)) {
                    $query->whereIn('id', $this->ids);
                }

                return $query
                    ->latest()
                    ->get()
                    ->map(function ($candidate) {

                        return [
                            'Month'          => $candidate->created_at->format('M Y'),
                            'Recruiter'      => $candidate->creator?->name,
                            'Customer Name'  => $candidate->customer?->customer,
                            'Job'            => $candidate->customerJob?->position,
                            'Skill'          => $candidate->customerJob?->skill,
                            'Candidate Name' => $candidate->candidate_name,
                            'Mobile'         => $candidate->mobile,
                            'Email'          => $candidate->email,
                            'Status'         => $candidate->status,
                            'Create Date'    => $candidate->created_at->format('d-m-Y'),
                        ];
                    });

                /*
            |--------------------------------------------------------------------------
            | Customer Status
            |--------------------------------------------------------------------------
            */
            case 'customer_status':

                $rows = collect();

                $query = Customer::with('jobs.candidates');

                if (!empty($this->ids)) {
                    $query->whereIn('id', $this->ids);
                }

                $query->get()
                    ->each(function ($customer) use ($rows) {

                        foreach ($customer->jobs as $job) {

                            $rows->push([
                                'Customer Name' => $customer->customer,
                                'Job' => $job->position,
                                'Skill' => $job->skill,

                                'Joined Count' => $job->candidates
                                    ->where('status', 'Joined')
                                    ->count(),

                                'Under Discussion Count' => $job->candidates
                                    ->where('status', 'Under Discussion')
                                    ->count(),

                                'Shared With Customer Count' => $job->candidates
                                    ->where('status', 'Shared with Customer')
                                    ->count(),

                                'Under Interview Count' => $job->candidates
                                    ->where('status', 'Under Interview')
                                    ->count(),
                            ]);
                        }
                    });

                return $rows;

            default:
                return collect();
        }
    }

    public function headings(): array
    {
        switch ($this->type) {

            case 'daily_summary':
            case 'interview_schedule':
                return [
                    'Recruiter',
                    'Customer Name',
                    'Job',
                    'Skill',
                    'Candidate Name',
                    'Mobile',
                    'Email',
                    'Notice Period',
                    'Status',
                    'Interview Date',
                    'Interview Level',
                ];

            case 'closures':
                return [
                    'Month',
                    'Recruiter',
                    'Customer Name',
                    'Job',
                    'Skill',
                    'Candidate Name',
                    'Mobile',
                    'Email',
                    'Status',
                    'Create Date',
                ];

            case 'customer_status':
                return [
                    'Customer Name',
                    'Job',
                    'Skill',
                    'Joined Count',
                    'Under Discussion Count',
                    'Shared With Customer Count',
                ];

            default:
                return [
                    'Recruiter',
                    'Customer Name',
                    'Job',
                    'Skill',
                    'Candidate Name',
                    'Mobile',
                    'Email',
                    'Notice Period',
                    'Status',
                    'Interview Date',
                    'Interview Level',
                ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size'  => 12,
                ],
                'fill' => [
                    'fillType'   => 'solid',
                    'startColor' => ['rgb' => '198754'],
                ],
            ],
        ];
    }
}
