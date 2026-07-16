<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Sale;
use App\Models\SaleRemark;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    public function filter(Request $request)
    {
        session([
            'sale_filters' => $request->all()
        ]);

        return redirect()->route('sales.index');
    }

    public function index(Request $request)
    {
        $filters = session('sale_filters', []);

        // Default Logged-in User
        if (empty($filters['created_by'])) {
            $filters['created_by'] = [auth()->id()];
        }

        $query = Sale::with(['creator']);

        /*
        |--------------------------------------------------------------------------
        | DEFAULT VALUES
        |--------------------------------------------------------------------------
        */
        $types = [
            'Sourcing',
            'Training',
            'Job Seeker',
            'Microsoft',
            'Tally',
            'Google',
            'Zoho',
            'Software Services',
            'Digital Marketing',
            'Razorpay',
            'BGC',
            'Others',
        ];

        $sources = [
            'IndiaMart',
            'Justdial',
            'Linkedin',
            'Facebook',
            'Instagram',
            'Twitter',
            'References',
            'Others',
        ];

        $statuses = [
            'New',
            'Under Discussion',
        ];

        $followUps = [
            'null',
            'today',
            'others',
        ];

        /*
        |--------------------------------------------------------------------------
        | COMPANY NAME
        |--------------------------------------------------------------------------
        */
        if (!empty($filters['company'])) {

            $query->where(
                'company',
                'LIKE',
                '%' . $filters['company'] . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */
        if (!empty($filters['mobile'])) {

            $query->where(
                'mobile',
                'LIKE',
                '%' . $filters['mobile'] . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TYPE
        |--------------------------------------------------------------------------
        */
        $selectedTypes = $filters['type'] ?? $types;

        if (!empty($selectedTypes)) {

            $query->whereIn(
                'type',
                $selectedTypes
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SOURCE
        |--------------------------------------------------------------------------
        */
        $selectedSources = $filters['source'] ?? $sources;

        if (!empty($selectedSources)) {

            $query->whereIn(
                'source',
                $selectedSources
            );
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */
        $selectedStatuses = $filters['status'] ?? $statuses;

        if (!empty($selectedStatuses)) {

            $query->whereIn(
                'status',
                $selectedStatuses
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FOLLOW UP
        |--------------------------------------------------------------------------
        */
        $selectedFollowUps = $filters['follow_up_date'] ?? $followUps;

        if (!empty($selectedFollowUps)) {

            $query->where(function ($q) use ($selectedFollowUps) {

                if (in_array('today', $selectedFollowUps)) {

                    $q->orWhereDate(
                        'follow_up_date',
                        now()->toDateString()
                    );
                }

                if (in_array('others', $selectedFollowUps)) {

                    $q->orWhereDate(
                        'follow_up_date',
                        '!=',
                        now()->toDateString()
                    )->whereNotNull('follow_up_date');
                }

                if (in_array('null', $selectedFollowUps)) {

                    $q->orWhereNull('follow_up_date');
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | CREATED BY
        |--------------------------------------------------------------------------
        */
        $query->whereIn(
            'created_by',
            (array) $filters['created_by']
        );

        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {

            $query->whereBetween('created_at', [
                Carbon::parse($filters['from_date'])->startOfDay(),
                Carbon::parse($filters['to_date'])->endOfDay(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | SALES USERS
        |--------------------------------------------------------------------------
        */
        $createdBySales = User::whereJsonContains('roles', 'sales')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */
        $sales = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact(
            'sales',
            'filters',
            'createdBySales'
        ));
    }

    public function create()
    {
        return view('sales.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([

            'client_contact' => 'required|string|max:255',

            'company' => 'nullable|string|max:255',

            'email' => 'nullable|email|max:255',

            'mobile' => 'nullable|string|max:20',

            'location' => 'nullable|string|max:255',

            'requirement' => 'nullable|string',

            'type' => 'required|in:Sourcing,Training,Job Seeker,Microsoft,Tally,Google,Zoho,Software Services,Digital Marketing,Razorpay,BGC,Others',

            'source' => 'required|in:IndiaMart,Justdial,Linkedin,Facebook,Instagram,Twitter,References,Others',

            'follow_up_date' => 'nullable|date',

            'status' => 'required|in:New,Won,Lost,Under Discussion,On-Hold,Fake,Spam,Irrelevant,Repeatedly Unreachable',

        ], [
            '*.required' => 'This field is required.',
        ]);

        // Created by
        $validated['created_by'] = Auth::id();

        // Updated by
        $validated['updated_by'] = Auth::id();

        $sale = Sale::create($validated);

        if (!empty(trim($request->remarks))) {

            SaleRemark::create([
                'sale_id' => $sale->id,
                'remarks'      => trim($request->remarks),
                'created_by'   => Auth::id(),
                'updated_by'   => Auth::id(),
            ]);
        }

        return redirect()
            ->route('sales.index')
            ->with('success', 'Sale created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load([
            'creator',
            'updater',
            'remarkHistories.creator'
        ]);

        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        $sale->load([
            'remarkHistories.creator'
        ]);

        return view('sales.edit', compact('sale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([

            'client_contact' => 'required|string|max:255',

            'company' => 'nullable|string|max:255',

            'email' => 'nullable|email|max:255',

            'mobile' => 'nullable|string|max:20',

            'location' => 'nullable|string|max:255',

            'requirement' => 'nullable|string',

            'type' => 'required|in:Sourcing,Training,Job Seeker,Microsoft,Tally,Google,Zoho,Software Services,Digital Marketing,Razorpay,BGC,Others',

            'source' => 'required|in:IndiaMart,Justdial,Linkedin,Facebook,Instagram,Twitter,References,Others',

            'follow_up_date' => 'nullable|date',

            'status' => 'required|in:New,Won,Lost,Under Discussion,On-Hold,Fake,Spam,Irrelevant,Repeatedly Unreachable',

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

        $sale->update($validated);

        if (!empty(trim($request->remarks))) {

            SaleRemark::create([
                'sale_id' => $sale->id,
                'remarks'      => trim($request->remarks),
                'created_by'   => Auth::id(),
                'updated_by'   => Auth::id(),
            ]);
        }

        return redirect()
            ->route('sales.edit', [
                'sale' => $sale->id,
                'page' => $request->page,
            ])
            ->with('success', 'Lead updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();

        return redirect()
            ->route('sales.index')
            ->with('success', 'Sale deleted successfully.');
    }

    public function export()
    {
        return Excel::download(
            new SalesExport,
            'sales.xlsx'
        );
    }
}
