<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimesheetRequest;
use App\Mail\TimesheetSubmittedMail;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Timesheet;
use App\Models\TimesheetEntry;
use App\Models\ToolsMaster;
use App\Services\MailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TimesheetController extends Controller
{
    public function index()
    {
        $timesheets = Timesheet::where('user_id', Auth::id())
            ->orderBy('week_start', 'desc')
            ->paginate(10);

        return view('timesheets.index', compact('timesheets'));
    }

    public function create()
    {
        $user = Auth::user();

        $projects = Project::with([
            'activities' => function ($query) {
                $query->where('status', 'active')
                    ->orderBy('name')
                    ->with([
                        'subActivities' => function ($q) {
                            $q->where('status', 'active')
                                ->orderBy('name');
                        }
                    ]);
            }
        ])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        if ($user->hasRole('contractor')) {
            $customers = Customer::whereIn('id', $user->customers ?? [])
                ->where('status', 'active')
                ->orderBy('customer')
                ->get();
        } else {
            $customers = Customer::where('status', 'active')
                ->orderBy('customer')
                ->get();
        }

        return view('timesheets.create', compact(
            'projects',
            'customers'
        ));
    }

    public function store(StoreTimesheetRequest $request)
    {
        // Prevent duplicate week
        $existing = Timesheet::where('user_id', Auth::id())
            ->where('week_start', $request->week_start)
            ->first();

        if ($existing) {
            return redirect()
                ->route('timesheets.create')
                ->with('error', 'Timesheet for this week already exists.');
        }

        $timesheet = null;

        DB::transaction(function () use ($request, &$timesheet) {

            $totalHours = 0;

            foreach ($request->tasks as $rows) {
                foreach ($rows['hours'] as $h) {
                    $totalHours += $h ?? 0;
                }
            }

            if ($totalHours <= 0) {
                throw ValidationException::withMessages([
                    'tasks' => 'At least one task with hours is required.'
                ]);
            }

            $timesheet = Timesheet::create([
                'user_id' => Auth::id(),
                'week_start' => $request->week_start,
                'week_end' => $request->week_end,
                'user_remarks' => $request->user_remarks,
                'status' => 'draft',
                'total_hours' => $totalHours,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            foreach ($request->tasks as $date => $rows) {
                foreach ($rows['hours'] as $i => $hours) {

                    $hasOtherData =
                        !empty($rows['sub_activity_id'][$i]) ||
                        !empty($rows['customer_id'][$i]) ||
                        !empty($rows['request_id'][$i]);

                    if ($hasOtherData && (!$hours || $hours <= 0)) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'tasks' => 'Hours are required for each task row.'
                        ]);
                    }

                    if (!$hours || $hours <= 0) continue;

                    TimesheetEntry::create([
                        'timesheet_id'    => $timesheet->id,
                        'work_date'       => $date,
                        'sub_activity_id' => $rows['sub_activity_id'][$i] ?? null,
                        'hours'           => $hours,
                        'remarks'         => $rows['description'][$i] ?? null,
                        'customer_id'     => $rows['customer_id'][$i] ?? null,
                        'request_id'      => $rows['request_id'][$i] ?? null,
                        'created_by'      => Auth::id(),
                        'updated_by'      => Auth::id(),
                    ]);
                }
            }
        });


        return redirect()
            ->route('timesheets.index')
            ->with('success', 'Timesheet saved as draft successfully');
    }

    public function previousWeek()
    {
        $timesheet = Timesheet::where('user_id', Auth::id())
            ->orderBy('week_start', 'desc')
            ->first();

        if (!$timesheet) {
            return response()->json([]);
        }

        $weekStart = \Carbon\Carbon::parse($timesheet->week_start)->startOfDay();

        $entries = TimesheetEntry::where('timesheet_id', $timesheet->id)
            ->orderBy('work_date')
            ->get()
            ->groupBy(function ($row) use ($weekStart) {
                return $weekStart->diffInDays(
                    \Carbon\Carbon::parse($row->work_date)->startOfDay()
                );
            })
            ->map(function ($rows) {
                return $rows->map(function ($row) {
                    return [
                        'sub_activity_id' => $row->sub_activity_id,
                        'customer_id'     => $row->customer_id,
                        'request_id'      => $row->request_id,
                        'hours'           => $row->hours,
                        'description'     => $row->remarks,
                    ];
                });
            });

        return response()->json($entries);
    }

    public function submit(Timesheet $timesheet)
    {
        if ($timesheet->user_id !== Auth::id()) {
            abort(403);
        }

        if ($timesheet->status !== 'draft') {
            return back()->with('error', 'Only draft timesheets can be submitted.');
        }

        $timesheet->update([
            'status' => 'submitted',
            'user_submission_at' => now(),
        ]);

        $config = ToolsMaster::first();
        
        try {

            if ($timesheet) {

                $html = view('emails.timesheet-submitted', [
                    'timesheet' => $timesheet,
                ])->render();

                $to = $timesheet->user->manager->email;

                $cc = array_filter([
                    $timesheet->user->email,
                    $config?->timesheet_notification_email, // Change to your timesheet mailbox
                ]);

                app(MailService::class)->send(
                    $to,
                    new TimesheetSubmittedMail($timesheet),
                    'Timesheet Submitted - ' . $timesheet->user->name,
                    $html,
                    $cc,
                    []
                );
            }
        } catch (\Throwable $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );
        }


        return redirect()
            ->route('timesheets.index')
            ->with('success', 'Timesheet submitted successfully.');
    }

    public function show($id)
    {
        $timesheet = Timesheet::with('user')->findOrFail($id);

        $entries = TimesheetEntry::with([
            'subActivity',
            'subActivity.activity',
            'subActivity.activity.project',
            'customer',
        ])
            ->where('timesheet_id', $timesheet->id)
            ->orderBy('work_date')
            ->paginate(10);

        $totalHours = TimesheetEntry::where('timesheet_id', $timesheet->id)
            ->sum('hours');

        return view('timesheets.show', compact(
            'timesheet',
            'entries',
            'totalHours'
        ));
    }

    public function edit(Timesheet $timesheet)
    {
        if ($timesheet->user_id !== Auth::id()) {
            abort(403);
        }

        if ($timesheet->status !== 'draft') {
            return redirect()
                ->route('timesheets.show', $timesheet->id)
                ->with('error', 'Only draft timesheets can be edited.');
        }

        $timesheet->load([
            'entries' => function ($q) {
                $q->orderBy('work_date');
            },
            'entries.subActivity',
            'entries.subActivity.activity',
            'entries.subActivity.activity.project',
        ]);

        $timesheetData = $timesheet->entries
            ->groupBy('work_date')
            ->map(function ($rows) {
                return $rows->map(function ($row) {
                    $projectId = null;
                    $activityId = null;

                    if ($row->subActivity) {
                        $activityId = $row->subActivity->activity_id;
                        if ($row->subActivity->activity) {
                            $projectId = $row->subActivity->activity->project_id;
                        }
                    }

                    return [
                        'id'              => $row->id,
                        'project_id'      => $projectId,
                        'activity_id'     => $activityId,
                        'sub_activity_id' => $row->sub_activity_id,
                        'customer_id'     => $row->customer_id,
                        'request_id'      => $row->request_id,
                        'hours'           => (float) $row->hours,
                        'description'     => $row->remarks ?? '',
                    ];
                });
            });

        $user = Auth::user();

        if ($user->hasRole('contractor')) {
            $customers = Customer::whereIn(
                'id',
                $user->customers ?? []
            )
                ->where('status', 'active')
                ->orderBy('customer')
                ->get();
        } else {
            $customers = Customer::where('status', 'active')
                ->orderBy('customer')
                ->get();
        }

        return view('timesheets.edit', [
            'timesheet'     => $timesheet,
            'timesheetData' => $timesheetData,
            'customers'     => $customers,
            'projects'      => Project::with([
                'activities.subActivities'
            ])->orderBy('name')->get(),
            'weekStartFormatted' => $timesheet->week_start ? \Carbon\Carbon::parse($timesheet->week_start)->format('Y-m-d') : '',
        ]);
    }

    public function update(StoreTimesheetRequest $request, Timesheet $timesheet)
    {
        if ($timesheet->user_id !== Auth::id()) {
            abort(403);
        }

        if ($timesheet->status !== 'draft') {
            return back()->with('error', 'Only draft timesheets can be updated.');
        }

        DB::transaction(function () use ($request, $timesheet) {

            $totalHours = 0;

            foreach ($request->tasks as $rows) {
                foreach ($rows['hours'] as $h) {
                    $totalHours += $h ?? 0;
                }
            }

            if ($totalHours <= 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'tasks' => 'At least one task with hours is required.'
                ]);
            }

            $timesheet->update([
                'week_start'       => $request->week_start,
                'week_end'         => $request->week_end,
                'user_remarks'     => $request->user_remarks,
                'total_hours'      => $totalHours,
                'user_submission_at' => $request->user_submission_at,
            ]);

            $timesheet->entries()->delete();

            foreach ($request->tasks as $date => $rows) {
                foreach ($rows['hours'] as $i => $hours) {

                    $hasOtherData =
                        !empty($rows['sub_activity_id'][$i]) ||
                        !empty($rows['customer_id'][$i]) ||
                        !empty($rows['request_id'][$i]);

                    if ($hasOtherData && (!$hours || $hours <= 0)) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'tasks' => 'Hours are required for each task row.'
                        ]);
                    }

                    if (!$hours || $hours <= 0) {
                        continue;
                    }

                    TimesheetEntry::create([
                        'timesheet_id'    => $timesheet->id,
                        'work_date'       => $date,
                        'customer_id'     => $rows['customer_id'][$i] ?? null,
                        'request_id'      => $rows['request_id'][$i] ?? null,
                        'sub_activity_id' => $rows['sub_activity_id'][$i] ?? null,
                        'hours'           => $hours,
                        'remarks'         => $rows['description'][$i] ?? null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('timesheets.index')
            ->with('success', 'Timesheet updated successfully.');
    }

    public function destroy(Timesheet $timesheet)
    {
        if ($timesheet->user_id !== Auth::id()) {
            abort(403);
        }

        if ($timesheet->status !== 'draft') {
            return back()->with('error', 'Only draft timesheets can be deleted.');
        }

        DB::transaction(function () use ($timesheet) {
            $timesheet->entries()->delete();
            $timesheet->delete();
        });

        return redirect()
            ->route('timesheets.index')
            ->with('success', 'Timesheet deleted successfully.');
    }
}
