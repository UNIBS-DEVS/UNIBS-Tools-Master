<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body>
    @if ($type === 'punch')
        <h3>Attendance Punch {{ strtoupper($model->punch_type ?? '') }} Recorded</h3>
        <p>Dear Manager,</p>
        <p>A new attendance punch has been recorded:</p>
    @elseif($type === 'manual_punch')
        @if ($status === 'submitted')
            <h3>Manual Attendance Punch Request Submitted</h3>
            <p>Dear Manager,</p>
            <p>A new manual attendance punch request has been submitted and is pending your review.</p>
        @else
            <h3>Manual Attendance Punch Request {{ ucfirst($status) }}</h3>
            <p>Dear {{ $model->user->name ?? 'Employee' }},</p>
            <p>Your manual attendance punch request has been <strong>{{ ucfirst($status) }}</strong>.</p>
        @endif
    @elseif($status === 'submitted')
        <h3>{{ str_replace('_', ' ', strtoupper($type)) }} Application Submitted</h3>
        <p>Dear Manager,</p>
        <p>A new {{ str_replace('_', ' ', $type) }} application has been submitted and is pending your review.</p>
    @elseif($status === 'approved_accounts')
        <h3>Unpaid Leave Approved - Payroll Notification</h3>
        <p>Dear Accounts Team,</p>
        <p>This is to notify you that an <strong>unpaid leave</strong> has been approved and payroll records should be
            updated accordingly.</p>
    @else
        <h3>{{ str_replace('_', ' ', strtoupper($type)) }} Request {{ ucfirst($status) }}</h3>
        <p>Dear {{ $model->employee->name ?? ($model->user->name ?? 'Employee') }},</p>
        <p>Your {{ str_replace('_', ' ', $type) }} request has been <strong>{{ ucfirst($status) }}</strong>.</p>
    @endif

    <table border="1" cellpadding="8" cellspacing="0"
        style="border-collapse: collapse; border-color: #ddd; width: 100%; max-width: 600px;">
        @foreach ($tableData as $label => $value)
            <tr>
                <th align="left" style="background-color: #f8f9fa; width: 35%;">{{ $label }}</th>
                <td>{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    @if ($status === 'submitted')
        <p>
            Please visit the <a href="{{ config('app.url') }}">Tools Portal</a> to review this request.
        </p>
    @elseif($status !== 'approved_accounts' && $type !== 'punch')
        <p>
            Please visit the <a href="{{ config('app.url') }}">Tools Portal</a> to view your request status.
        </p>
    @endif

    <p>
        Regards,<br>
        {{ config('app.name') }}
    </p>
</body>

</html>
