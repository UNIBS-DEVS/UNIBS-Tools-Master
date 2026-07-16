<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AttendanceLocation;
use App\Models\UserAttendanceLocation;
use Illuminate\Http\Request;

class UserAttendanceLocationController extends Controller
{
    public function index(Request $request)
    {
        $locationId = $request->get('attendance_location_id');

        $location = null;

        $query = UserAttendanceLocation::with([
            'user',
            'attendanceLocation'
        ]);

        if ($locationId) {

            $query->where(
                'attendance_location_id',
                $locationId
            );

            $location = AttendanceLocation::find($locationId);
        }

        $assignments = $query
            ->latest()
            ->paginate(10)
            ->appends($request->all());

        return view(
            'user_attendance_locations.index',
            compact(
                'assignments',
                'location',
                'locationId'
            )
        );
    }

    // public function create(Request $request)
    // {
    //     $users = User::where('status', 'active')
    //         ->orderBy('name')
    //         ->get();

    //     $locations = AttendanceLocation::where('is_active', 1)
    //         ->orderBy('location_name')
    //         ->get();

    //     $selectedLocationId = $request->get('attendance_location_id');

    //     return view(
    //         'user_attendance_locations.create',
    //         compact(
    //             'users',
    //             'locations',
    //             'selectedLocationId'
    //         )
    //     );
    // }

    public function create(Request $request)
    {
        $locationId = $request->get('attendance_location_id');

        $location = AttendanceLocation::findOrFail($locationId);

        $users = User::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view(
            'user_attendance_locations.create',
            compact(
                'users',
                'location'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_location_id' => 'required|exists:attendance_locations,id',
            'status' => 'required|in:active,inactive',
        ]);

        UserAttendanceLocation::create([
            'user_id' => $request->user_id,
            'attendance_location_id' => $request->attendance_location_id,
            'status' => $request->status,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route(
                'user-attendance-locations.index',
                [
                    'attendance_location_id' => $request->attendance_location_id
                ]
            )
            ->with('success', 'Assignment created successfully.');
    }

    // public function edit(UserAttendanceLocation $userAttendanceLocation)
    // {
    //     $users = User::where('status', 'active')->get();

    //     $locations = AttendanceLocation::where('is_active', 1)
    //         ->get();

    //     return view(
    //         'user_attendance_locations.edit',
    //         compact(
    //             'userAttendanceLocation',
    //             'users',
    //             'locations'
    //         )
    //     );
    // }

    public function edit(UserAttendanceLocation $userAttendanceLocation)
    {
        $users = User::where('status', 'active')
            ->orderBy('name')
            ->get();

        $location = $userAttendanceLocation->attendanceLocation;

        return view(
            'user_attendance_locations.edit',
            compact(
                'userAttendanceLocation',
                'users',
                'location'
            )
        );
    }

    public function update(
        Request $request,
        UserAttendanceLocation $userAttendanceLocation
    ) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_location_id' => 'required|exists:attendance_locations,id',
            'status' => 'required|in:active,inactive',
        ]);

        $userAttendanceLocation->update([
            'user_id' => $request->user_id,
            'attendance_location_id' => $request->attendance_location_id,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route(
                'user-attendance-locations.index',
                [
                    'attendance_location_id' => $request->attendance_location_id
                ]
            )
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy(
        UserAttendanceLocation $userAttendanceLocation
    ) {
        $locationId = $userAttendanceLocation->attendance_location_id;

        $userAttendanceLocation->delete();

        return redirect()
            ->route(
                'user-attendance-locations.index',
                [
                    'attendance_location_id' => $locationId
                ]
            )
            ->with('success', 'Assignment deleted successfully.');
    }
}
