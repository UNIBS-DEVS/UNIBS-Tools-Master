<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateJobMappingRemark;
use App\Models\CjmChange;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuditController extends Controller
{
    public function filter(Request $request)
    {
        $filters = $request->all();

        $filters['searched'] = true;

        session([
            'audit_filters' => $filters
        ]);

        return redirect()->route('audit.index');
    }

    public function index(Request $request)
    {
        $filters = session('audit_filters', []);

        $customerOptions = Customer::where('status', 'active')
            ->orderBy('customer')
            ->get();


        $createdByOptions = User::whereJsonContains('roles', 'sourcing')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | First Page Load = No Data
        |--------------------------------------------------------------------------
        */


        if (!isset($filters['searched'])) {

            $changes = CjmChange::whereRaw('1 = 0')
                ->paginate(20);

            $remarks = CandidateJobMappingRemark::whereRaw('1 = 0')
                ->paginate(20, ['*'], 'remarks_page');
 
            return view('audits.index', compact(
                'changes',
                'remarks',
                'filters',
                'createdByOptions',
                'customerOptions'
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Changes
        |--------------------------------------------------------------------------
        */
        $query = CjmChange::with([
            'candidate.customer',
            'candidate.position',
            'creator',
        ]);

        // Customer
        if (!empty($filters['customer_id']) && is_array($filters['customer_id'])) {

            $query->whereHas('candidate', function ($q) use ($filters) {
                $q->whereIn('customer_id', $filters['customer_id']);
            });
        }

        // Candidate
        if (!empty($filters['candidate'])) {

            $query->whereHas('candidate', function ($q) use ($filters) {
                $q->where(
                    'candidate_name',
                    'LIKE',
                    '%' . trim($filters['candidate']) . '%'
                );
            });
        }

        // Skill
        if (!empty($filters['skill'])) {

            $query->whereHas('candidate', function ($q) use ($filters) {

                $skill = trim($filters['skill']);

                $q->whereRaw(
                    "LOWER(skill) REGEXP ?",
                    ['(^|[[:space:],-])' . strtolower($skill) . '([[:space:],-]|$)']
                );
            });
        }

        // Job
        if (!empty($filters['job'])) {

            $query->whereHas('candidate.position', function ($q) use ($filters) {

                $q->where(
                    'position',
                    'LIKE',
                    '%' . trim($filters['job']) . '%'
                );
            });
        }

        // Recruiter
        if (!empty($filters['created_by']) && is_array($filters['created_by'])) {

            $query->whereIn(
                'created_by',
                $filters['created_by']
            );
        }

        // Date Range
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {

            $query->whereBetween('created_at', [
                Carbon::parse($filters['from_date'])->startOfDay(),
                Carbon::parse($filters['to_date'])->endOfDay(),
            ]);
        }

        $changes = $query
            ->latest()
            ->paginate(20);

        /*
        |--------------------------------------------------------------------------
        | Remarks History
        |--------------------------------------------------------------------------
        */
        $remarksQuery = CandidateJobMappingRemark::with([
            'candidate.customer',
            'candidate.position',
            'creator',
        ]);

        // Same filters for remarks
        if (!empty($filters['customer_id']) && is_array($filters['customer_id'])) {

            $remarksQuery->whereHas('candidate', function ($q) use ($filters) {
                $q->whereIn('customer_id', $filters['customer_id']);
            });
        }

        if (!empty($filters['candidate'])) {

            $remarksQuery->whereHas('candidate', function ($q) use ($filters) {
                $q->where(
                    'candidate_name',
                    'LIKE',
                    '%' . trim($filters['candidate']) . '%'
                );
            });
        }

        if (!empty($filters['job'])) {

            $remarksQuery->whereHas('candidate.position', function ($q) use ($filters) {
                $q->where(
                    'position',
                    'LIKE',
                    '%' . trim($filters['job']) . '%'
                );
            });
        }

        if (!empty($filters['created_by']) && is_array($filters['created_by'])) {

            $remarksQuery->whereIn(
                'created_by',
                $filters['created_by']
            );
        }

        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {

            $remarksQuery->whereBetween('created_at', [
                Carbon::parse($filters['from_date'])->startOfDay(),
                Carbon::parse($filters['to_date'])->endOfDay(),
            ]);
        }



        $changes = $query
            ->latest()
            ->paginate(10, ['*'], 'changes_page');

        $remarks = $remarksQuery
            ->latest()
            ->paginate(10, ['*'], 'remarks_page');

        return view('audits.index', compact(
            'changes',
            'remarks',
            'filters',
            'createdByOptions',
            'customerOptions'
        ));
    }


    public function export() {}
}
