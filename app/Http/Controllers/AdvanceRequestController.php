<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\AdvanceRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\AdvanceRequest;

class AdvanceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = AdvanceRequest::with([
            'employee',
            'items',
            'items.category',
            'payments'
        ]);

        if ($user->hasRole('manager')) {
            // Managers see their own and their subordinates' requests
            $query->where(function ($q) use ($user) {
                $q->where('users_id', $user->id)
                    ->orWhereHas('employee', function ($employeeQuery) use ($user) {
                        $employeeQuery->where('manager_id', $user->id);
                    });
            });
        } else {
            // Regular employees, admins, and other roles only see their own requests
            $query->where('users_id', $user->id);
        }

        $advances = $query
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->withSum('items', 'requested_amount')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('advances.index', [
            'advances' => $advances,
            'status'   => $request->status
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $employee = Auth::user();

        $request->validate([
            'advance_reason' => 'required|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'required|exists:expense_categories,id',
            'items.*.requested_amount' => 'required|numeric|min:0',
            'items.*.expense_reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $status = $request->input('action') === 'draft'
                ? 'Pending'
                : 'Submitted';

            $advance = AdvanceRequest::create([
                'users_id'        => $employee->id,
                'advance_reason'  => $request->advance_reason,
                'status'          => $status,
                'approved_amount' => null,
                'pending_amount'  => null,
                'created_by'      => $employee->id,
                'updated_by'      => $employee->id,
            ]);

            foreach ($request->items as $item) {
                AdvanceRequestItem::create([
                    'advance_req_id'   => $advance->id,
                    'category_id'      => $item['category_id'],
                    'requested_amount' => $item['requested_amount'],
                    'expense_reason'   => $item['expense_reason'],
                    'created_by'       => $employee->id,
                    'updated_by'       => $employee->id,
                ]);
            }

            DB::commit();

            if ($status === 'Submitted') {
                try {
                    $advance->load('employee.manager');
                    if ($advance->employee && $advance->employee->manager && $advance->employee->manager->email) {
                        app(\App\Services\MailService::class)->send(
                            $advance->employee->manager->email,
                            new \App\Mail\ExpenseModuleMail($advance, 'advance', 'submitted')
                        );
                    }
                } catch (\Exception $e) {
                    Log::error('Advance submit mail failed in store: ' . $e->getMessage());
                }
            }

            return redirect()
                ->route('advances.index')
                ->with('success', 'Advance request saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Advance save failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdvanceRequest $advance)
    {
        $advance->load([
            'employee',
            'items',
            'items.category'
        ]);

        if ($advance->status !== 'Pending' && $advance->status !== 'Rejected') {
            return redirect()
                ->route('advances.index')
                ->with('error', 'Only pending or rejected advance requests can be edited.');
        }

        $categories = ExpenseCategory::orderBy('category_name')->get();

        return view('advances.edit', compact('advance', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdvanceRequest $advance)
    {
        $employee = Auth::user();

        if (!$employee) {
            return redirect('/login');
        }

        $validated = $request->validate([
            'advance_reason' => 'required|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'required|exists:expense_categories,id',
            'items.*.requested_amount' => 'required|numeric|min:0',
            'items.*.expense_reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $status = $request->input('action') === 'draft'
                ? 'Pending'
                : 'Submitted';

            $advance->update([
                'advance_reason'   => $validated['advance_reason'],
                'status'           => $status,
                'manager_action_at' => null,
                'manager_remarks'  => null,
                'updated_by'       => $employee->id,
            ]);

            // Delete Existing Items
            $advance->items()->delete();

            // Recreate Items
            foreach ($validated['items'] as $item) {
                AdvanceRequestItem::create([
                    'advance_req_id'   => $advance->id,
                    'category_id'      => $item['category_id'],
                    'requested_amount' => $item['requested_amount'],
                    'expense_reason'   => $item['expense_reason'],
                    'created_by'       => $employee->id,
                    'updated_by'       => $employee->id,
                ]);
            }

            // Recalculate Total Requested Amount
            $totalRequested = $advance->items()->sum('requested_amount');

            $advance->update([
                'pending_amount' => $totalRequested
            ]);

            DB::commit();

            if ($status === 'Submitted') {
                try {
                    $advance->load('employee.manager');
                    if ($advance->employee && $advance->employee->manager && $advance->employee->manager->email) {
                        app(\App\Services\MailService::class)->send(
                            $advance->employee->manager->email,
                            new \App\Mail\ExpenseModuleMail($advance, 'advance', 'submitted')
                        );
                    }
                } catch (\Exception $e) {
                    Log::error('Advance submit mail failed in update: ' . $e->getMessage());
                }
            }

            return redirect()
                ->route('advances.index')
                ->with('success', 'Advance request updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Advance update failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ExpenseCategory::orderBy('category_name')->get();

        return view('advances.create', compact('categories'));
    }

    /**
     * Display a listing of items for the specified advance request.
     */
    public function itemsIndex(AdvanceRequest $advance, AdvanceRequestItem $item = null)
    {
        $advance->load([
            'employee',
            'items' => function ($query) {
                $query->latest('id');
            },
            'items.category',
            'payments'
        ]);

        if ($advance->status !== 'Pending' && $advance->status !== 'Rejected') {
            return redirect()
                ->route('advances.index')
                ->with('error', 'Only pending or rejected advance requests can be modified.');
        }

        $editingItem = $item;
        $categories = ExpenseCategory::orderBy('category_name')->get();

        return view('advances.items_index', [
            'advance'      => $advance,
            'items'        => $advance->items,
            'categories'   => $categories,
            'editingItem'  => $editingItem
        ]);
    }

    /**
     * Store a newly created item in the advance request.
     */
    public function storeItem(Request $request, AdvanceRequest $advance)
    {
        $employee = Auth::user();

        if (!$employee) {
            return redirect('/login');
        }

        if ($advance->status !== 'Pending' && $advance->status !== 'Rejected') {
            return redirect()
                ->route('advances.index')
                ->with('error', 'Only pending or rejected advance requests can be modified.');
        }

        $validated = $request->validate([
            'category_id'      => 'required|exists:expense_categories,id',
            'requested_amount' => 'required|numeric|min:0',
            'expense_reason'   => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            AdvanceRequestItem::create([
                'advance_req_id'   => $advance->id,
                'category_id'      => $validated['category_id'],
                'requested_amount' => $validated['requested_amount'],
                'expense_reason'   => $validated['expense_reason'],
                'created_by'       => $employee->id,
                'updated_by'       => $employee->id,
            ]);

            $advance->update([
                'updated_by' => $employee->id
            ]);

            $totalRequested = $advance->items()->sum('requested_amount');

            $advance->update([
                'pending_amount' => $totalRequested
            ]);

            DB::commit();

            return redirect()
                ->route('advances.items.index', $advance->id)
                ->with('success', 'Advance item added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Advance item save failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified item in the advance request.
     */
    public function updateItem(Request $request, AdvanceRequest $advance, AdvanceRequestItem $item)
    {
        $employee = Auth::user();

        if (!$employee) {
            return redirect('/login');
        }

        if ($advance->status !== 'Pending' && $advance->status !== 'Rejected') {
            return redirect()
                ->route('advances.index')
                ->with('error', 'Only pending or rejected advance requests can be modified.');
        }

        $validated = $request->validate([
            'category_id'      => 'required|exists:expense_categories,id',
            'requested_amount' => 'required|numeric|min:0',
            'expense_reason'   => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $item->update([
                'category_id'      => $validated['category_id'],
                'requested_amount' => $validated['requested_amount'],
                'expense_reason'   => $validated['expense_reason'],
                'updated_by'       => $employee->id,
            ]);

            $totalRequested = $advance->items()->sum('requested_amount');

            $advance->update([
                'updated_by'     => $employee->id,
                'pending_amount' => $totalRequested,
            ]);

            DB::commit();

            return redirect()
                ->route('advances.items.index', $advance->id)
                ->with('success', 'Advance item updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Advance item update failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified item from the advance request.
     */
    public function deleteItem(AdvanceRequest $advance, AdvanceRequestItem $item)
    {
        $employee = Auth::user();

        if (!$employee) {
            return redirect('/login');
        }

        if ($advance->status !== 'Pending' && $advance->status !== 'Rejected') {
            return redirect()
                ->route('advances.index')
                ->with('error', 'Only pending or rejected advance requests can be modified.');
        }

        try {
            DB::beginTransaction();

            $item->delete();

            $totalRequested = $advance->items()->sum('requested_amount');

            $advance->update([
                'updated_by'     => $employee->id,
                'pending_amount' => $totalRequested,
            ]);

            DB::commit();

            return redirect()
                ->route('advances.items.index', $advance->id)
                ->with('success', 'Advance item deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Advance item deletion failed: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Submit the specified advance request to manager.
     */
    public function submit(AdvanceRequest $advance)
    {
        $employee = Auth::user();

        if (!$employee) {
            return redirect('/login');
        }

        if ($advance->status !== 'Pending' && $advance->status !== 'Rejected') {
            return redirect()
                ->route('advances.index')
                ->with('error', 'Only pending or rejected advance requests can be submitted.');
        }

        if ($advance->items->isEmpty()) {
            return back()->with('error', 'Cannot submit an advance request with no items.');
        }

        try {
            DB::beginTransaction();

            $totalRequested = $advance->items()->sum('requested_amount');

            $advance->update([
                'status'            => 'Submitted',
                'pending_amount'    => $totalRequested,
                'manager_action_at' => null,
                'manager_remarks'   => null,
                'updated_by'        => $employee->id,
            ]);

            DB::commit();

            try {
                $advance->load('employee.manager');
                if ($advance->employee && $advance->employee->manager && $advance->employee->manager->email) {
                    app(\App\Services\MailService::class)->send(
                        $advance->employee->manager->email,
                        new \App\Mail\ExpenseModuleMail($advance, 'advance', 'submitted')
                    );
                }
            } catch (\Exception $e) {
                Log::error('Advance submit mail failed in submit: ' . $e->getMessage());
            }

            return redirect()
                ->route('advances.index')
                ->with('success', 'Advance request submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Advance submission failed: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }
}
