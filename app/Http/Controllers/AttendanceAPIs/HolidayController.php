<?php

namespace App\Http\Controllers\AttendanceAPIs;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'year' => ['required', 'digits:4'],
        ]);

        $holidays = Holiday::query()
            ->where('holiday_year', $request->year)
            ->orderBy('holiday_date')
            ->get([
                'id',
                'holiday_year',
                'holiday_date',
                'description',
                'holiday_type',
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Holiday list fetched successfully.',
            'data' => $holidays,
        ]);
    }
}
