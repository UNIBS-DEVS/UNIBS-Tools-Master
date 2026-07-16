<?php

namespace App\Http\Controllers;

use App\Exports\SourcingReportExport;
use App\Mail\SourcingReportMail;
use App\Models\Candidate;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SourcingReportController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {

            abort(403, 'Unauthorized');
        }

        $type = $request->report_type ?? 'daily_summary';

        $records = collect();

        switch ($type) {
            /*
            |--------------------------------------------------------------------------
            | Daily Summary
            |--------------------------------------------------------------------------
            */
            case 'daily_summary':

                $records = Candidate::with([
                    'customer',
                    'customerJob',
                    'creator'
                ])
                    ->where(function ($query) {
                        $query->whereDate('created_at', today())
                            ->orWhereDate('updated_at', today());
                    })
                    ->orderByDesc('created_at')
                    ->orderByDesc('updated_at')
                    ->get();

                break;

            /*
            |--------------------------------------------------------------------------
            | Interview Schedule
            |--------------------------------------------------------------------------
            */
            case 'interview_schedule':

                $records = Candidate::with([
                    'customer',
                    'customerJob',
                    'creator'
                ])
                    ->where('status', 'Under Interview')
                    ->orderBy('interview_date')
                    ->get();

                break;

            /*
            |--------------------------------------------------------------------------
            | Closures
            |--------------------------------------------------------------------------
            */
            case 'closures':

                $records = Candidate::with([
                    'customer',
                    'customerJob',
                    'creator'
                ])
                    ->where('status', 'Joined')
                    ->latest()
                    ->get();

                break;

            /*
            |--------------------------------------------------------------------------
            | Customer Status
            |--------------------------------------------------------------------------
            */
            case 'customer_status':

                $records = Customer::with(['jobs.candidates'])
                    ->get()
                    ->map(function ($customer) {

                        foreach ($customer->jobs as $job) {

                            $job->joined_count =
                                $job->candidates
                                ->where('status', 'Joined')
                                ->count();

                            $job->under_discussion_count =
                                $job->candidates
                                ->where('status', 'Under Discussion')
                                ->count();

                            $job->shared_count =
                                $job->candidates
                                ->where('status', 'Shared with Customer')
                                ->count();

                            $job->under_interview_count =
                                $job->candidates
                                ->where('status', 'Under Interview')
                                ->count();
                        }

                        return $customer;
                    });

                break;
        }

        // dd($records, $type);
        return view('reports.sourcing.index', compact(
            'type',
            'records'
        ));
    }

    public function export(Request $request)
    {

        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        $type = $request->report_type ?? 'daily_summary';

        $visibleIds = [];

        if ($request->filled('visible_ids')) {
            $visibleIds = array_filter(
                explode(',', $request->visible_ids)
            );
        }
 
        $fileName =
            'sourcing_report_' .
            $type .
            '_' .
            now()->format('Ymd_His') .
            '.xlsx';

        Excel::store(
            new SourcingReportExport(
                $type,
                $visibleIds
            ),
            $fileName,
            'public'
        );

        Mail::to(auth()->user()->email)
            ->send(
                new SourcingReportMail(
                    $fileName,
                    $type
                )
            );

        return Excel::download(
            new SourcingReportExport(
                $type,
                $visibleIds
            ),
            $fileName
        );
    }
}
