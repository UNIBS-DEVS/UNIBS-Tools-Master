<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Mail\SalesReportMail;
use App\Models\Sale;
use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->all();

        session([
            'report_filters' => $filters
        ]);

        if (!auth()->user()->hasRole(['admin', 'sales'])) {
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

                $records = Sale::with('creator')
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
            | Weekly Summary
            |--------------------------------------------------------------------------
            */
            case 'weekly_summary':

                $records = Sale::selectRaw('source, type, COUNT(*) as total')
                    ->whereIn('source', ['IndiaMart', 'Justdial'])
                    ->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])
                    ->groupBy('source', 'type')
                    ->orderBy('source')
                    ->get();

                break;

            /*
            |--------------------------------------------------------------------------
            | Follow Up
            |--------------------------------------------------------------------------
            */
            case 'follow_up':

                $records = Sale::with('creator')
                    ->whereNotNull('follow_up_date')
                    ->orderBy('follow_up_date')
                    ->get();

                break;

            /*
            |--------------------------------------------------------------------------
            | Closures
            |--------------------------------------------------------------------------
            */
            case 'closures':

                $records = Sale::with('creator')
                    ->where('status', 'Won')
                    ->latest()
                    ->get();

                break;

            /*
            |--------------------------------------------------------------------------
            | Licence Report
            |--------------------------------------------------------------------------
            */
            case 'licence_report':

                $records = Sale::with('creator')
                    ->whereIn('type', [
                        'Microsoft',
                        'Zoho',
                        'Tally',
                        'Google'
                    ])
                    ->latest()
                    ->get();

                break;

            /*
            |--------------------------------------------------------------------------
            | Tender Report
            |--------------------------------------------------------------------------
            */
            case 'tender_report':

                $records = Tender::with([
                    'primaryUser',
                    'secondaryUser',
                ])
                    ->latest()
                    ->get();

                break;
        }

        return view(
            'reports.sales.index',
            compact(
                'type',
                'records'
            )
        );
    }

    public function export(Request $request)
    {
        if (!auth()->user()->hasRole(['admin', 'sales'])) {
            abort(403, 'Unauthorized');
        }

        $type = $request->report_type ?? 'daily_summary';

        $visibleIds = [];

        if ($request->filled('visible_ids')) {
            $visibleIds = explode(',', $request->visible_ids);
        }

        $fileName =
            'sales_report_' .
            $type .
            '_' .
            now()->format('Ymd_His') .
            '.xlsx';

        // Store Excel
        Excel::store(
            new SalesReportExport($type, $visibleIds),
            $fileName,
            'public'
        );

        // Email Excel
        Mail::to(auth()->user()->email)
            ->send(
                new SalesReportMail($fileName, $type)
            );

        // Download Excel
        return Excel::download(
            new SalesReportExport($type, $visibleIds),
            $fileName
        );
    }
}
