<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLocation;
use App\Models\ShiftSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceLocationController extends Controller
{
    public function index()
    {
        $locations = AttendanceLocation::with('shiftSchedule')
            ->latest()
            ->paginate(20);

        return view(
            'attendance_locations.index',
            compact('locations')
        );
    }

    public function create()
    {
        $shiftSchedules = ShiftSchedule::get();


        return view('attendance_locations.create', compact('shiftSchedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_name' => 'required|max:255',
            'type' => 'required|in:office,home',
            'shift_schedule_id' => 'required|exists:shift_schedules,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:1',

        ]);

        AttendanceLocation::create([
            'location_name' => $request->location_name,
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'is_active' => $request->has('is_active'),
            'shift_schedule_id' => $request->shift_schedule_id,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);


        return redirect()
            ->route('attendance-locations.index')
            ->with('success', 'Location created successfully.');
    }

    public function edit(AttendanceLocation $attendanceLocation)
    {
        $shiftSchedules = ShiftSchedule::all();

        // dd($shiftSchedules);

        return view(
            'attendance_locations.edit',
            compact(
                'attendanceLocation',
                'shiftSchedules',
            )
        );
    }

    public function update(Request $request, AttendanceLocation $attendanceLocation)
    {
        $request->validate([
            'location_name' => 'required|max:255',
            'type' => 'required|in:office,home',
            'shift_schedule_id' => 'required|exists:shift_schedules,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:1',

        ]);

        $attendanceLocation->update([
            'location_name' => $request->location_name,
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'is_active' => $request->has('is_active'),
            'shift_schedule_id' => $request->shift_schedule_id,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('attendance-locations.index')
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(AttendanceLocation $attendanceLocation)
    {
        $attendanceLocation->delete();

        return redirect()
            ->route('attendance-locations.index')
            ->with('success', 'Location deleted successfully.');
    }
}
