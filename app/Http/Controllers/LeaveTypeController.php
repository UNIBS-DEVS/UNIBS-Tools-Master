<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leaveTypes = LeaveType::latest()->get();

        return view('leave_types.index', compact('leaveTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('leave_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_name'   => 'required|string|max:50',
            'accrual_type' => 'required|string',
            'accrual'      => 'required|numeric',
            'max_balance'  => 'required|numeric',
            'status'       => 'required|in:active,inactive',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        LeaveType::create($validated);

        return redirect()
            ->route('leave-types.index')
            ->with('success', 'Leave Type created successfully.');
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
        $leaveType = LeaveType::findOrFail($id);

        return view('leave_types.edit', compact('leaveType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'leave_name'   => 'required|string|max:50',
            'accrual_type' => 'required|string',
            'accrual'      => 'required|numeric',
            'max_balance'  => 'required|numeric',
            'status'       => 'required|in:active,inactive',
        ]);

        $leaveType = LeaveType::findOrFail($id);

        $validated['updated_by'] = Auth::id();

        $leaveType->update($validated);

        return redirect()
            ->route('leave-types.index')
            ->with('success', 'Leave Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $leaveType = LeaveType::findOrFail($id);

        $leaveType->delete();

        return redirect()
            ->route('leave-types.index')
            ->with('success', 'Leave Type deleted successfully.');
    }
}
