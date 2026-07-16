<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerJob;
use App\Models\RecruiterAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class RecruiterAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');

        $recruiters = User::where('status', 'Active')
            ->whereJsonContains('roles', 'sourcing')
            ->orderBy('name')
            ->get();

        $customers = Customer::with([
            'jobs' => function ($query) {
                $query->where('status', 'Open');
            },
            'jobs.recruiterAssignments' => function ($query) use ($date) {
                $query->whereDate('assignment_date', $date);
            }
        ])
            ->where('status', 'Active')
            ->whereHas('jobs', function ($query) {
                $query->where('status', 'Open');
            })
            ->get();

        return view(
            'recruiter_assignments.index',
            compact(
                'customers',
                'recruiters',
                'date'
            )
        );
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'assignment_date' => 'required|date',
            'assignments' => 'required|array',
        ]);

        foreach ($request->assignments as $assignment) {

            $conditions = [
                'customer_job_id' => $assignment['customer_job_id'],
                'recruiter_id'    => $assignment['recruiter_id'],
                'assignment_date' => $request->assignment_date,
            ];

            if ($assignment['checked']) {

                RecruiterAssignment::updateOrCreate(
                    $conditions,
                    [
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]
                );
            } else {

                RecruiterAssignment::where($conditions)->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Assignments saved successfully.'
        ]);
    } 

    public function myAssignments(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');

        $jobs = CustomerJob::with('customer')
            ->whereHas('recruiterAssignments', function ($q) use ($date) {
                $q->where('recruiter_id', auth()->id())
                    ->whereDate('assignment_date', $date);
            })
            ->get();

        // dd($jobs);
        return view(
            'recruiter_assignments.my_assignments',
            compact('jobs', 'date')
        );
    }
}
