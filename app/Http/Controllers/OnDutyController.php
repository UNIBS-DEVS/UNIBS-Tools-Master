<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OnDuty;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OnDutyController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = OnDuty::with('employee')->where('user_id', Auth::id());

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
            'onduty.index',
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
            'onduty.create',
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
            'start_time' => [
                'required',
                'date_format:H:i',
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
            ],
            'reason' => [
                'required',
                'string',
                'max:500',
            ],
        ]);

        try {
            $startDateTime = $request->date . ' ' . $request->start_time . ':00';
            $endDateTime = $request->date . ' ' . $request->end_time . ':00';

            $onDuty = OnDuty::create([
                'user_id' => $employee->id,
                'date' => $request->date,
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
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
                        'model' => $onDuty,
                        'type' => 'onduty',
                        'status' => 'submitted',
                        'tableData' => [
                            'Employee' => $employee->name,
                            'Status'   => 'Submitted',
                            'Date'     => $onDuty->date ? \Carbon\Carbon::parse($onDuty->date)->format('d M Y') : '-',
                            'Hours'    => ($onDuty->start_time && $onDuty->end_time) ? "{$onDuty->start_time->format('h:i A')} to {$onDuty->end_time->format('h:i A')}" : '-',
                            'Reason'   => $onDuty->reason ?? '-',
                        ]
                    ])->render();

                    app(\App\Services\MailService::class)->send(
                        $toEmail,
                        new \App\Mail\AttendanceModuleMail($onDuty, 'onduty', 'submitted'),
                        'New On Duty Application Submitted by ' . $employee->name,
                        $html,
                        $cc
                    );
                } catch (\Throwable $mailEx) {
                    Log::error('On Duty notification email failed: ' . $mailEx->getMessage());
                }
            }

            return redirect()
                ->route('onduty.index')
                ->with(
                    'success',
                    'On Duty request submitted successfully.'
                );
        } catch (\Exception $e) {
            Log::error(
                'On Duty request failed: ' .
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
        $onduty = OnDuty::findOrFail($id);

        if ($onduty->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (strtolower($onduty->status) !== 'rejected') {
            return redirect()
                ->route('onduty.index')
                ->with('error', 'Only rejected On Duty requests can be edited.');
        }

        return view('onduty.edit', compact('onduty'));
    }

    public function update(Request $request, $id)
    {
        $onduty = OnDuty::findOrFail($id);

        if ($onduty->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (strtolower($onduty->status) !== 'rejected') {
            return redirect()
                ->route('onduty.index')
                ->with('error', 'Only rejected On Duty requests can be updated.');
        }

        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $startDateTime = $request->date . ' ' . $request->start_time . ':00';
            $endDateTime = $request->date . ' ' . $request->end_time . ':00';

            $onduty->update([
                'date' => $request->date,
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'reason' => $request->reason,
                'status' => 'submitted',
                'updated_by' => Auth::id(),
            ]);

            return redirect()
                ->route('onduty.index')
                ->with('success', 'On Duty request updated and resubmitted successfully.');
        } catch (\Exception $e) {
            Log::error('On Duty request update failed: ' . $e->getMessage());

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

        $query = OnDuty::with(['employee']);

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

        return view('onduty.requests', compact('requests', 'status'));
    }

    public function managerProcess(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['manager', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $onduty = OnDuty::findOrFail($id);

        if (!$user->hasRole(['admin'])) {
            $onduty->load('employee');
            if ($onduty->employee?->manager_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'manager_remarks' => 'required|string|max:500',
        ]);

        try {
            $onduty->update([
                'status' => $request->status,
                'manager_remarks' => $request->manager_remarks,
                'manager_action_at' => now(),
                'updated_by' => $user->id,
            ]);

            // Send notification email to the employee
            $employee = $onduty->employee;
            $employeeEmail = $employee?->email;
            $config = \App\Models\ToolsMaster::first();
            $attendanceNotificationEmail = $config?->attendance_notification_email;

            if ($employeeEmail || $attendanceNotificationEmail) {
                try {
                    $toEmail = $employeeEmail ?: $attendanceNotificationEmail;
                    $cc = array_filter([$user->email, $employeeEmail ? $attendanceNotificationEmail : null]);

                    $isApproved = (strtolower($request->status) === 'approved');
                    $status = $isApproved ? 'approved' : 'rejected';
                    $subject = $isApproved ? 'On Duty Request Approved' : 'On Duty Request Rejected';
                    $mailable = new \App\Mail\AttendanceModuleMail($onduty, 'onduty', $status);

                    $html = view('emails.attendance_module_template', [
                        'model' => $onduty,
                        'type' => 'onduty',
                        'status' => $status,
                        'tableData' => [
                            'Employee' => $employee->name,
                            'Status'   => ucfirst($status),
                            'Date'     => $onduty->date ? \Carbon\Carbon::parse($onduty->date)->format('d M Y') : '-',
                            'Hours'    => ($onduty->start_time && $onduty->end_time) ? "{$onduty->start_time->format('h:i A')} to {$onduty->end_time->format('h:i A')}" : '-',
                            'Reason'   => $onduty->reason ?? '-',
                            'Manager Remarks' => $onduty->manager_remarks ?? '-',
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
                    Log::error('On Duty manager processing notification email failed: ' . $mailEx->getMessage());
                }
            }

            $message = 'On Duty request ' . $request->status . ' successfully.';

            return redirect()
                ->route('manager.onduty.requests')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('On Duty request manager process failed: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }
}
