<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    public function requests(Request $request)
    {
        $user = Auth::user();

        if (
            !$user ||
            !$user->hasRole(['manager', 'admin'])
        ) {
            abort(403);
        }

        $status = $request->status ?? 'Submitted';

        $query = Expense::with([
            'employee',
            'employee.manager',
            'items',
            'items.category',
            'items.attachments'
        ])
            ->withSum('items', 'amount');

        if (!$user->hasRole(['admin'])) {
            $query->whereHas(
                'employee',
                function ($employeeQuery) use ($user) {
                    $employeeQuery->where(
                        'manager_id',
                        $user->id
                    );
                }
            );
        }

        if ($status !== 'all') {
            $query->where(
                'status',
                $status
            );
        } else {
            $query->whereIn(
                'status',
                ['Submitted', 'Approved', 'Rejected']
            );
        }

        $requests = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'manager.requests',
            compact('requests', 'status')
        );
    }

    public function approve(Request $request, $id)
    {
        $user = Auth::user();

        if (
            !$user ||
            !$user->hasRole(['manager', 'admin'])
        ) {
            abort(403);
        }

        $expense = $this->getAccessibleExpense(
            $id,
            $user
        );

        if ($expense->status !== 'Submitted') {
            return redirect()
                ->route('manager.requests')
                ->with(
                    'error',
                    'Only submitted expense requests can be approved.'
                );
        }

        $request->validate([
            'manager_remarks' => 'required|string|max:1000'
        ]);


        $expense->update([
            'status'            => 'Approved',
            'manager_action_at' => now(),
            'manager_remarks'   => $request->manager_remarks,
            'updated_by'        => $user->id
        ]);

        $expense->load('employee');

        $accounts = User::whereJsonContains('roles', 'account')->get();

        try {
            foreach ($accounts as $account) {
                app(\App\Services\MailService::class)->send(
                    $account->email,
                    new \App\Mail\ExpenseModuleMail($expense, 'expense', 'accounts_received')
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Expense manager approval accounts notify mail failed: ' . $e->getMessage());
        }

        try {
            if ($expense->employee) {
                app(\App\Services\MailService::class)->send(
                    $expense->employee->email,
                    new \App\Mail\ExpenseModuleMail($expense, 'expense', 'manager_approved')
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Expense manager approval employee notify mail failed: ' . $e->getMessage());
        }

        return redirect()
            ->route('manager.requests')
            ->with(
                'success',
                'Expense approved successfully.'
            );
    }


    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        if (
            !$user ||
            !$user->hasRole(['manager', 'admin'])
        ) {
            abort(403);
        }

        $expense = $this->getAccessibleExpense(
            $id,
            $user
        );

        if ($expense->status !== 'Submitted') {
            return redirect()
                ->route('manager.requests')
                ->with(
                    'error',
                    'Only submitted expense requests can be rejected.'
                );
        }

        $request->validate([
            'manager_remarks' => 'required|string|max:1000'
        ]);

        $expense->update([
            'status'            => 'Rejected',
            'manager_action_at' => now(),
            'manager_remarks'   => $request->manager_remarks,
            'updated_by'        => $user->id
        ]);

        $expense->load('employee');

        try {
            if ($expense->employee) {
                app(\App\Services\MailService::class)->send(
                    $expense->employee->email,
                    new \App\Mail\ExpenseModuleMail($expense, 'expense', 'manager_rejected')
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Expense manager rejection employee notify mail failed: ' . $e->getMessage());
        }

        return redirect()
            ->route('manager.requests')
            ->with(
                'success',
                'Expense rejected successfully.'
            );
    }

    protected function getAccessibleExpense($id, User $user): Expense
    {
        $query = Expense::with([
            'employee',
            'items',
            'items.category',
            'items.attachments'
        ])->where(
            'id',
            $id
        );

        if (!$user->hasRole(['admin'])) {
            $query->whereHas(
                'employee',
                function ($employeeQuery) use ($user) {
                    $employeeQuery->where(
                        'manager_id',
                        $user->id
                    );
                }
            );
        }

        return $query->firstOrFail();
    }

    public function show($id)
    {
        $user = Auth::user();

        $expense = $this->getAccessibleExpense(
            $id,
            $user
        );

        return view(
            'manager.show',
            compact('expense')
        );
    }
}
