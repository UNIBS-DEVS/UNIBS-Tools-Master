<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body>
    @if ($status === 'submitted')
        <h3>New {{ ucfirst($type) }} Request Submitted</h3>
        <p>A new {{ $type }} request has been submitted and is awaiting manager review.</p>
    @elseif($status === 'accounts_received')
        <h3>{{ ucfirst($type) }} Request Ready For Processing</h3>
        <p>A {{ $type }} request has been approved by the manager and is awaiting action from accounts.</p>
    @elseif($status === 'accounts_approved')
        <h3>{{ $type === 'expense' ? 'Reimbursement' : 'Advance Payment' }} Processed Successfully</h3>
        <p>Your {{ $type }} request payment has been processed.</p>
    @elseif($status === 'manager_approved')
        <h3>{{ ucfirst($type) }} Request Approved By Manager</h3>
        <p>Your {{ $type }} request has been approved by your manager and forwarded to accounts for processing.
        </p>
    @elseif($status === 'manager_rejected')
        <h3>{{ ucfirst($type) }} Request Rejected By Manager</h3>
        <p>Your {{ $type }} request has been rejected by your manager.</p>
    @elseif($status === 'accounts_rejected')
        <h3>{{ ucfirst($type) }} Request Rejected By Accounts</h3>
        <p>Your {{ $type }} request has been rejected by accounts.</p>
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

    @if ($status === 'accounts_received')
        <p>Please login to the Expense Management System and process this request.</p>
    @elseif($status === 'manager_rejected' || $status === 'accounts_rejected')
        <p>You may edit and resubmit the request if required.</p>
    @else
        <p>Please review it in the application.</p>
    @endif

    <p>
        Regards,<br>
        {{ config('app.name') }}
    </p>
</body>

</html>
