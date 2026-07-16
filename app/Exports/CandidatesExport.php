<?php

namespace App\Exports;

use App\Models\Candidate;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CandidatesExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $filters = $this->filters;

        $query = Candidate::with([
            'customer',
            'position',
            'creator',
            'updater'
        ]);

        // Candidate
        if (!empty($filters['candidate'])) {

            $query->where(
                'candidate_name',
                'LIKE',
                '%' . $filters['candidate'] . '%'
            );
        }

        // Skill
        if (!empty($filters['skill'])) {

            $skill = trim($filters['skill']);

            $query->whereRaw(
                "LOWER(skill) REGEXP ?",
                ['(^|[[:space:],-])' . strtolower($skill) . '([[:space:],-]|$)']
            );
        }

        // Mobile
        if (!empty($filters['mobile'])) {

            $query->where(
                'mobile',
                'LIKE',
                '%' . $filters['mobile'] . '%'
            );
        }

        // Email
        if (!empty($filters['email'])) {

            $query->where(
                'email',
                'LIKE',
                '%' . $filters['email'] . '%'
            );
        }

        // Status
        if (!empty($filters['status'])) {

            $query->whereIn(
                'status',
                (array) $filters['status']
            );
        }

        // Notice Period
        if (!empty($filters['notice_period'])) {

            $query->whereIn(
                'notice_period',
                (array) $filters['notice_period']
            );
        }

        // Created By
        if (!empty($filters['created_by'])) {

            $query->whereIn(
                'created_by',
                (array) $filters['created_by']
            );
        }

        // Date Filter
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {

            $query->whereBetween('created_at', [
                Carbon::parse($filters['from_date'])->startOfDay(),
                Carbon::parse($filters['to_date'])->endOfDay(),
            ]);
        } else {

            $query->whereBetween('created_at', [
                now()->startOfDay(),
                now()->endOfDay(),
            ]);
        }

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($candidate) {

                return [

                    'Customer'             => $candidate->customer?->customer,
                    'Job Position'         => $candidate->position?->position,

                    'Candidate Name'       => $candidate->candidate_name,
                    'Mobile'               => $candidate->mobile,
                    'Email'                => $candidate->email,
                    'Gender'               => $candidate->gender,

                    'Current Company'      => $candidate->current_company,
                    'Skills'               => $candidate->skill,

                    'Notice Period'        => $candidate->notice_period,

                    'Last Working Day'     => $candidate->last_working_day
                        ? Carbon::parse($candidate->last_working_day)->format('d-m-Y')
                        : '',

                    'Total Experience'     => $candidate->experience,
                    'Relevant Experience'  => $candidate->relevant_experience,

                    'Current Location'     => $candidate->current_location,
                    'Preferred Location'   => $candidate->preferred_location,

                    'Current Fixed CTC'    => $candidate->current_fixed_ctc,
                    'Current Variable CTC' => $candidate->current_variable_ctc,
                    'Expected CTC'         => $candidate->expected_ctc,

                    'Status'               => $candidate->status,

                    'Interview Date'       => $candidate->interview_date
                        ? Carbon::parse($candidate->interview_date)->format('d-m-Y h:i A')
                        : '',

                    'Interview Level'      => $candidate->interview_level,

                    'Resume Path'          => $candidate->resume_path,

                    'Education'            => $candidate->education,

                    'Created By'           => $candidate->creator?->name,

                    'Created At'           => optional($candidate->created_at)
                        ->format('d-m-Y h:i A'),

                    'Updated At'           => optional($candidate->updated_at)
                        ->format('d-m-Y h:i A'),
                ];
            });
    }

    public function headings(): array
    {
        return [

            'Customer',
            'Job Position',

            'Candidate Name',
            'Mobile',
            'Email',
            'Gender',

            'Current Company',
            'Skills',

            'Notice Period',
            'Last Working Day',

            'Total Experience',
            'Relevant Experience',

            'Current Location',
            'Preferred Location',

            'Current Fixed CTC',
            'Current Variable CTC',
            'Expected CTC',

            'Status',

            'Interview Date',
            'Interview Level',

            'Resume Path',

            'Education',

            'Created By',

            'Created At',
            'Updated At',

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
