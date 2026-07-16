<?php

namespace App\Http\Controllers;

use App\Exports\TendersExport;
use App\Models\Tender;
use App\Models\TenderRemark;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class TenderController extends Controller
{
    public function filter(Request $request)
    {
        if (!auth()->user()->hasRole(['admin', 'tender executive'])) {

            abort(403, 'Unauthorized');
        }

        session([
            'tender_filters' => $request->all()
        ]);

        return redirect()->route('tenders.index');
    }

    public function index()
    {
        if (!auth()->user()->hasRole(['admin', 'tender executive'])) {

            abort(403, 'Unauthorized');
        }

        $filters = session('tender_filters', []);

        $query = Tender::with([
            'primaryUser',
            'secondaryUser',
            'creator',
            'updater',
        ]);

        $states = [
            'Andhra Pradesh',
            'Arunachal Pradesh',
            'Assam',
            'Bihar',
            'Chhattisgarh',
            'Delhi',
            'Goa',
            'Gujarat',
            'Haryana',
            'Himachal Pradesh',
            'Jharkhand',
            'Karnataka',
            'Kerala',
            'Madhya Pradesh',
            'Maharashtra',
            'Odisha',
            'Punjab',
            'Rajasthan',
            'Tamil Nadu',
            'Telangana',
            'Uttar Pradesh',
            'Uttarakhand',
            'West Bengal',
        ];

        if (!empty($filters['tender_num'])) {
            $query->where('tender_num', 'like', '%' . $filters['tender_num'] . '%');
        }

        if (!empty($filters['primary_user_id'])) {
            $query->where('primary_user_id', $filters['primary_user_id']);
        }

        if (!empty($filters['secondary_user_id'])) {
            $query->where('secondary_user_id', $filters['secondary_user_id']);
        }

        // if (!empty($filters['status'])) {
        //     $query->where('status', $filters['status']);
        // }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->where('status', 'Pending');
        }

        if (!empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        $tenders = $query
            ->latest()
            ->paginate(20);

        $tenderUsers = User::whereJsonContains('roles', 'tender executive')
            ->orderBy('name')
            ->get();


        return view('tenders.index', compact(
            'tenders',
            'filters',
            'tenderUsers',
            'states'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->hasRole(['admin', 'tender executive'])) {

            abort(403, 'Unauthorized');
        }

        $tenderUsers = User::whereJsonContains('roles', 'tender executive')
            ->orderBy('name')
            ->get();

        $states = $this->getStates();

        return view('tenders.create', compact(
            'tenderUsers',
            'states'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['admin', 'tender executive'])) {

            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([

            'tender_num' => 'required|string|max:255',
            'primary_user_id' => 'nullable|exists:users,id',
            'secondary_user_id' => 'nullable|exists:users,id',
            'submission_date' => 'nullable|date',
            'type' => 'required|in:IT Manpower,Non-IT Manpower,SAP,Trainings,IT Projects,Others',
            'status' => 'required|in:Pending,Submitted,Under Evaluation,Won,Lost',
            'due_date' => 'nullable|date',
            'estimated_value' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'bid_price' => 'nullable|string|max:255',
            'platform' => 'required|string',

        ], [
            '*.required' => 'This field is required.',
        ]);

        // Created by
        $validated['created_by'] = Auth::id();

        // Updated by
        $validated['updated_by'] = Auth::id();

        $tender = Tender::create($validated);

        if (!empty(trim($request->remarks))) {

            TenderRemark::create([
                'tender_id' => $tender->id,
                'remarks'      => trim($request->remarks),
                'created_by'   => Auth::id(),
                'updated_by'   => Auth::id(),
            ]);
        }

        return redirect()
            ->route('tenders.index')
            ->with('success', 'Sale created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!auth()->user()->hasRole(['admin', 'tender executive'])) {

            abort(403, 'Unauthorized');
        }

        $tender = Tender::with([
            'primaryUser',
            'secondaryUser'
        ])->findOrFail($id);

        return view('tenders.show', compact('tender'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (!auth()->user()->hasRole(['admin', 'tender executive'])) {

            abort(403, 'Unauthorized');
        }

        $tender = Tender::findOrFail($id);

        $tenderUsers = User::whereJsonContains('roles', 'tender executive')
            ->orderBy('name')
            ->get();

        $states = $this->getStates();

        return view('tenders.edit', compact(
            'tender',
            'tenderUsers',
            'states'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tender $tender)
    {
        if (!auth()->user()->hasRole(['admin', 'tender executive'])) {

            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'tender_num' => 'required|string|max:255',
            'primary_user_id' => 'nullable|exists:users,id',
            'secondary_user_id' => 'nullable|exists:users,id',
            'submission_date' => 'nullable|date',
            'type' => 'required|string',
            'status' => 'required|string',
            'due_date' => 'nullable|date',
            'estimated_value' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'bid_price' => 'nullable|string|max:255',
            'platform' => 'required|string',
        ], [
            '*.required' => 'This field is required.',
        ]);

        $validated['updated_by'] = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Remarks are stored in history table only
        |--------------------------------------------------------------------------
        */
        unset($validated['remarks']);

        $tender->update($validated);

        if (!empty(trim($request->remarks))) {

            TenderRemark::create([
                'tender_id' => $tender->id,
                'remarks'      => trim($request->remarks),
                'created_by'   => Auth::id(),
                'updated_by'   => Auth::id(),
            ]);
        }

        return redirect()
            ->route('tenders.index')
            ->with('success', 'Sale updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->hasRole(['admin', 'tender executive'])) {

            abort(403, 'Unauthorized');
        }

        $tender = Tender::findOrFail($id);

        $tender->delete();

        return redirect()
            ->route('tenders.index')
            ->with('success', 'Tender deleted successfully.');
    }

    private function getStates()
    {
        if (!auth()->user()->hasRole(['admin', 'tender executive'])) {

            abort(403, 'Unauthorized');
        }

        return [
            'Andhra Pradesh',
            'Arunachal Pradesh',
            'Assam',
            'Bihar',
            'Chhattisgarh',
            'Delhi',
            'Goa',
            'Gujarat',
            'Haryana',
            'Himachal Pradesh',
            'Jharkhand',
            'Karnataka',
            'Kerala',
            'Madhya Pradesh',
            'Maharashtra',
            'Odisha',
            'Punjab',
            'Rajasthan',
            'Tamil Nadu',
            'Telangana',
            'Uttar Pradesh',
            'Uttarakhand',
            'West Bengal',
        ];
    }

    public function export()
    {
        return Excel::download(
            new TendersExport,
            'tenders.xlsx'
        );
    }
}
