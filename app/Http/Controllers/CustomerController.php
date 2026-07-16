<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use App\Mail\CustomerExportMail;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function filter(Request $request)
    {
        session([
            'customer_filters' => $request->all()
        ]);

        return redirect()->route('customers.index');
    }

    public function index(Request $request)
    {
        $filters = session('customer_filters', []);

        $query = Customer::with(['spocUser', 'backupSpoc', 'creator']);

        // Customer Filter
        if (!empty($filters['customer_id'])) {

            $customerIds = (array) $filters['customer_id'];

            $query->whereIn('id', $customerIds);
        } else {

            $query->where('status', 'Active');
        }

        // Status Filter
        if (!empty($filters['status'])) {

            $query->whereIn('status', (array) $filters['status']);
        }

        // Domain Filter
        if (!empty($filters['domain'])) {

            $query->whereIn('domain', (array) $filters['domain']);
        }

        // Created By Filter
        if (!empty($filters['created_by'])) {

            $createdBy = (array) $filters['created_by'];

            $query->whereIn('created_by', $createdBy);
        }

        // Customer Dropdown
        $customerOptions = Customer::orderBy('customer')->get();

        // Only Sourcing Users
        $createdByOptions = User::whereJsonContains('roles', 'sourcing')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        // Customers Data
        $customers = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact(
            'customers',
            'customerOptions',
            'createdByOptions'
        ));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $spocs = User::whereJsonContains('roles', 'sourcing')->get();
        $backupSpocs = User::whereJsonContains('roles', 'sourcing')->get();

        return view('customers.create', compact('spocs', 'backupSpocs'));
    }

    /**
     * Store customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer'      => 'required|string|max:255',
            'contact'       => 'nullable|string|max:255',
            'email'         => 'nullable|email',
            'mobile'        => 'nullable|digits_between:10,15|max:20',
            'status'        => 'required|string',
            'remarks'       => 'nullable|string',
            'domain'        => 'required|string',
            'spoc'          => 'nullable',
            'backup_spoc'   => 'nullable|string|max:255',
        ],   [
            '*.required' => 'This field is required.',
        ]);

        // Created by
        $validated['created_by'] = Auth::id();

        // Updated by
        $validated['updated_by'] = Auth::id();

        Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Show customer
     */
    public function show(Customer $customer)
    {
        $customer->load(['spocUser', 'backupSpoc', 'creator', 'updater']);

        return view('customers.show', compact('customer'));
    }

    /**
     * Edit customer
     */
    public function edit(Customer $customer)
    {
        $spocs = User::whereJsonContains('roles', 'sourcing')->get();
        $backupSpocs   = User::whereJsonContains('roles', 'sourcing')->get();
        return view('customers.edit', compact(
            'customer',
            'spocs',
            'backupSpocs'
        ));
    }

    /**
     * Update customer
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'customer'      => 'required|string|max:255',
            'contact'       => 'nullable|string|max:255',
            'email'         => 'nullable|email',
            'mobile'        => 'nullable|digits_between:10,15|max:20',
            'status'        => 'required|string',
            'remarks'       => 'nullable|string',
            'domain'        => 'required|string',
            'spoc'          => 'nullable',
            'backup_spoc'   => 'nullable|string|max:255',
        ],   [
            '*.required' => 'This field is required.',
        ]);

        // Updated by
        $validated['updated_by'] = Auth::id();

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Delete customer
     */
    public function destroy(Customer $customer)
    {
        if (!auth()->user()->hasRole('admin')) {

            abort(403, 'Unauthorized');
        }

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * Export customers
     */
    public function export()
    {
        // Get applied filters from session
        $filters = session('customer_filters', []);

        $fileName = 'customers_' . now()->format('Ymd_His') . '.xlsx';

        // Generate Excel with filtered data
        Excel::store(
            new CustomersExport($filters),
            $fileName,
            'local'
        );

        $filePath = Storage::disk('local')->path($fileName);

        // Send to logged-in user
        $userEmail = auth()->user()->email;

        if ($userEmail && file_exists($filePath)) {

            Mail::to($userEmail)->send(
                new CustomerExportMail($filePath)
            );
        }

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
