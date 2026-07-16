<?php

namespace App\Http\Controllers;

use App\Models\AdvanceRequest;
use App\Models\AdvancePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AccountAdvanceController extends Controller
{
    /**
     * Display a listing of advance requests for accounts review.
     */
    public function requests(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['admin', 'accounts'])) {
            abort(403);
        }

        $status = $request->status ?? 'Approved';

        $query = AdvanceRequest::with([
            'employee',
            'payments',
            'items'
        ]);

        if ($status !== 'all') {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', [
                'Approved',
                'Paid',
                'Rejected',
                'Partially Settled',
                'Fully Settled'
            ]);
        }

        $requests = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('accounts.advance_requests', compact('requests', 'status'));
    }

    /**
     * Show the process form for the specified advance request.
     */
    public function showProcess(AdvanceRequest $advance)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['admin', 'accounts'])) {
            abort(403);
        }

        $advance->load([
            'employee',
            'employee.manager',
            'items',
            'items.category',
            'payments'
        ]);

        return view('accounts.advance_process', compact('advance'));
    }

    /**
     * Process the payment or rejection for the specified advance request.
     */
    public function process(Request $request, AdvanceRequest $advance)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['admin', 'accounts'])) {
            abort(403);
        }

        if ($request->status === 'Paid') {
            $request->validate([
                'paid_amount'      => 'required|numeric|min:0|max:' . $advance->approved_amount,
                'payment_mode'     => 'required|string|max:50',
                'reference_no'     => 'required|string|max:255',
                'payment_date'     => 'required|date',
                'accounts_remarks' => 'nullable|string|max:1000',
            ], [
                'paid_amount.max' => 'The paid amount cannot exceed the manager-approved amount of ' . $advance->approved_amount . '.',
            ]);

            AdvancePayment::create([
                'advance_id'   => $advance->id,
                'paid_amount'  => $request->paid_amount,
                'payment_date' => $request->payment_date,
                'payment_mode' => $request->payment_mode,
                'reference_no' => $request->reference_no,
                'remarks'      => $request->accounts_remarks ?? '',
                'created_by'   => $user->id,
                'updated_by'   => $user->id,
            ]);

            $advance->update([
                'status'           => 'Paid',
                'pending_amount'   => $request->paid_amount,
                'accounts_remarks' => $request->accounts_remarks,
                'updated_by'       => $user->id,
                'approved_amount'  => $request->paid_amount,
            ]);

            $advance->load('employee');
            $payment = AdvancePayment::where('advance_id', $advance->id)->latest('id')->first();

            try {
                if ($advance->employee && $advance->employee->email && $payment) {
                    app(\App\Services\MailService::class)->send(
                        $advance->employee->email,
                        new \App\Mail\ExpenseModuleMail($advance, 'advance', 'accounts_approved', $payment)
                    );
                }
            } catch (\Exception $e) {
                Log::error('Advance accounts payment employee notify mail failed: ' . $e->getMessage());
            }

            return redirect()
                ->route('accounts.advances.requests')
                ->with('success', 'Advance request marked as Paid and payment recorded successfully.');
        } elseif ($request->status === 'Rejected') {
            $request->validate([
                'accounts_remarks' => 'required|string|max:1000',
            ]);

            $advance->update([
                'status'           => 'Rejected',
                'accounts_remarks' => $request->accounts_remarks,
                'updated_by'       => $user->id,
            ]);

            $advance->load('employee');

            try {
                if ($advance->employee && $advance->employee->email) {
                    app(\App\Services\MailService::class)->send(
                        $advance->employee->email,
                        new \App\Mail\ExpenseModuleMail($advance, 'advance', 'accounts_rejected')
                    );
                }
            } catch (\Exception $e) {
                Log::error('Advance accounts rejection employee notify mail failed: ' . $e->getMessage());
            }

            return redirect()
                ->route('accounts.advances.requests')
                ->with('success', 'Advance request has been rejected.');
        }

        return redirect()
            ->route('accounts.advances.requests')
            ->with('error', 'Invalid action selected.');
    }
}
