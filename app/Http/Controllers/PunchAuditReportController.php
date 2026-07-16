<?php

namespace App\Http\Controllers;

use App\Exports\PunchAuditReportExport;
use App\Models\Attendance;
use App\Models\User;
use App\Models\WorkingDayMinHour;
use App\Services\AttendanceCalculator;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PunchAuditReportController extends Controller
{
    protected AttendanceCalculator $calculator;

    public function __construct(AttendanceCalculator $calculator)
    {

        $this->calculator = $calculator;
    }

    public function index(Request $request)
    {
        dd(auth()->user()->getRoleNames());
        $employees = User::where('status', 'active')
            ->orderBy('name')
            ->get();

        $reports = $this->getPunchReport($request);

        return view(
            'attendance_reports.punch_report.index',
            compact(
                'reports',
                'employees'
            )
        );
    }

    public function export(Request $request)
    {
        return Excel::download(
            new PunchAuditReportExport(
                $this->getPunchReport($request)
            ),
            'Punch_Audit_Report.xlsx'
        );
    }

    /**
     * Generate punch report.
     */
    private function getPunchReport(Request $request)
    {
        $query = Attendance::with('user')
            ->whereIn('status', ['approved', 'auto_approved']);

        // Date Filters
        if ($request->filled('from_date')) {
            $query->whereDate('attendance_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('attendance_date', '<=', $request->to_date);
        }

        // Employee Filter
        if ($request->filled('employee')) {
            $query->where('user_id', $request->employee);
        }

        $attendances = $query
            ->orderBy('user_id')
            ->orderBy('attendance_date')
            ->orderBy('punch_at')
            ->get();

        $reports = [];

        $groups = $attendances->groupBy(function ($attendance) {
            return $attendance->user_id . '_' . $attendance->attendance_date->format('Y-m-d');
        });

        $workingDays = WorkingDayMinHour::pluck('minimum_hours', 'day');

        foreach ($groups as $records) {

            $summary = $this->calculator->calculate($records);

            $first = $records->first();

            $day = $first->attendance_date->format('l');

            $minimumHours = (float) ($workingDays[$day] ?? 0);

            $workedHours = round($summary['minutes'] / 60, 2);

            $shortage = round($minimumHours - $workedHours, 2);

            $reports[] = [

                'user' => $first->user,

                'attendance_date' => $first->attendance_date,

                'day' => $day,

                'minimum_hours' => $minimumHours,

                'worked_hours_decimal' => $workedHours,

                'shortage' => $shortage,

                'first_in' => $summary['first_in'],

                'last_out' => $summary['last_out'],

                'working_minutes' => $summary['minutes'],

                'working_hours' => $summary['hours'],

                'sessions' => $summary['sessions'],

            ];
        }

        // dd($reports);
        return collect($reports);
    }
}
