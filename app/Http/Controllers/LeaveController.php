<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leaves = Leave::latest()->get();

        return view('leave.index', compact('leaves'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('leave.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required',
            'duration'   => 'required',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
        ]);

        Leave::create([
            'leave_type' => $request->leave_type,
            'duration'   => $request->duration,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'remarks'    => $request->remarks,
        ]);

        return redirect()
            ->route('leave.index')
            ->with('success', 'Leave created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Leave $leave)
    {
        return view('leave.show', compact('leave'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
{
    $leave = Leave::findOrFail($id);

    return view('leave.edit', compact('leave'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Leave $leave)
    {
        $request->validate([
            'leave_type' => 'required',
            'duration'   => 'required',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
        ]);

        $leave->update([
            'leave_type' => $request->leave_type,
            'duration'   => $request->duration,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'remarks'    => $request->remarks,
        ]);

        return redirect()
            ->route('leave.index')
            ->with('success', 'Leave updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Leave $leave)
    {
        $leave->delete();

        return redirect()
            ->route('leave.index')
            ->with('success', 'Leave deleted successfully.');
    }
}