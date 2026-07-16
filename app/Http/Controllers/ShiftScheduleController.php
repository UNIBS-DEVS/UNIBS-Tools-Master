<?php

namespace App\Http\Controllers;

use App\Models\ShiftSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403, 'Unauthorized');
        }

        $shifts = ShiftSchedule::latest()->get();

        return view('shift_schedules.index', compact('shifts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403, 'Unauthorized');
        }

        return view('shift_schedules.create');
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'shift_schedule' => 'required|string|max:255',
        ], [
            '*.required' => 'This field is required.',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        ShiftSchedule::create($validated);

        return redirect()
            ->route('shift-schedule.index')
            ->with('success', 'Shift Schedule created successfully.');
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(ShiftSchedule $shiftSchedule)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403, 'Unauthorized');
        }

        return view('shift_schedules.edit', compact('shiftSchedule'));
    }

    /**
     * Update the resource.
     */
    public function update(Request $request, ShiftSchedule $shiftSchedule)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'shift_schedule' => 'required|string|max:255',
        ], [
            '*.required' => 'This field is required.',
        ]);
 
        $validated['updated_by'] = Auth::id(); 

        $shiftSchedule->update($validated);

        return redirect()
            ->route('shift-schedule.index')
            ->with('success', 'Shift Schedule updated successfully.');
    }

    /**
     * Remove the resource.
     */
    public function destroy(ShiftSchedule $shiftSchedule)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403, 'Unauthorized');
        }

        $shiftSchedule->delete();

        return redirect()
            ->route('shift-schedule.index')
            ->with('success', 'Shift Schedule deleted successfully.');
    }
}
