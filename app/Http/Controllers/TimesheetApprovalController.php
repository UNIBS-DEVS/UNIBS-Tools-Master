<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveTimesheetRequest;
use App\Http\Requests\RejectTimesheetRequest;
use App\Mail\TimesheetStatusMail;
use App\Models\Timesheet;
use App\Models\TimesheetEntry;
use App\Models\ToolsMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class TimesheetApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status ?? 'submitted';

        $timesheets = Timesheet::with('user')
            ->when($status !== 'all', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest('user_submission_at')
            ->paginate(15);

        return view(
            'timesheet-approvals.index',
            compact('timesheets', 'status')
        );
    }

    public function show(Timesheet $timesheet)
    {
        $entries = TimesheetEntry::with([
            'subActivity.activity.project',
            'customer',
        ])
            ->where('timesheet_id', $timesheet->id)
            ->orderBy('work_date')
            ->paginate(50);

        $totalHours = $timesheet->total_hours;

        return view(
            'timesheet-approvals.show',
            compact(
                'timesheet',
                'entries',
                'totalHours'
            )
        );
    }


    public function approve(ApproveTimesheetRequest $request, Timesheet $timesheet)
    {
        $timesheet->update([
            'status'            => 'approved',
            'manager_id'        => auth()->id(),
            'manager_action_at' => now(),
            'manager_remarks'   => $request->manager_remarks,
        ]);

        $config = ToolsMaster::first();

        try {

            $html = view('emails.timesheet-status', [
                'timesheet' => $timesheet,
            ])->render();

            $to = $timesheet->user->email;

            $cc = array_filter([
                $timesheet->user->manager->email,
                $config?->timesheet_notification_email, // Change to your timesheet mailbox
            ]);

            app(\App\Services\MailService::class)->send(
                $to,
                new \App\Mail\TimesheetStatusMail($timesheet),
                'Timesheet Approved - ' . $timesheet->user->name,
                $html,
                $cc,
            );
        } catch (\Throwable $e) {

            return back()->with('error', $e->getMessage());
        }


        return back()
            ->with('success', 'Timesheet approved successfully.');
    }

    public function reject(RejectTimesheetRequest $request, Timesheet $timesheet)
    {
        $timesheet->update([
            'status'            => 'rejected',
            'manager_id'        => auth()->id(),
            'manager_action_at' => now(),
            'manager_remarks'   => $request->manager_remarks,
        ]);

        $config = ToolsMaster::first();

        try {

            $html = view('emails.timesheet-status', [
                'timesheet' => $timesheet,
            ])->render();

            $to = $timesheet->user->email;

            $cc = array_filter([
                $timesheet->user->manager->email,
                $config?->timesheet_notification_email, // Change to your timesheet mailbox
            ]);

            app(\App\Services\MailService::class)->send(
                $to,
                new \App\Mail\TimesheetStatusMail($timesheet),
                'Timesheet Rejected - ' . $timesheet->user->name,
                $html,
                $cc,
            );
        } catch (\Throwable $e) {

            return back()->with('error', $e->getMessage());
        }

        return back()
            ->with('error', 'Timesheet rejected successfully.');
    }
}
