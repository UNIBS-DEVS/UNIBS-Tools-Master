<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AccountController extends Controller
{
    public function requests(Request $request)
    {
        $query = Expense::with([
            'employee',
            'reimbursement',
            'items'
        ])
            ->where(
                'status',
                'Approved'
            );

        if (
            $request->filled('account_status') &&
            $request->account_status != 'All'
        ) {
            $query->whereHas(
                'reimbursement',
                function ($q) use ($request) {
                    $q->where(
                        'status',
                        $request->account_status
                    );
                }
            );
        }

        $requests = $query
            ->latest('id')
            ->get();

        return view(
            'accounts.requests',
            compact('requests')
        );
    }
    public function showProcess($id)
    {
        $expense = Expense::with([
            'employee',
            'employee.manager',
            'items',
            'items.category',
            'items.attachments'
        ])->findOrFail($id);

        $outstandingAdvances = \App\Models\AdvanceRequest::where('users_id', $expense->employee_id)
            ->whereIn('status', ['Paid', 'Partially Settled'])
            ->with('payments')
            ->get();

        return view(
            'accounts.process',
            compact('expense', 'outstandingAdvances')
        );
    }
    public function process(Request $request, $id)
    {
        $user = Auth::user();

        if (
            !$user ||
            !$user->hasRole([
                'admin',
                'accounts'
            ])
        ) {
            abort(403);
        }

        $expense = Expense::findOrFail($id);

        if ($request->status === 'Paid') {
            $totalSettled = 0;
            if ($request->has('settlements')) {
                foreach ($request->settlements as $advanceId => $settledAmount) {
                    $totalSettled += (float)$settledAmount;
                }
            }

            $maxAllowedPaid = max(0.0, (float)$expense->total_amount - $totalSettled);

            $request->validate([
                'amount_paid' => 'required|numeric|min:0|max:' . $maxAllowedPaid,
                'payment_method' => 'required|string|max:50',
                'transaction_reference' => 'nullable|string|max:255',
                'accounts_remarks' => 'required|string|max:1000',
            ], [
                'amount_paid.max' => 'The paid amount cannot exceed the remaining requested amount of ' . number_format($maxAllowedPaid, 2) . ' (Total: ' . number_format($expense->total_amount, 2) . ' minus Settled: ' . number_format($totalSettled, 2) . ').',
            ]);
        } else {
            $request->validate([
                'accounts_remarks' => 'required|string|max:1000',
            ]);
        }

        $amountPaid = 0;
        $transactionReference = null;
        $expenseStatus = 'Approved';

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $id, $user, &$amountPaid, &$transactionReference, &$expenseStatus) {
            if (
                $request->status == 'Failed' ||
                $request->status == 'Pending'
            ) {
                $amountPaid = 0;
                $transactionReference = null;
            } else {
                $amountPaid = $request->amount_paid;
                $transactionReference = $request->transaction_reference;
            }

            Reimbursement::create([
                'expense_id' => $id,
                'amount_paid' => $amountPaid,
                'payment_date' => $request->status == 'Paid' ? now()->toDateString() : null,
                'payment_method' => $request->payment_method,
                'transaction_reference' => $transactionReference,
                'status' => $request->status,
                'accounts_remarks' => $request->accounts_remarks ?? '',
                'updated_by' => $user->id,
                'created_by' => $user->id
            ]);

            if ($request->status == 'Paid') {
                $expenseStatus = 'Reimbursed';

                // Process Settlements
                if ($request->has('settlements')) {
                    foreach ($request->settlements as $advanceId => $settledAmount) {
                        $settledAmount = (float) $settledAmount;
                        if ($settledAmount > 0) {
                            $advance = \App\Models\AdvanceRequest::lockForUpdate()->find($advanceId);
                            if ($advance) {
                                \App\Models\AdvanceSettlement::create([
                                    'advance_id' => $advance->id,
                                    'expense_id' => $id,
                                    'settled_amount' => $settledAmount,
                                    'remarks' => 'Settled via Reimbursement Expense #' . $id,
                                    'created_by' => $user->id,
                                    'updated_by' => $user->id,
                                ]);

                                $newPendingAmount = max(0.0, (float)$advance->pending_amount - $settledAmount);
                                $newStatus = $newPendingAmount == 0 ? 'Fully Settled' : 'Partially Settled';

                                $advance->update([
                                    'pending_amount' => $newPendingAmount,
                                    'status' => $newStatus,
                                    'updated_by' => $user->id,
                                ]);
                            }
                        }
                    }
                }
            } elseif (
                $request->status == 'Failed' ||
                $request->status == 'Rejected'
            ) {
                $expenseStatus = 'Rejected';
            } else {
                $expenseStatus = 'Approved';
            }

            Expense::where('id', $id)->update([
                'status' => $expenseStatus
            ]);
        });

        $expense = Expense::with(
            'employee'
        )->findOrFail($id);



        $reimbursement = Reimbursement::where(
            'expense_id',
            $id
        )->latest('id')
            ->first();

        $employee = User::find(
            $expense->employee_id
        );

        try {
            if ($request->status == 'Paid') {
                $expense->load('employee');
                $employee = $expense->employee;
                app(\App\Services\MailService::class)->send(
                    $employee->email,
                    new \App\Mail\ExpenseModuleMail($reimbursement, 'expense', 'accounts_approved')
                );
            }

            if (
                $request->status == 'Failed' ||
                $request->status == 'Rejected' ||
                $request->status == 'Pending'
            ) {
                $expense->load('employee');
                $employee = $expense->employee;
                app(\App\Services\MailService::class)->send(
                    $employee->email,
                    new \App\Mail\ExpenseModuleMail($reimbursement, 'expense', 'accounts_rejected')
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Expense reimbursement mail failed: ' . $e->getMessage());
        }

        return redirect(
            '/account/requests'
        );
    }
}
