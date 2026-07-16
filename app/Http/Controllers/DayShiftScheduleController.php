<?php

namespace App\Http\Controllers;

use App\Models\DayShiftSchedule;
use App\Models\ShiftSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DayShiftScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403, 'Unauthorized');
        }

        $shift = null;

        $query = DayShiftSchedule::with([
            'shiftSchedule',
            'creator',
            'updater',
        ]);

        if ($request->filled('shift_schedule_id')) {

            $shift = ShiftSchedule::findOrFail(
                $request->shift_schedule_id
            );

            $query->where(
                'shift_schedule_id',
                $shift->id
            );
        }

        $dayshifts = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'day_shift_schedules.index',
            [
                'dayshifts' => $dayshifts,
                'shift'     => $shift,
                'shiftId'   => $shift?->id,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403, 'Unauthorized');
        }

        $shift = ShiftSchedule::findOrFail(
            $request->shift_schedule_id
        );

        return view(
            'day_shift_schedules.create',
            compact('shift')
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'shift_schedule_id' => 'required|exists:shift_schedules,id',
            'day' => [
                'required',
                \Illuminate\Validation\Rule::unique('day_shift_schedules')
                    ->where(function ($query) use ($request) {
                        return $query->where(
                            'shift_schedule_id',
                            $request->shift_schedule_id
                        );
                    }),
            ],
            'start_time' => 'required',
            'end_time' => 'required',
            'grace_minutes' => 'required|numeric|min:0',
        ], [
            '*.required' => 'This field is required.',
            'day.unique' => 'This day already exists for the selected shift schedule.',
        ]);


        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        DayShiftSchedule::create($validated);

        return redirect()
            ->route('day-shift-schedule.index', [
                'shift_schedule_id' => $validated['shift_schedule_id']
            ])
            ->with('success', 'Day Shift Schedule created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403, 'Unauthorized');
        }

        $dayshift = DayShiftSchedule::with(
            'shiftSchedule'
        )->findOrFail($id);

        return view(
            'day_shift_schedules.edit',
            compact('dayshift')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'shift_schedule_id' => 'required|exists:shift_schedules,id',
            'day' => [
                'required',
                \Illuminate\Validation\Rule::unique('day_shift_schedules')
                    ->ignore($id)
                    ->where(function ($query) use ($request) {

                        return $query->where(
                            'shift_schedule_id',
                            $request->shift_schedule_id
                        );
                    }),
            ],
            'start_time' => 'required',
            'end_time' => 'required',
            'grace_minutes' => 'required|numeric|min:0',
        ], [
            'day.unique' => 'This day already exists for the selected shift schedule.',
            '*.required' => 'This field is required.',
        ]);

        $dayshift = DayShiftSchedule::findOrFail($id);

        $validated['updated_by'] = Auth::id();

        $dayshift->update($validated);

        return redirect()
            ->route('day-shift-schedule.index', [
                'shift_schedule_id' => $validated['shift_schedule_id']
            ])
            ->with('success', 'Day Shift Schedule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dayshift = DayShiftSchedule::findOrFail($id);

        $shiftScheduleId = $dayshift->shift_schedule_id;

        $dayshift->delete();

        return redirect()
            ->route('day-shift-schedule.index', [
                'shift_schedule_id' => $shiftScheduleId
            ])
            ->with('success', 'Day Shift Schedule deleted successfully.');
    }
}
