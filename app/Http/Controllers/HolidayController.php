<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::latest()->get();

        return view('holidays.index', compact('holidays'));
    }

    public function create()
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        return view('holidays.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'holiday_year' => 'required',
            'holiday_date' => 'required|date',
            'description' => 'required',
            'holiday_type' => 'required'
        ]);

        Holiday::create([
            'holiday_year' => $request->holiday_year,
            'holiday_date' => $request->holiday_date,
            'description' => $request->description,
            'holiday_type' => $request->holiday_type,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('holidays.index')
            ->with('success', 'Holiday Created Successfully');
    }

    public function edit($id)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        $holiday = Holiday::findOrFail($id);

        return view('holidays.edit', compact('holiday'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'holiday_year' => 'required',
            'holiday_date' => 'required|date',
            'description' => 'required',
            'holiday_type' => 'required'
        ]);

        $holiday = Holiday::findOrFail($id);

        $holiday->update([
            'holiday_year' => $request->holiday_year,
            'holiday_date' => $request->holiday_date,
            'description' => $request->description,
            'holiday_type' => $request->holiday_type,
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('holidays.index')
            ->with('success', 'Holiday Updated Successfully');
    }

    public function destroy($id)
    {
        Holiday::findOrFail($id)->delete();

        return redirect()->route('holidays.index')
            ->with('success', 'Holiday Deleted Successfully');
    }
}
