<?php

namespace App\Http\Controllers;

use App\Models\AdvanceRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ManagerAdvanceController extends Controller
{
    /**
     * Display a listing of advance requests for the manager.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['manager', 'admin'])) {
            abort(403);
        }

        $status = $request->status ?? 'Submitted';

        $query = AdvanceRequest::with([
            'employee',
            'employee.manager',
            'items',
            'items.category'
        ])
            ->withSum('items', 'requested_amount');

        if (!$user->hasRole(['admin'])) {
            $query->whereHas('employee', function ($employeeQuery) use ($user) {
                $employeeQuery->where('manager_id', $user->id);
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', [
                'Submitted',
                'Approved',
                'Rejected',
                'Paid',
                'Partially Settled',
                'Fully Settled'
            ]);
        }

        $requests = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('manager.advance_requests', compact('requests', 'status'));
    }

    /**
     * Approve the specified advance request.
     */
    public function approve(Request $request, AdvanceRequest $advance)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['manager', 'admin'])) {
            abort(403);
        }

        $this->authorizeAccess($advance, $user);

        if ($advance->status !== 'Submitted') {
            return redirect()
                ->route('manager.advances.index')
                ->with('error', 'Only submitted advance requests can be approved.');
        }

        $request->validate([
            'approved_amount' => 'required|numeric|min:0|max:' . $advance->total_requested_amount,
            'manager_remarks' => 'required|string|max:1000',
        ], [
            'approved_amount.max' => 'The approved amount cannot exceed the requested amount of ' . $advance->total_requested_amount . '.',
        ]);

        $advance->update([
            'status'            => 'Approved',
            'approved_amount'   => $request->approved_amount,
            'pending_amount'    => $request->approved_amount,
            'manager_action_at' => now(),
            'manager_remarks'   => $request->manager_remarks,
            'updated_by'        => $user->id,
        ]);

        $advance->load('employee');

        $accounts = User::whereJsonContains('roles', 'account')->get();

        try {
            foreach ($accounts as $account) {
                app(\App\Services\MailService::class)->send(
                    $account->email,
                    new \App\Mail\ExpenseModuleMail($advance, 'advance', 'accounts_received')
                );
            }
        } catch (\Exception $e) {
            Log::error('Advance manager approval accounts notify mail failed: ' . $e->getMessage());
        }

        try {
            if ($advance->employee && $advance->employee->email) {
                app(\App\Services\MailService::class)->send(
                    $advance->employee->email,
                    new \App\Mail\ExpenseModuleMail($advance, 'advance', 'manager_approved')
                );
            }
        } catch (\Exception $e) {
            Log::error('Advance manager approval employee notify mail failed: ' . $e->getMessage());
        }

        return redirect()
            ->route('manager.advances.index')
            ->with('success', 'Advance request approved successfully.');
    }

    /**
     * Reject the specified advance request.
     */
    public function reject(Request $request, AdvanceRequest $advance)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['manager', 'admin'])) {
            abort(403);
        }

        $this->authorizeAccess($advance, $user);

        if ($advance->status !== 'Submitted') {
            return redirect()
                ->route('manager.advances.index')
                ->with('error', 'Only submitted advance requests can be rejected.');
        }

        $request->validate([
            'manager_remarks' => 'required|string|max:1000',
        ]);

        $advance->update([
            'status'            => 'Rejected',
            'manager_action_at' => now(),
            'manager_remarks'   => $request->manager_remarks,
            'updated_by'        => $user->id,
        ]);

        $advance->load('employee');

        try {
            if ($advance->employee && $advance->employee->email) {
                app(\App\Services\MailService::class)->send(
                    $advance->employee->email,
                    new \App\Mail\ExpenseModuleMail($advance, 'advance', 'manager_rejected')
                );
            }
        } catch (\Exception $e) {
            Log::error('Advance manager rejection employee notify mail failed: ' . $e->getMessage());
        }

        return redirect()
            ->route('manager.advances.index')
            ->with('success', 'Advance request rejected successfully.');
    }

    /**
     * Display details of the specified advance request.
     */
    public function show(AdvanceRequest $advance)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['manager', 'admin'])) {
            abort(403);
        }

        $this->authorizeAccess($advance, $user);

        $advance->load([
            'employee',
            'items',
            'items.category'
        ]);

        return view('manager.advance_show', compact('advance'));
    }

    /**
     * Validate manager access to the specified advance request.
     */
    protected function authorizeAccess(AdvanceRequest $advance, User $user): void
    {
        if ($user->hasRole(['admin'])) {
            return;
        }

        $isManager = $advance->employee()->where('manager_id', $user->id)->exists();

        if (!$isManager) {
            abort(403, 'Unauthorized access to this advance request.');
        }
    }
}
