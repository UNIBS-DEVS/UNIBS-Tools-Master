<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompOff;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CompOffController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = CompOff::with('employee')->where('user_id', Auth::id());

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
            'compoff.index',
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
            'compoff.create',
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

            'day_worked' => [
                'required',
                'date',
            ],

            'reason' => [
                'required',
                'string',
                'max:500',
            ],

            'action' => [
                'nullable',
                'in:draft,submit',
            ],

        ]);

        try {

            $status = $request->action == 'draft'
                ? 'Draft'
                : 'Pending';

            $compOff = CompOff::create([
                'user_id' => $employee->id,
                'day_worked' => $request->day_worked,
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
                        'model' => $compOff,
                        'type' => 'compoff',
                        'status' => 'submitted',
                        'tableData' => [
                            'Employee' => $employee->name,
                            'Status'   => 'Submitted',
                            'Day Worked' => $compOff->day_worked ? \Carbon\Carbon::parse($compOff->day_worked)->format('d M Y') : '-',
                            'Reason'   => $compOff->reason ?? '-',
                        ]
                    ])->render();

                    app(\App\Services\MailService::class)->send(
                        $toEmail,
                        new \App\Mail\AttendanceModuleMail($compOff, 'compoff', 'submitted'),
                        'New Comp Off Application Submitted by ' . $employee->name,
                        $html,
                        $cc
                    );
                } catch (\Throwable $mailEx) {
                    Log::error('Comp Off notification email failed: ' . $mailEx->getMessage());
                }
            }

            return redirect()
                ->route('compoff.index')
                ->with(
                    'success',
                    'Comp Off request submitted successfully.'
                );
        } catch (\Exception $e) {
            Log::error(
                'Comp Off request failed: ' .
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
        $compOff = CompOff::findOrFail($id);

        if ($compOff->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (strtolower($compOff->status) !== 'rejected') {
            return redirect()
                ->route('compoff.index')
                ->with('error', 'Only rejected Comp Off requests can be edited.');
        }

        return view('compoff.edit', compact('compOff'));
    }

    public function update(Request $request, $id)
    {
        $compOff = CompOff::findOrFail($id);

        if ($compOff->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (strtolower($compOff->status) !== 'rejected') {
            return redirect()
                ->route('compoff.index')
                ->with('error', 'Only rejected Comp Off requests can be updated.');
        }

        $request->validate([
            'day_worked' => 'required|date',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $compOff->update([
                'day_worked' => $request->day_worked,
                'reason' => $request->reason,
                'status' => 'submitted',
                'updated_by' => Auth::id(),
            ]);

            return redirect()
                ->route('compoff.index')
                ->with('success', 'Comp Off request updated and resubmitted successfully.');
        } catch (\Exception $e) {
            Log::error('Comp Off request update failed: ' . $e->getMessage());

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

        $query = CompOff::with(['employee']);

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

        return view('compoff.requests', compact('requests', 'status'));
    }

    public function managerProcess(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['manager', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $compOff = CompOff::findOrFail($id);

        // Verify manager assignment if not admin
        if (!$user->hasRole(['admin'])) {
            $compOff->load('employee');
            if ($compOff->employee?->manager_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'manager_remarks' => 'required|string|max:500',
        ]);

        try {
            $compOff->update([
                'status' => $request->status,
                'manager_remarks' => $request->manager_remarks ?? '',
                'manager_action_at' => now(),
                'updated_by' => $user->id,
            ]);

            // Send notification email to the employee
            $employee = $compOff->employee;
            $employeeEmail = $employee?->email;
            $config = \App\Models\ToolsMaster::first();
            $attendanceNotificationEmail = $config?->attendance_notification_email;

            if ($employeeEmail || $attendanceNotificationEmail) {
                try {
                    $toEmail = $employeeEmail ?: $attendanceNotificationEmail;
                    $cc = array_filter([$user->email, $employeeEmail ? $attendanceNotificationEmail : null]);

                    $isApproved = (strtolower($request->status) === 'approved');
                    $status = $isApproved ? 'approved' : 'rejected';
                    $subject = $isApproved ? 'Comp Off Request Approved' : 'Comp Off Request Rejected';
                    $mailable = new \App\Mail\AttendanceModuleMail($compOff, 'compoff', $status);

                    $html = view('emails.attendance_module_template', [
                        'model' => $compOff,
                        'type' => 'compoff',
                        'status' => $status,
                        'tableData' => [
                            'Employee' => $employee->name,
                            'Status'   => ucfirst($status),
                            'Day Worked' => $compOff->day_worked ? \Carbon\Carbon::parse($compOff->day_worked)->format('d M Y') : '-',
                            'Reason'   => $compOff->reason ?? '-',
                            'Manager Remarks' => $compOff->manager_remarks ?? '-',
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
                    Log::error('Comp Off manager processing notification email failed: ' . $mailEx->getMessage());
                }
            }

            $message = 'Comp Off request ' . $request->status . ' successfully.';

            return redirect()
                ->route('manager.compoff.requests')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Comp Off request manager process failed: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }
}
