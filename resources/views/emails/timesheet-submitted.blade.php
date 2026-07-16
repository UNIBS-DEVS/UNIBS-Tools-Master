<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            border-color: rgb(126, 129, 132);
        }
    </style>
</head>

<body>

    <p>Hello {{ $timesheet->user->manager->name }},</p>

    <p>
        <strong>{{ $timesheet->user->name }}</strong> has submitted a timesheet for your review.
    </p>

    <table border="1" cellpadding="8" cellspacing="0"
        style="border-collapse: collapse; border-color: #ddd; width: 100%;">

        <tr>
            <th align="left" style="background-color:#f2f2f2;">Start Date</th>
            <td>{{ \Carbon\Carbon::parse($timesheet->week_start)->format('d M Y') }}</td>
        </tr>

        <tr>
            <th align="left" style="background-color:#f2f2f2;">End Date</th>
            <td>{{ \Carbon\Carbon::parse($timesheet->week_end)->format('d M Y') }}</td>
        </tr>

        <tr>
            <th align="left" style="background-color:#f2f2f2;">Total Hours</th>
            <td>{{ $timesheet->total_hours }}</td>
        </tr>

        <tr>
            <th align="left" style="background-color:#f2f2f2;">Status</th>
            <td>
                @if ($timesheet->status == 'submitted')
                    <span style="color:#fd7e14;font-weight:bold;">Submitted</span>
                @elseif($timesheet->status == 'approved')
                    <span style="color:#28a745;font-weight:bold;">Approved</span>
                @elseif($timesheet->status == 'rejected')
                    <span style="color:#dc3545;font-weight:bold;">Rejected</span>
                @else
                    {{ ucfirst($timesheet->status) }}
                @endif
            </td>
        </tr>

        <tr>
            <th align="left" style="background-color:#f2f2f2;">User Remarks</th>
            <td>{{ $timesheet->user_remarks ?: '-' }}</td>
        </tr>

    </table>

    <br>

    <p>
        Please Visit the
        <a href="{{ config('app.url') }}">Tools Portal</a>
        to review and approve/reject the timesheet.
    </p>

    <p>Thanks and Regards</p>

    <p>
        {{ config('app.name') }}
    </p>

</body>

</html>
