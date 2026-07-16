<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body>

    <h3>Attendance Notification</h3>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th align="left">Employee</th>
            <td>{{ $attendance->user->name ?? 'None' }}</td>
        </tr>

        <tr>
            <th align="left">Email</th>
            <td>{{ $attendance->user->email ?? 'None' }}</td>
        </tr>

        <tr>
            <th align="left">Punch Type</th>
            <td>
                <strong>
                    {{ strtoupper($attendance->punch_type) }}
                    -
                    {{ strtoupper($attendance->punch_source) }}
                </strong>
            </td>
        </tr>

        <tr>
            <th align="left">Date</th>
            <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') }}</td>
        </tr>

        <tr>
            <th align="left">Time</th>
            <td>{{ \Carbon\Carbon::parse($attendance->punch_at)->format('h:i A') }}</td>
        </tr>

        <tr>
            <th align="left">Location</th>
            <td>
                {{ $attendance->attendanceLocation?->location_name }}
            </td>
        </tr>

        <tr>
            <th align="left">Location Type</th>
            <td>
                {{ $attendance->attendanceLocation?->type }}
            </td>
        </tr>

        <tr>
            <th align="left">Status</th>
            <td>{{ ucfirst($attendance->status) }}</td>
        </tr>

        <tr>
            <th align="left">Remarks</th>
            <td>{{ $attendance->remarks ?? '-' }}</td>
        </tr>
    </table>

    <p>
        Regards,<br>
        {{ config('app.name') }}
    </p>

</body>

</html>
