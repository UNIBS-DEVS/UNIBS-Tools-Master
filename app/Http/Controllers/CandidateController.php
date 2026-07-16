<?php

namespace App\Http\Controllers;

use App\Exports\CandidatesExport;
use App\Models\Candidate;
use App\Models\Customer;
use App\Models\CustomerJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\CandidateJobMappingRemark;

class CandidateController extends Controller
{
    public function filter(Request $request)
    {
        $filters = $request->all();

        $filters['searched'] = true;

        session([
            'candidate_filters' => $filters
        ]);

        return redirect()->route('candidates.index');
    }

    public function index(Request $request)
    {
        $filters = session('candidate_filters', []);

        // Dropdown data
        $customerOptions = Customer::orderBy('customer')->get();

        $createdByOptions = User::whereJsonContains('roles', 'sourcing')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | First page load = No data
        |--------------------------------------------------------------------------
        */
        if (!isset($filters['searched'])) {

            $candidates = Candidate::whereRaw('1 = 0')
                ->paginate(40);

            return view('candidates.index', compact(
                'candidates',
                'filters',
                'createdByOptions',
                'customerOptions'
            ));
        }

        // Main Query
        $query = Candidate::with([
            'customer',
            'position',
            'creator',
            'updater',
            'latestRemark'
        ]);

        // Customer
        if (!empty($filters['customer_id']) && is_array($filters['customer_id'])) {

            $query->whereIn('customer_id', $filters['customer_id']);
        }

        // Candidate
        if (!empty($filters['candidate'])) {

            $query->where(
                'candidate_name',
                'LIKE',
                '%' . trim($filters['candidate']) . '%'
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
                '%' . trim($filters['mobile']) . '%'
            );
        }

        // Email
        if (!empty($filters['email'])) {

            $query->where(
                'email',
                'LIKE',
                '%' . trim($filters['email']) . '%'
            );
        }

        // Status
        if (!empty($filters['status']) && is_array($filters['status'])) {

            $query->whereIn(
                'status',
                $filters['status']
            );
        }

        // Notice Period
        if (!empty($filters['notice_period']) && is_array($filters['notice_period'])) {

            $query->whereIn(
                'notice_period',
                $filters['notice_period']
            );
        }

        // Created By
        if (!empty($filters['created_by']) && is_array($filters['created_by'])) {

            $query->whereIn(
                'created_by',
                $filters['created_by']
            );
        }

        // Date Range
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {

            $query->whereBetween('updated_at', [
                Carbon::parse($filters['from_date'])->startOfDay(),
                Carbon::parse($filters['to_date'])->endOfDay(),
            ]);
        }

        $candidates = $query
            ->orderByDesc('created_at')
            ->paginate(40);

        return view('candidates.index', compact(
            'candidates',
            'filters',
            'createdByOptions',
            'customerOptions'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::where('status', 'Active')
            ->orderBy('customer')
            ->get();

        $positions = CustomerJob::where('status', 'Open')
            ->orderBy('position')
            ->get();

        $recruiters = User::whereJsonContains('roles', 'sourcing')
            ->orderBy('name')
            ->get();

        return view('candidates.create', compact(
            'customers',
            'positions',
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'customer_id' => 'required|exists:customers,id',
            'customer_job_id' => 'required|exists:customer_jobs,id',

            'candidate_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',

            'gender' => 'required|in:Male,Female,Other',

            'current_company' => 'nullable|string|max:255',
            'skill' => 'nullable|string|max:255',

            'notice_period' => 'nullable|string|max:100',
            'last_working_day' => 'nullable|date',

            'experience_years' => 'required|integer|min:0|max:35',
            'experience_months' => 'required|integer|min:0|max:11',

            'relevant_experience_years' => 'required|integer|min:0|max:35',
            'relevant_experience_months' => 'required|integer|min:0|max:11',

            'current_location' => 'nullable|string|max:255',
            'preferred_location' => 'nullable|string|max:255',

            'current_fixed_ctc' => 'nullable|numeric|min:0',
            'current_variable_ctc' => 'nullable|numeric|min:0',
            'expected_ctc' => 'nullable|numeric|min:0',

            'status' => 'nullable|string|max:100',

            'interview_date' => 'nullable|date',

            'interview_level' => 'nullable|in:L1,L2,Manager,C Level,HR',

            'resume_path' => 'nullable|string',

            'education' => 'nullable|string',

            'remark_type' => 'nullable|in:Candidate Update,Candidate Issue,Customer Update,Customer Issue,Interview Update,Offer Update,Delay,Escalation,Internal Note',
        ],   [
            '*.required' => 'This field is required.',
        ]);

        $validated['current_fixed_ctc'] = $request->current_fixed_ctc !== null
            ? number_format($request->current_fixed_ctc, 2, '.', '')
            : null;

        $validated['current_variable_ctc'] = $request->current_variable_ctc !== null
            ? number_format($request->current_variable_ctc, 2, '.', '')
            : null;

        $validated['expected_ctc'] = $request->expected_ctc !== null
            ? number_format($request->expected_ctc, 2, '.', '')
            : null;

        // Created by
        $validated['created_by'] = Auth::id();

        // Updated by
        $validated['updated_by'] = Auth::id();

        $candidate = Candidate::create($validated);

        if (!empty(trim($request->remarks))) {

            CandidateJobMappingRemark::create([
                'candidate_id' => $candidate->id,
                'remark_type'  => $request->remark_type,
                'remarks'      => trim($request->remarks),
                'created_by'   => Auth::id(),
                'updated_by'   => Auth::id(),
            ]);
        }

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidate created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidate $candidate)
    {
        // dd($candidate);
        return view('candidates.show', compact('candidate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Candidate $candidate)
    {

        $candidate->load([
            'remarkHistories.creator'
        ]);

        $customers = Customer::where('status', 'Active')->get();

        $positions = CustomerJob::where('status', 'Open')->get();

        return view('candidates.edit', compact(
            'candidate',
            'customers',
            'positions',
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'customer_id'            => 'required|exists:customers,id',
            'customer_job_id'        => 'required|exists:customer_jobs,id',

            'candidate_name'         => 'required|string|max:255',
            'mobile'                 => 'required|string|max:20',
            'email'                  => 'nullable|email',

            'gender'                 => 'nullable',

            'current_company'        => 'nullable|string|max:255',
            'skill'                  => 'nullable|string|max:255',

            'notice_period'          => 'nullable',
            'last_working_day'       => 'nullable|date',

            'experience_years' => 'required|integer|min:0|max:35',
            'experience_months' => 'required|integer|min:0|max:11',

            'relevant_experience_years' => 'required|integer|min:0|max:35',
            'relevant_experience_months' => 'required|integer|min:0|max:11',

            'current_location'       => 'nullable|string|max:255',
            'preferred_location'     => 'nullable|string|max:255',

            'current_fixed_ctc'      => 'nullable|numeric|min:0',
            'current_variable_ctc'   => 'nullable|numeric|min:0',
            'expected_ctc'           => 'nullable|numeric|min:0',

            'status'                 => 'required',

            'interview_date'         => 'nullable|date',

            'interview_level'        => 'nullable',

            'resume_path'            => 'nullable|string',

            'education' => 'nullable|string',

            'remark_type' => 'nullable|in:Candidate Update,Candidate Issue,Customer Update,Customer Issue,Interview Update,Offer Update,Delay,Escalation,Internal Note',
        ],   [
            '*.required' => 'This field is required.',
        ]);

        $validated['current_fixed_ctc'] = $request->current_fixed_ctc !== null
            ? number_format($request->current_fixed_ctc, 2, '.', '')
            : null;

        $validated['current_variable_ctc'] = $request->current_variable_ctc !== null
            ? number_format($request->current_variable_ctc, 2, '.', '')
            : null;

        $validated['expected_ctc'] = $request->expected_ctc !== null
            ? number_format($request->expected_ctc, 2, '.', '')
            : null;

        $validated['updated_by'] = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Remarks are stored in history table only
        |--------------------------------------------------------------------------
        */
        unset($validated['remarks']);

        $candidate->update($validated);

        /*
        |--------------------------------------------------------------------------
        | Save remark history
        |--------------------------------------------------------------------------
        */
        if (!empty(trim($request->remarks))) {

            CandidateJobMappingRemark::create([
                'candidate_id' => $candidate->id,
                'remark_type'  => $request->remark_type,
                'remarks'      => trim($request->remarks),
                'created_by'   => Auth::id(),
                'updated_by'   => Auth::id(),
            ]);
        }

        // return redirect()
        //     ->back()
        //     ->with('success', 'Candidate updated successfully.');

        return redirect()
            ->route('candidates.edit', [
                'candidate' => $candidate->id,
                'page' => $request->page,
            ])
            ->with('success', 'Candidate updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidate $candidate)
    {

        if (!auth()->user()->hasRole('admin')) {

            abort(403, 'Unauthorized');
        }

        $candidate->delete();

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidate deleted successfully.');
    }

    public function export()
    {
        return Excel::download(
            new CandidatesExport(
                session('candidate_filters', [])
            ),
            'Candidate_Job_Mapping.xlsx'
        );
    }
}
