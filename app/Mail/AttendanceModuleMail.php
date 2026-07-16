<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttendanceModuleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $model;
    public $type;
    public $status;

    /**
     * Create a new message instance.
     *
     * @param mixed $model  Eloquent model instance (LeaveRequest, WorkFromHome, CompOff, OnDuty, Attendance)
     * @param string $type  'leave', 'wfh', 'compoff', 'onduty', 'punch', 'manual_punch'
     * @param string $status 'submitted', 'approved', 'rejected', 'approved_accounts', 'punch'
     */
    public function __construct($model, string $type, string $status)
    {
        $this->model = $model;
        $this->type = $type;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $employee = $this->model->employee ?? $this->model->user;
        $employeeName = $employee->name ?? 'Employee';
        $typeLabel = str_replace('_', ' ', strtoupper($this->type));

        if ($this->type === 'punch') {
            $subject = 'Attendance Punch ' . strtoupper($this->model->punch_type ?? '') . ' - ' . $employeeName;
        } elseif ($this->type === 'manual_punch') {
            if ($this->status === 'submitted') {
                $subject = "New Attendance Punch Request Submitted by " . $employeeName;
            } else {
                $subject = "Attendance Punch Request " . ucfirst($this->status);
            }
        } elseif ($this->status === 'approved_accounts') {
            $subject = "Unpaid Leave Approved - " . $employeeName;
        } elseif ($this->status === 'submitted') {
            $subject = "New {$typeLabel} Application Submitted by " . $employeeName;
        } else {
            $subject = "{$typeLabel} Request " . ucfirst($this->status);
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.attendance_module_template',
            with: [
                'type' => $this->type,
                'status' => $this->status,
                'model' => $this->model,
                'tableData' => $this->extractDetails(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Extract specific attributes dynamically for the email table body.
     */
    private function extractDetails(): array
    {
        $employee = $this->model->employee ?? $this->model->user;
        $employeeName = $employee->name ?? '-';

        $details = [
            'Employee' => $employeeName,
        ];

        if ($this->type === 'punch' || $this->type === 'manual_punch') {
            $details['Date'] = $this->model->attendance_date ? \Carbon\Carbon::parse($this->model->attendance_date)->format('d M Y') : '-';
            $details['Punch Time'] = $this->model->punch_at ? \Carbon\Carbon::parse($this->model->punch_at)->format('h:i A') : '-';
            $details['Punch Type'] = strtoupper($this->model->punch_type ?? '-');
            $details['Punch Source'] = ucfirst($this->model->punch_source ?? '-');
            $details['Location Name'] = $this->model->attendanceLocation?->location_name ?? '-';
            $details['Location Type'] = ucfirst($this->model->attendanceLocation?->type ?? '-');
            $details['Status'] = ucfirst($this->model->status ?? $this->status);
            $details['Remarks'] = $this->model->remarks ?? '-';
        } else {
            $details['Status'] = ($this->status === 'approved_accounts') ? 'Approved (Payroll notified)' : ucfirst($this->status);

            // Specific fields based on type
            if ($this->type === 'leave') {
                $startDate = $this->model->start_date ? \Carbon\Carbon::parse($this->model->start_date)->format('d M Y') : '-';
                $endDate = $this->model->end_date ? \Carbon\Carbon::parse($this->model->end_date)->format('d M Y') : '-';
                $details['Dates'] = "{$startDate} to {$endDate}";
                $details['Duration'] = $this->model->duration ? ucfirst($this->model->duration) : '-';
                $details['Leave Type'] = $this->model->leaveType->leave_name ?? 'None';
                $details['Remarks'] = $this->model->remarks ?? '-';
            } elseif ($this->type === 'wfh') {
                $details['Date'] = $this->model->date ? \Carbon\Carbon::parse($this->model->date)->format('d M Y') : '-';
                $details['Shift Type'] = $this->model->type ? ucfirst(str_replace('-', ' ', $this->model->type)) : '-';
                $details['Reason'] = $this->model->reason ?? '-';
            } elseif ($this->type === 'compoff') {
                $details['Day Worked'] = $this->model->day_worked ? \Carbon\Carbon::parse($this->model->day_worked)->format('d M Y') : '-';
                $details['Reason'] = $this->model->reason ?? '-';
            } elseif ($this->type === 'onduty') {
                $details['Date'] = $this->model->date ? \Carbon\Carbon::parse($this->model->date)->format('d M Y') : '-';
                $startTime = $this->model->start_time ? \Carbon\Carbon::parse($this->model->start_time)->format('h:i A') : null;
                $endTime = $this->model->end_time ? \Carbon\Carbon::parse($this->model->end_time)->format('h:i A') : null;
                $details['Hours'] = ($startTime && $endTime) ? "{$startTime} to {$endTime}" : '-';
                $details['Reason'] = $this->model->reason ?? '-';
            }

            // Action outcome remarks
            if ($this->status !== 'submitted') {
                $details['Manager Remarks'] = $this->model->manager_remarks ?? '-';
            }
        }

        return $details;
    }
}
