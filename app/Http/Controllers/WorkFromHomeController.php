<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkFromHome;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WorkFromHomeController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = WorkFromHome::with('employee')->where('user_id', Auth::id());

        if ($status && $status !== 'all') {
            $statusLower = strtolower($status);
            if ($statusLower === 'pending' || $statusLower === 'submitted') {
                $query->whereIn('status', ['submitted', 'Pending', 'pending']);
            } else {
                $query->whereIn('status', [$statusLower, ucfirst($statusLower)]);
            }
        }

        $requests = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'wfh.index',
            compact(
                'requests',
                'status'
            )
        );
    }

    public function create(Request $request)
    {
        $employees = User::orderBy('name')->get();
        return view(
            'wfh.create',
            compact('employees')
        );
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $employee = Auth::user();

        $request->validate([
            'date' => [
                'required',
                'date',
            ],
            'type' => [
                'required',
                'in:fullday,halfday- first,halfday- second',
            ],
            'reason' => [
                'required',
                'string',
                'max:500',
            ],
        ]);

        try {
            $wfhRequest = WorkFromHome::create([
                'user_id' => $employee->id,
                'date' => $request->date,
                'type' => $request->type,
                'reason' => $request->reason,
                'status' => 'submitted',
                'created_by' => $employee->id,
                'updated_by' => $employee->id,
            ]);

            // Send notification email to manager
            $manager = $employee->manager;
            $config = \App\Models\ToolsMaster::first();
            $attendanceNotificationEmail = $config?->attendance_notification_email;
            $managerEmail = $manager?->email;

            if ($managerEmail || $attendanceNotificationEmail) {
                try {
                    $toEmail = $managerEmail ?: $attendanceNotificationEmail;
                    $cc = array_filter([$employee->email, $managerEmail ? $attendanceNotificationEmail : null]);

                    $html = view('emails.attendance_module_template', [
                        'model' => $wfhRequest,
                        'type' => 'wfh',
                        'status' => 'submitted',
                        'tableData' => [
                            'Employee' => $employee->name,
                            'Status'   => 'Submitted',
                            'Date'     => $wfhRequest->date ? \Carbon\Carbon::parse($wfhRequest->date)->format('d M Y') : '-',
                            'Shift Type' => ucfirst(str_replace('-', ' ', $wfhRequest->type)),
                            'Reason'   => $wfhRequest->reason ?? '-',
                        ]
                    ])->render();

                    app(\App\Services\MailService::class)->send(
                        $toEmail,
                        new \App\Mail\AttendanceModuleMail($wfhRequest, 'wfh', 'submitted'),
                        'New WFH Application Submitted by ' . $employee->name,
                        $html,
                        $cc
                    );
                } catch (\Throwable $mailEx) {
                    Log::error('WFH notification email failed: ' . $mailEx->getMessage());
                }
            }

            return redirect()
                ->route('wfh.index')
                ->with(
                    'success',
                    'Work From Home request submitted successfully.'
                );
        } catch (\Exception $e) {
            Log::error(
                'WFH request failed: ' .
                    $e->getMessage()
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    public function edit($id)
    {
        $wfh = WorkFromHome::findOrFail($id);

        if ($wfh->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (strtolower($wfh->status) !== 'rejected') {
            return redirect()
                ->route('wfh.index')
                ->with('error', 'Only rejected WFH requests can be edited.');
        }

        return view('wfh.edit', compact('wfh'));
    }

    public function update(Request $request, $id)
    {
        $wfh = WorkFromHome::findOrFail($id);

        if ($wfh->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (strtolower($wfh->status) !== 'rejected') {
            return redirect()
                ->route('wfh.index')
                ->with('error', 'Only rejected WFH requests can be updated.');
        }

        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:fullday,halfday- first,halfday- second',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $wfh->update([
                'date' => $request->date,
                'type' => $request->type,
                'reason' => $request->reason,
                'status' => 'submitted',
                'updated_by' => Auth::id(),
            ]);

            return redirect()
                ->route('wfh.index')
                ->with('success', 'Work From Home request updated and resubmitted successfully.');
        } catch (\Exception $e) {
            Log::error('WFH request update failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function managerRequests(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['manager', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $status = $request->get('status', 'submitted');

        $query = WorkFromHome::with(['employee']);

        if (!$user->hasRole(['admin'])) {
            $query->whereHas('employee', function ($employeeQuery) use ($user) {
                $employeeQuery->where('manager_id', $user->id);
            });
        }

        if ($status && $status !== 'all') {
            $statusLower = strtolower($status);
            if ($statusLower === 'pending' || $statusLower === 'submitted') {
                $query->whereIn('status', ['submitted', 'Pending', 'pending']);
            } else {
                $query->whereIn('status', [$statusLower, ucfirst($statusLower)]);
            }
        }

        $requests = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('wfh.requests', compact('requests', 'status'));
    }

    public function managerProcess(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['manager', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $wfh = WorkFromHome::findOrFail($id);

        if (!$user->hasRole(['admin'])) {
            $wfh->load('employee');
            if ($wfh->employee?->manager_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'manager_remarks' => 'required|string|max:500',
        ]);

        try {
            $wfh->update([
                'status' => $request->status,
                'manager_remarks' => $request->manager_remarks,
                'manager_action_at' => now(),
                'updated_by' => $user->id,
            ]);

            // Send notification email to the employee
            $employee = $wfh->employee;
            $employeeEmail = $employee?->email;
            $config = \App\Models\ToolsMaster::first();
            $attendanceNotificationEmail = $config?->attendance_notification_email;

            if ($employeeEmail || $attendanceNotificationEmail) {
                try {
                    $toEmail = $employeeEmail ?: $attendanceNotificationEmail;
                    $cc = array_filter([$user->email, $employeeEmail ? $attendanceNotificationEmail : null]);

                    $isApproved = (strtolower($request->status) === 'approved');
                    $status = $isApproved ? 'approved' : 'rejected';
                    $subject = $isApproved ? 'WFH Request Approved' : 'WFH Request Rejected';
                    $mailable = new \App\Mail\AttendanceModuleMail($wfh, 'wfh', $status);

                    $html = view('emails.attendance_module_template', [
                        'model' => $wfh,
                        'type' => 'wfh',
                        'status' => $status,
                        'tableData' => [
                            'Employee' => $employee->name,
                            'Status'   => ucfirst($status),
                            'Date'     => $wfh->date ? \Carbon\Carbon::parse($wfh->date)->format('d M Y') : '-',
                            'Shift Type' => ucfirst(str_replace('-', ' ', $wfh->type)),
                            'Reason'   => $wfh->reason ?? '-',
                            'Manager Remarks' => $wfh->manager_remarks ?? '-',
                        ]
                    ])->render();

                    app(\App\Services\MailService::class)->send(
                        $toEmail,
                        $mailable,
                        $subject,
                        $html,
                        $cc
                    );
                } catch (\Throwable $mailEx) {
                    Log::error('WFH manager processing notification email failed: ' . $mailEx->getMessage());
                }
            }

            $message = 'Work From Home request ' . $request->status . ' successfully.';

            return redirect()
                ->route('manager.wfh.requests')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('WFH request manager process failed: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }
}
