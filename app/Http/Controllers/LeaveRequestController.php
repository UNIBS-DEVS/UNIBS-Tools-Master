<?php

namespace App\Http\Controllers;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\MailService;
use App\Mail\MyCustomMail;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $this->initializeBalancesForUser($user->id);

        $query = LeaveRequest::with('leaveType')
            ->where('created_by', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Fetch user's leave balances for display
        $leaveBalances = LeaveBalance::with('leaveType')
            ->where('user_id', $user->id)
            ->get();

        // Fetch user's transaction history log
        $leaveTransactions = LeaveTransaction::with(['leaveType', 'creator'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view(
            'leave_requests.index',
            compact('leaveRequests', 'leaveBalances', 'leaveTransactions')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userId = Auth::id();
        $this->initializeBalancesForUser($userId);

        $leaveTypes = LeaveType::where('status', 'active')
            ->leftJoin('leave_balances', function ($join) use ($userId) {
                $join->on('leave_types.id', '=', 'leave_balances.leave_type_id')
                    ->where('leave_balances.user_id', '=', $userId);
            })
            ->select(
                'leave_types.*',
                'leave_balances.balance'
            )
            ->orderBy('leave_name')
            ->get();

        return view('leave_requests.create', compact('leaveTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'duration'      => 'required|in:Full Day,First Half,Second Half',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'remarks'       => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();
        $this->initializeBalancesForUser($userId);

        // Calculate requested duration
        $durationDays = 0.5;
        if ($validated['duration'] === 'Full Day') {
            $startDate = \Carbon\Carbon::parse($validated['start_date']);
            $endDate = \Carbon\Carbon::parse($validated['end_date']);
            $durationDays = $startDate->diffInDays($endDate) + 1;
        }

        // Fetch available balance
        $balanceRecord = LeaveBalance::where('leave_type_id', $validated['leave_type_id'])
            ->where('user_id', $userId)
            ->first();

        $availableBalance = $balanceRecord ? (float) $balanceRecord->balance : 0.00;

        // Sum other pending requests of the same type
        $pendingLeavesDuration = LeaveRequest::where('created_by', $userId)
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('status', 'submitted')
            ->get()
            ->sum(function ($pendingReq) {
                if (strtolower($pendingReq->duration) !== 'full day') {
                    return 0.5;
                }
                $start = \Carbon\Carbon::parse($pendingReq->start_date);
                $end = \Carbon\Carbon::parse($pendingReq->end_date);
                return $start->diffInDays($end) + 1;
            });

        if ($availableBalance - $pendingLeavesDuration < $durationDays) {
            return back()
                ->withInput()
                ->with('error', 'Insufficient leave balance. Available (excluding other pending): ' . ($availableBalance - $pendingLeavesDuration) . ' days.');
        }

        // Ensure balance record exists in table
        LeaveBalance::firstOrCreate(
            [
                'leave_type_id' => $validated['leave_type_id'],
                'user_id'       => $userId,
            ],
            [
                'balance'       => 0.00,
                'created_by'    => $userId,
                'updated_by'    => $userId,
            ]
        );

        $leaveRequest = LeaveRequest::create([
            'leave_type_id' => $validated['leave_type_id'],
            'duration'      => $validated['duration'],
            'start_date'    => $validated['start_date'],
            'end_date'      => $validated['end_date'],
            'remarks'       => $validated['remarks'] ?? null,
            'status'        => 'submitted',
            'created_by'    => $userId,
            'updated_by'    => $userId,
        ]);

        // Send submission notification email to the manager
        $user = Auth::user();
        $manager = $user->manager;
        $config = \App\Models\ToolsMaster::first();
        $attendanceNotificationEmail = $config?->attendance_notification_email;
        $managerEmail = $manager?->email;

        if ($managerEmail || $attendanceNotificationEmail) {
            try {
                $toEmail = $managerEmail ?: $attendanceNotificationEmail;
                $cc = array_filter([$user->email, $managerEmail ? $attendanceNotificationEmail : null]);

                $html = view('emails.attendance_module_template', [
                    'model' => $leaveRequest,
                    'type' => 'leave',
                    'status' => 'submitted',
                    'tableData' => [
                        'Employee' => $user->name,
                        'Status'   => 'Submitted',
                        'Dates'    => "{$leaveRequest->start_date->format('d M Y')} to {$leaveRequest->end_date->format('d M Y')}",
                        'Duration' => ucfirst($leaveRequest->duration),
                        'Leave Type' => $leaveRequest->leaveType->leave_name ?? 'None',
                        'Remarks'  => $leaveRequest->remarks ?? '-',
                    ]
                ])->render();

                app(\App\Services\MailService::class)->send(
                    $toEmail,
                    new \App\Mail\AttendanceModuleMail($leaveRequest, 'leave', 'submitted'),
                    'New Leave Application Submitted by ' . $user->name,
                    $html,
                    $cc
                );
            } catch (\Throwable $e) {
                return back()->with(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return redirect()
            ->route('leave-requests.index')
            ->with('success', 'Leave applied successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $leaveRequest = LeaveRequest::with('leaveType')->findOrFail($id);

        if (
            !Auth::user()->hasAnyRole(['admin', 'hr', 'manager']) &&
            $leaveRequest->created_by != Auth::id()
        ) {
            abort(403);
        }

        return view('leave_requests.show', compact('leaveRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if (
            !Auth::user()->hasAnyRole(['admin', 'hr', 'manager']) &&
            $leaveRequest->created_by != Auth::id()
        ) {
            abort(403);
        }

        $userId = Auth::id();
        $this->initializeBalancesForUser($userId);

        $leaveTypes = LeaveType::where('status', 'active')
            ->leftJoin('leave_balances', function ($join) use ($userId) {
                $join->on('leave_types.id', '=', 'leave_balances.leave_type_id')
                    ->where('leave_balances.user_id', '=', $userId);
            })
            ->select(
                'leave_types.*',
                'leave_balances.balance'
            )
            ->orderBy('leave_name')
            ->get();

        return view('leave_requests.edit', compact('leaveRequest', 'leaveTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if (
            !Auth::user()->hasAnyRole(['admin', 'hr', 'manager']) &&
            $leaveRequest->created_by != Auth::id()
        ) {
            abort(403);
        }

        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'duration'      => 'required|in:Full Day,First Half,Second Half',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'remarks'       => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();
        $this->initializeBalancesForUser($userId);

        // Calculate requested duration
        $durationDays = 0.5;
        if ($validated['duration'] === 'Full Day') {
            $startDate = \Carbon\Carbon::parse($validated['start_date']);
            $endDate = \Carbon\Carbon::parse($validated['end_date']);
            $durationDays = $startDate->diffInDays($endDate) + 1;
        }

        // Fetch available balance
        $balanceRecord = LeaveBalance::where('leave_type_id', $validated['leave_type_id'])
            ->where('user_id', $userId)
            ->first();

        $availableBalance = $balanceRecord ? (float) $balanceRecord->balance : 0.00;

        // Sum other pending requests (excluding the current one)
        $pendingLeavesDuration = LeaveRequest::where('created_by', $userId)
            ->where('id', '!=', $id)
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('status', 'submitted')
            ->get()
            ->sum(function ($pendingReq) {
                if (strtolower($pendingReq->duration) !== 'full day') {
                    return 0.5;
                }
                $start = \Carbon\Carbon::parse($pendingReq->start_date);
                $end = \Carbon\Carbon::parse($pendingReq->end_date);
                return $start->diffInDays($end) + 1;
            });

        if ($availableBalance - $pendingLeavesDuration < $durationDays) {
            return back()
                ->withInput()
                ->with('error', 'Insufficient leave balance. Available (excluding other pending): ' . ($availableBalance - $pendingLeavesDuration) . ' days.');
        }

        $leaveRequest->update([
            'leave_type_id' => $validated['leave_type_id'],
            'duration'      => $validated['duration'],
            'start_date'    => $validated['start_date'],
            'end_date'      => $validated['end_date'],
            'remarks'       => $validated['remarks'],
            'updated_by'    => Auth::id(),
        ]);

        return redirect()
            ->route('leave-requests.index')
            ->with('success', 'Leave updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */

    public function cancel(string $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if (
            $leaveRequest->created_by != Auth::id() ||
            !in_array(strtolower($leaveRequest->status), ['submitted', 'rejected'])
        ) {
            return back()->with('error', 'This leave request cannot be cancelled.');
        }

        $leaveRequest->update([
            'status'     => 'cancelled',
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('leave-requests.index')
            ->with('success', 'Leave request cancelled successfully.');
    }

    /**
     * Below code for manager.
     */
    public function managerIndex()
    {

        $user = Auth::user();

        if (! $user->hasAnyRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized Access');
        }

        $query = LeaveRequest::with([
            'leaveType',
            'employee',
        ])
            ->where('status', 'submitted');

        // Managers can approve only their employees' requests; Admin can approve all
        if (! $user->hasRole('admin')) {
            $query->whereHas('employee', function ($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        }

        $leaveRequests = $query
            ->latest()
            ->paginate(10);

        return view('leave_requests.manager', compact('leaveRequests'));
    }

    public function approved(Request $request, $id)
    {
        $user = Auth::user();

        if (! $user->hasAnyRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized');
        }

        $leaveRequest = $this->getAccessibleLeaveRequest($id, $user);

        if (!$leaveRequest->employee) {
            return back()->with('error', 'Employee record not found.');
        }

        if (strtolower($leaveRequest->status) !== 'submitted') {
            return redirect()
                ->route('leave-requests.manager')
                ->with('error', 'Only submitted leave requests can be approved.');
        }

        $validated = $request->validate([
            'manager_remarks' => 'required|string|max:1000',
        ]);

        // Calculate duration days
        $durationDays = 0.5;
        if (strtolower($leaveRequest->duration) === 'full day') {
            $startDate = \Carbon\Carbon::parse($leaveRequest->start_date);
            $endDate = \Carbon\Carbon::parse($leaveRequest->end_date);
            $durationDays = $startDate->diffInDays($endDate) + 1;
        }

        // Deduct from leave balance
        $balanceRecord = LeaveBalance::where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('user_id', $leaveRequest->created_by)
            ->first();

        if (!$balanceRecord || $balanceRecord->balance < $durationDays) {
            return back()->with('error', 'Employee has insufficient leave balance to approve this request.');
        }

        $previousBalance = (float) $balanceRecord->balance;
        $newBalance = $previousBalance - (float) $durationDays;

        $balanceRecord->balance = $newBalance;
        $balanceRecord->save();

        // Log debit transaction
        LeaveTransaction::create([
            'user_id'          => $leaveRequest->created_by,
            'leave_type_id'    => $leaveRequest->leave_type_id,
            'transaction_type' => 'debit',
            'amount'           => $durationDays,
            'previous_balance' => $previousBalance,
            'current_balance'  => $newBalance,
            'remarks'          => 'Leave Request Approved (ID: ' . $leaveRequest->id . ')',
            'created_by'       => $user->id, // Approving manager
        ]);

        $leaveRequest->update([
            'status'            => 'approved',
            'manager_remarks'   => $validated['manager_remarks'],
            'manager_action_at' => now(),
            'updated_by'        => $user->id,
        ]);

        // Send approval notification email to the employee
        $employee = $leaveRequest->employee;
        $employeeEmail = $employee?->email;
        $config = \App\Models\ToolsMaster::first();
        $attendanceNotificationEmail = $config?->attendance_notification_email;

        if ($employeeEmail || $attendanceNotificationEmail) {
            try {
                $toEmail = $employeeEmail ?: $attendanceNotificationEmail;

                // Shared CC list
                $cc = array_filter([$attendanceNotificationEmail]);
                $isUnpaid = ($leaveRequest->leaveType && $leaveRequest->leaveType->paid == 0);
                if ($isUnpaid) {
                    $hrEmail = $config?->hr_email;
                    $managerEmail = $employee?->manager?->email;
                    $cc = array_filter([$hrEmail, $managerEmail, $attendanceNotificationEmail, $user->email]);
                }

                $html = view('emails.attendance_module_template', [
                    'model' => $leaveRequest,
                    'type' => 'leave',
                    'status' => 'approved',
                    'tableData' => [
                        'Employee' => $employee->name,
                        'Status'   => 'Approved',
                        'Dates'    => "{$leaveRequest->start_date->format('d M Y')} to {$leaveRequest->end_date->format('d M Y')}",
                        'Duration' => ucfirst($leaveRequest->duration),
                        'Leave Type' => $leaveRequest->leaveType->leave_name ?? 'None',
                        'Remarks'  => $leaveRequest->remarks ?? '-',
                        'Manager Remarks' => $leaveRequest->manager_remarks ?? '-',
                    ]
                ])->render();

                // 1. Send mail to the employee
                app(\App\Services\MailService::class)->send(
                    $toEmail,
                    new \App\Mail\AttendanceModuleMail($leaveRequest, 'leave', 'approved'),
                    'Leave Request Approved',
                    $html,
                    $cc
                );

                // 2. If unpaid leave, send a separate mail to accounts
                if ($isUnpaid) {
                    $accountsEmail = $config?->accounts_email;
                    if ($accountsEmail) {
                        $htmlAccounts = view('emails.attendance_module_template', [
                            'model' => $leaveRequest,
                            'type' => 'leave',
                            'status' => 'approved_accounts',
                            'tableData' => [
                                'Employee' => $employee->name,
                                'Email' => $employee->email ?? '-',
                                'Leave Type' => ($leaveRequest->leaveType->leave_name ?? 'None') . ' (Unpaid)',
                                'Duration' => ucfirst($leaveRequest->duration),
                                'Start Date' => $leaveRequest->start_date ? $leaveRequest->start_date->format('d M Y') : '-',
                                'End Date' => $leaveRequest->end_date ? $leaveRequest->end_date->format('d M Y') : '-',
                                'Manager Remarks' => $leaveRequest->manager_remarks ?? '-',
                            ]
                        ])->render();

                        app(\App\Services\MailService::class)->send(
                            $accountsEmail,
                            new \App\Mail\AttendanceModuleMail($leaveRequest, 'leave', 'approved_accounts'),
                            'Unpaid Leave Approved - ' . ($employee->name ?? 'Employee'),
                            $htmlAccounts,
                            $cc
                        );
                    }
                }
            } catch (\Throwable $e) {
                return back()->with(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return redirect()
            ->route('leave-requests.manager')
            ->with('success', 'Leave request approved successfully.');
    }

    public function rejected(Request $request, $id)
    {
        $user = Auth::user();

        if (! $user->hasAnyRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized');
        }

        $leaveRequest = $this->getAccessibleLeaveRequest($id, $user);

        if (!$leaveRequest->employee) {
            return back()->with('error', 'Employee record not found.');
        }

        if (strtolower($leaveRequest->status) !== 'submitted') {
            return redirect()
                ->route('leave-requests.manager')
                ->with('error', 'Only submitted leave requests can be rejected.');
        }

        $validated = $request->validate([
            'manager_remarks' => 'required|string|max:1000',
        ]);

        $leaveRequest->update([
            'status'            => 'rejected',
            'manager_remarks'   => $validated['manager_remarks'],
            'manager_action_at' => now(),
            'updated_by'        => $user->id,
        ]);

        // Send rejection notification email to the employee
        $employee = $leaveRequest->employee;
        $employeeEmail = $employee?->email;
        $config = \App\Models\ToolsMaster::first();
        $attendanceNotificationEmail = $config?->attendance_notification_email;

        if ($employeeEmail || $attendanceNotificationEmail) {
            try {
                $toEmail = $employeeEmail ?: $attendanceNotificationEmail;
                $cc = array_filter([$user->email, $employeeEmail ? $attendanceNotificationEmail : null]);

                $html = view('emails.attendance_module_template', [
                    'model' => $leaveRequest,
                    'type' => 'leave',
                    'status' => 'rejected',
                    'tableData' => [
                        'Employee' => $employee->name,
                        'Status'   => 'Rejected',
                        'Dates'    => "{$leaveRequest->start_date->format('d M Y')} to {$leaveRequest->end_date->format('d M Y')}",
                        'Duration' => ucfirst($leaveRequest->duration),
                        'Leave Type' => $leaveRequest->leaveType->leave_name ?? 'None',
                        'Remarks'  => $leaveRequest->remarks ?? '-',
                        'Manager Remarks' => $leaveRequest->manager_remarks ?? '-',
                    ]
                ])->render();

                app(\App\Services\MailService::class)->send(
                    $toEmail,
                    new \App\Mail\AttendanceModuleMail($leaveRequest, 'leave', 'rejected'),
                    'Leave Request Rejected',
                    $html,
                    $cc
                );
            } catch (\Throwable $e) {
                return back()->with(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return redirect()
            ->route('leave-requests.manager')
            ->with('success', 'Leave request rejected successfully.');
    }

    protected function getAccessibleLeaveRequest($id, $user)
    {
        $query = LeaveRequest::with([
            'leaveType',
            'employee',
        ])
            ->where('id', $id);

        // Managers can approve only their employees' requests; Admin can approve all
        if (! $user->hasRole('admin')) {
            $query->whereHas('employee', function ($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        }

        return $query->firstOrFail();
    }

    protected function initializeBalancesForUser($userId)
    {
        $leaveTypes = LeaveType::where('status', 'active')->get();
        foreach ($leaveTypes as $type) {
            $exists = LeaveBalance::where('leave_type_id', $type->id)
                ->where('user_id', $userId)
                ->exists();

            if (!$exists) {
                $balanceRecord = LeaveBalance::create([
                    'leave_type_id' => $type->id,
                    'user_id'       => $userId,
                    'balance'       => $type->accrual,
                    'created_by'    => $userId,
                    'updated_by'    => $userId,
                ]);

                if ($type->accrual > 0) {
                    // Log initial credit
                    LeaveTransaction::create([
                        'user_id'          => $userId,
                        'leave_type_id'    => $type->id,
                        'transaction_type' => 'credit',
                        'amount'           => $type->accrual,
                        'previous_balance' => 0.00,
                        'current_balance'  => $type->accrual,
                        'remarks'          => 'Initial Accrual on Account Activation',
                        'created_by'       => 1, // System admin
                    ]);
                }
            }
        }
    }
}
