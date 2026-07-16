<?php

namespace App\Http\Controllers;

use App\Exports\CustomerJobsExport;
use App\Models\Customer;
use App\Models\CustomerJob;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CustomerJobController extends Controller
{
    public function filter(Request $request)
    {
        session([
            'customer_job_filters' => $request->all()
        ]);

        return redirect()->route('customer-jobs.index');
    }

    public function index(Request $request)
    {
        // SESSION FILTERS
        $filters = session('customer_job_filters', []);

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        | If customer_id comes from customer page
        | then override session filters
        */
        if ($request->filled('customer_id')) {

            $filters['customer_id'] = (array) $request->customer_id;
        }

        $jobs = CustomerJob::with('customer');

        // Customer Filter
        if (!empty($filters['customer_id'])) {

            $jobs->whereIn(
                'customer_id',
                $filters['customer_id']
            );
        }

        // Skill Filter
        if (!empty($filters['skill'])) {

            $skill = trim($filters['skill']);

            $jobs->whereRaw(
                "LOWER(skill) REGEXP ?",
                ['(^|[[:space:],-])' . strtolower($skill) . '([[:space:],-]|$)']
            );
        }

        // Position Filter
        if (!empty($filters['job_position'])) {

            $job_position = trim($filters['job_position']);

            $jobs->whereRaw(
                "LOWER(position) REGEXP ?",
                ['(^|[[:space:],-])' . strtolower($job_position) . '([[:space:],-]|$)']
            );
        }

        // Status Filter
        $statuses = $filters['status'] ?? ['Open'];

        $jobs->whereIn('status', $statuses);

        $jobs = $jobs
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $jobPositions = CustomerJob::select('position')
            ->distinct()
            ->orderBy('position')
            ->pluck('position');

        $customers = Customer::where('status', 'Active')
            ->orderBy('customer')
            ->get();

        return view('customer_jobs.index', compact(
            'jobs',
            'customers',
            'jobPositions',
            'filters'
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

        return view('customer_jobs.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'position' => 'required|string|max:255',
            'skill' => 'required|string|max:255',
            'experience' => 'nullable|string|max:255',
            'status' => 'required|in:Open,Closed,On-Hold',
            'budget' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'count' => 'required|integer|min:1|max:500',
            'jd_path' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ],   [
            '*.required' => 'This field is required.',
        ]);

        CustomerJob::create($validated);

        return redirect()
            ->route('customer-jobs.index')
            ->with('success', 'Job created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerJob $customerJob)
    {
        return view('customer_jobs.show', compact('customerJob'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerJob $customerJob)
    {
        $customers = Customer::where('status', 'Active')
            ->orderBy('customer')
            ->get();

        return view('customer_jobs.edit', compact('customerJob', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerJob $customerJob)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'position' => 'required|string|max:255',
            'skill' => 'required|string|max:255',
            'experience' => 'nullable|string|max:255',
            'status' => 'required|in:Open,Closed,On-Hold',
            'budget' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'count' => 'required|integer|min:1|max:500',
            'jd_path' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ],   [
            '*.required' => 'This field is required.',
        ]);

        $customerJob->update($validated);

        return redirect()
            ->route('customer-jobs.index')
            ->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerJob $customerJob)
    {

        if (!auth()->user()->hasRole('admin')) {

            abort(403, 'Unauthorized');
        }

        $customerJob->delete();

        return redirect()
            ->route('customer-jobs.index')
            ->with('success', 'Job deleted successfully.');
    }

    // Export Excel
    public function export(Request $request)
    {
        return Excel::download(
            new CustomerJobsExport($request),
            'customer-jobs.xlsx'
        );
    }
}
