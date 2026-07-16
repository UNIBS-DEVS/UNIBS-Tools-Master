<?php

namespace App\Http\Controllers\AttendanceAPIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendancePunchAuditRequest;
use App\Models\AttendancePunchAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendancePunchAuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AttendancePunchAudit::query();

        // If authenticated API
        // $query->where('user_id', auth()->id());

        if ($request->filled('start_date') && $request->filled('end_date')) {

            $query->whereBetween(
                'created_at',
                [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]
            );
        } else {

            $query->whereDate('created_at', today());
        }

        $records = $query
            ->latest('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $records->count(),
            'data' => $records
        ]);
    }

    public function store(AttendancePunchAuditRequest $request)
    {
        $audit = AttendancePunchAudit::create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Audit saved successfully.',
            'id' => $audit->id,
        ], 201);
    }
}
