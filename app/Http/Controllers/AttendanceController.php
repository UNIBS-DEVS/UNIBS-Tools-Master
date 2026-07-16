<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\UserAttendanceLocation;
use App\Models\AttendanceLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // 1. Fetch user's assigned active locations for the dropdown
        $userLocations = UserAttendanceLocation::with('attendanceLocation')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->get();

        // 2. Today's punches for current status check
        $todayPunches = Attendance::with('attendanceLocation')
            ->where('user_id', $userId)
            ->whereDate('attendance_date', today())
            ->orderBy('punch_at')
            ->get();

        $lastPunch = $todayPunches->last();

        // 3. Table 1: Last 7 days punch records (auto_approved or approved)
        $last7DaysPunches = Attendance::with('attendanceLocation')
            ->where('user_id', $userId)
            ->where('attendance_date', '>=', today()->subDays(7))
            ->whereIn('status', ['auto_approved', 'approved'])
            ->orderBy('punch_at', 'desc')
            ->get();

        // 4. Table 2: My punch requests (pending, approved, or rejected - not auto_approved)
        $punchRequests = Attendance::with('attendanceLocation')
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->orderBy('punch_at', 'desc')
            ->get();

        return view(
            'attendance.index',
            compact('userLocations', 'todayPunches', 'lastPunch', 'last7DaysPunches', 'punchRequests')
        );
    }

    public function create()
    {
        // dd('AttendanceController@create');
        $userId = auth()->id();

        $locations = UserAttendanceLocation::with('attendanceLocation')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->get()
            ->pluck('attendanceLocation')
            ->filter(function ($location) {
                return $location && $location->is_active;
            })
            ->values();

        return view('attendance.create', compact('locations'));
    }

    public function punch(Request $request)
    {
        $request->validate([
            'attendance_date'        => 'required|date|before_or_equal:today',
            'punch_time'             => 'required|string',
            'punch_type'             => 'required|in:in,out',
            'attendance_location_id' => 'required|exists:attendance_locations,id',
            // 'remarks'                => 'required|string|max:500',
        ]);

        $userId = auth()->id();
        $locationId = $request->attendance_location_id;

        // Verify that this location is assigned to the user
        $userLocation = UserAttendanceLocation::with('attendanceLocation')
            ->where('user_id', $userId)
            ->where('attendance_location_id', $locationId)
            ->where('status', 'active')
            ->first();

        if (!$userLocation) {
            return back()->with(
                'error',
                'The selected attendance location is not assigned to you.'
            );
        }

        $location = $userLocation->attendanceLocation;
        if (!$location || !$location->is_active) {
            return back()->with(
                'error',
                'The selected attendance location is inactive.'
            );
        }

        $punchAt = \Carbon\Carbon::parse($request->attendance_date . ' ' . $request->punch_time);

        // Prevent future punch times (allowing 24 hours buffer for timezone differences)
        if ($punchAt->gt(now()->addHours(24))) {
            return back()->with(
                'error',
                'You cannot request a punch time in the future.'
            );
        }

        $punchType = $request->punch_type;

        // Create the manual request punch with status = 'pending'
        $attendance = Attendance::create([
            'user_id'                => $userId,
            'attendance_location_id' => $location->id,
            'attendance_date'        => $request->attendance_date,
            'punch_at'               => $punchAt,
            'punch_type'             => $punchType,
            'punch_source'           => 'Manual',
            'status'                 => 'pending',
            'remarks'                => $request->remarks,
            'created_by'             => $userId,
        ]);

        // Send notification email to manager
        $employee = auth()->user();
        $manager = $employee->manager;
        $config = \App\Models\ToolsMaster::first();
        $attendanceNotificationEmail = $config?->attendance_notification_email;
        $managerEmail = $manager?->email;

        if ($managerEmail || $attendanceNotificationEmail) {
            try {
                $toEmail = $managerEmail ?: $attendanceNotificationEmail;
                $cc = array_filter([$employee->email, $managerEmail ? $attendanceNotificationEmail : null]);

                $attendance->load(['user.manager', 'attendanceLocation']);

                $html = view('emails.attendance_module_template', [
                    'model' => $attendance,
                    'type' => 'manual_punch',
                    'status' => 'submitted',
                    'tableData' => [
                        'Employee' => $employee->name,
                        'Status'   => 'Submitted (Pending review)',
                        'Date'     => $attendance->attendance_date ? \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') : '-',
                        'Punch Time' => $attendance->punch_at ? \Carbon\Carbon::parse($attendance->punch_at)->format('h:i A') : '-',
                        'Punch Type' => strtoupper($attendance->punch_type ?? '-'),
                        'Punch Source' => ucfirst($attendance->punch_source ?? '-'),
                        'Location Name' => $attendance->attendanceLocation?->location_name ?? '-',
                        'Location Type' => ucfirst($attendance->attendanceLocation?->type ?? '-'),
                        'Remarks'  => $attendance->remarks ?? '-',
                    ]
                ])->render();

                app(\App\Services\MailService::class)->send(
                    $toEmail,
                    new \App\Mail\AttendanceModuleMail($attendance, 'manual_punch', 'submitted'),
                    'New Attendance Punch Request Submitted by ' . $employee->name,
                    $html,
                    $cc
                );
            } catch (\Throwable $mailEx) {
                \Illuminate\Support\Facades\Log::error('Attendance punch notification email failed: ' . $mailEx->getMessage());
            }
        }

        return redirect()
            ->route('attendance.index')
            ->with(
                'success',
                'Manual punch ' . ucfirst($punchType) . '  request submitted for manager approval.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Manager Approvals Actions
    |--------------------------------------------------------------------------
    */

    public function managerRequests(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['manager', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $status = $request->get('status', 'pending');

        $query = Attendance::with(['user', 'attendanceLocation']);

        if (!$user->hasRole(['admin'])) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        } else {
            // Show requests (pending/approved/rejected, excluding auto_approved)
            $query->where('status', '!=', 'auto_approved');
        }

        $requests = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('attendance.requests', compact('requests', 'status'));
    }

    public function managerProcess(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['manager', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $attendance = Attendance::findOrFail($id);

        if (!$user->hasRole(['admin'])) {
            $attendance->load('user');
            if ($attendance->user?->manager_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'manager_remarks' => 'required|string|max:500',
        ]);

        try {
            $attendance->update([
                'status' => $request->status,
                'remarks' => $request->manager_remarks,
                'updated_by' => $user->id,
            ]);

            // Send notification email to the employee
            $employee = $attendance->user;
            $employeeEmail = $employee?->email;
            $config = \App\Models\ToolsMaster::first();
            $attendanceNotificationEmail = $config?->attendance_notification_email;

            if ($employeeEmail || $attendanceNotificationEmail) {
                try {
                    $toEmail = $employeeEmail ?: $attendanceNotificationEmail;
                    $manager = $employee?->manager;
                    $cc = array_filter([$manager?->email, $employeeEmail ? $attendanceNotificationEmail : null]);

                    $attendance->load(['user.manager', 'attendanceLocation']);

                    $isApproved = $request->status === 'approved';
                    $status = $isApproved ? 'approved' : 'rejected';
                    $mailable = new \App\Mail\AttendanceModuleMail($attendance, 'manual_punch', $status);

                    $html = view('emails.attendance_module_template', [
                        'model' => $attendance,
                        'type' => 'manual_punch',
                        'status' => $status,
                        'tableData' => [
                            'Employee' => $employee->name,
                            'Status'   => ucfirst($status),
                            'Date'     => $attendance->attendance_date ? \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') : '-',
                            'Punch Time' => $attendance->punch_at ? \Carbon\Carbon::parse($attendance->punch_at)->format('h:i A') : '-',
                            'Punch Type' => strtoupper($attendance->punch_type ?? '-'),
                            'Punch Source' => ucfirst($attendance->punch_source ?? '-'),
                            'Location Name' => $attendance->attendanceLocation?->location_name ?? '-',
                            'Location Type' => ucfirst($attendance->attendanceLocation?->type ?? '-'),
                            'Remarks'  => $attendance->remarks ?? '-',
                            'Manager Remarks' => $attendance->manager_remarks ?? '-',
                        ]
                    ])->render();

                    app(\App\Services\MailService::class)->send(
                        $toEmail,
                        $mailable,
                        'Attendance Punch Request ' . ucfirst($request->status),
                        $html,
                        $cc
                    );
                } catch (\Throwable $mailEx) {
                    Log::error('Attendance punch process notification email failed: ' . $mailEx->getMessage());
                }
            }

            $message = 'Attendance punch request ' . $request->status . ' successfully.';

            return redirect()
                ->route('manager.attendance.requests')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Attendance manager process failed: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    private function distance(
        $lat1,
        $lon1,
        $lat2,
        $lon2
    ) {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a =
            sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon / 2) *
            sin($dLon / 2);

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return $earthRadius * $c;
    }
}
