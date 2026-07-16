<?php

namespace App\Http\Controllers\AttendanceAPIs;

use App\Http\Controllers\Controller;
use App\Mail\AttendanceModuleMail;
use App\Models\Attendance;
use App\Models\ToolsMaster;
use App\Models\User;
use App\Models\UserAttendanceLocation;
use App\Services\MailService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AttendanceController extends Controller
{
    public function userLocations($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $locations = UserAttendanceLocation::with([
            'attendanceLocation.shiftSchedule.daySchedules'
        ])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->get()
            ->map(function ($item) {

                $location = $item->attendanceLocation;
                $shiftSchedule = $location->shiftSchedule;

                return [
                    'location_id' => $location->id,
                    'location_name' => $location->location_name,
                    'type' => $location->type,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'radius' => $location->radius,
                    'is_active' => $location->is_active,

                    'shift_schedule' => [
                        'id' => $shiftSchedule?->id,
                        'name' => $shiftSchedule?->shift_schedule_name,

                        'day_shift_schedule' => $shiftSchedule
                            ? $shiftSchedule->daySchedules->map(function ($day) {
                                return [
                                    'day' => $day->day,
                                    'start_time' => $day->start_time,
                                    'end_time' => $day->end_time,
                                    'grace_minutes' => $day->grace_minutes,
                                ];
                            })->values()
                            : [],
                    ],
                ];
            });

        if ($locations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No attendance locations assigned.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Locations fetched successfully.',
            'data' => $locations
        ]);
    }

    public function userPunch(Request $request, MailService $mailService)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'location_id' => 'required|exists:attendance_locations,id',
            'punch_type' => 'required|in:in,out',
            'punch_source' => 'required',
            'status' => 'required',
            'remarks' => 'nullable|string',
            'punch_at' => 'nullable|date',
        ]);

        // Check if punch out request
        if ($request->punch_type === 'out') {

            $punchAt = $request->filled('punch_at')
                ? \Carbon\Carbon::parse($request->punch_at)
                : now();

            $lastPunch = Attendance::where('user_id', $request->user_id)
                ->whereDate('punch_at', $punchAt->toDateString())
                ->orderByDesc('punch_at')
                ->orderByDesc('id')
                ->first();

            // No active punch in found
            if (!$lastPunch || $lastPunch->punch_type === 'out') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already punched out or no active punch-in found.'
                ], 200);
            }

            // Auto/manual punch out remarks
            $request->merge([
                'remarks' => $request->remarks ?? 'Punch out successful.',
            ]);
        }

        $punchAt = $request->filled('punch_at')
            ? \Carbon\Carbon::parse($request->punch_at)
            : now();

        $attendance = Attendance::create([
            'user_id' => $request->user_id,
            'attendance_location_id' => $request->location_id,
            'attendance_date' => $punchAt->toDateString(),
            'punch_at' => $punchAt,
            'punch_type' => $request->punch_type,
            'punch_source' => $request->punch_source,
            'status' => $request->status,
            'remarks' => $request->remarks,
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
        ]);

        $attendance->load([
            'user.manager',
            'attendanceLocation'
        ]);

        try {

            $config = ToolsMaster::first();

            $managerEmail = $attendance->user?->manager?->email;
            $userEmail = $attendance->user?->email;

            if ($managerEmail) {

                $cc = array_filter([
                    $config?->attendance_notification_email,
                    $userEmail,
                ]);

                $mailService->send(
                    $managerEmail,
                    new AttendanceModuleMail($attendance, 'punch', 'punch'),
                    null,
                    null,
                    $cc
                );
            }
        } catch (Exception $e) {

            Log::error('Attendance Email Failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $request->punch_type === 'out'
                ? 'Punch out successful.'
                : 'Punch in successful.',
            'data' => $attendance,
        ]);
    }

    public function getUserPunches(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $query = Attendance::with([
            'attendanceLocation',
            'user'
        ])->where('user_id', $request->user_id);

        if ($request->filled('start_date') && $request->filled('end_date')) {

            $query->whereBetween('punch_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        } else {

            $query->whereDate('punch_at', today());
        }

        $records = $query->orderByDesc('punch_at')
            ->orderByDesc('id')
            ->get()
            ->map(function ($attendance) {

                return [
                    'id' => $attendance->id,
                    'user_id' => $attendance->user_id,
                    'employee_name' => $attendance->user?->name,
                    'attendance_date' => optional($attendance->attendance_date)->format('Y-m-d'),
                    'punch_at' => optional($attendance->punch_at)->format('Y-m-d H:i:s'),
                    'punch_type' => $attendance->punch_type,
                    'punch_source' => $attendance->punch_source,
                    'status' => $attendance->status,
                    'remarks' => $attendance->remarks,
                    'location' => [
                        'id' => $attendance->attendanceLocation?->id,
                        'location_name' => $attendance->attendanceLocation?->location_name,
                        'type' => $attendance->attendanceLocation?->type,
                    ],
                ];
            });

        if ($records->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No attendance records found.',
                'punch_count' => 0,
                'data' => []
            ], 404);
        }

        $response = [
            'success' => true,
            'message' => 'Attendance records fetched successfully.',
            'punch_count' => $records->count(),
            'data' => $records,
        ];

        if ($request->boolean('punch_count')) {
            unset($response['data']);
        }

        return response()->json($response);
    }
}
