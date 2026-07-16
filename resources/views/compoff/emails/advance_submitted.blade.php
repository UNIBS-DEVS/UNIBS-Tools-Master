<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Advance Request Submitted</title>
</head>
<body>
    <h2>New Advance Request Submitted</h2>
    <p>A new advance request has been submitted and is awaiting your review.</p>
    <p><strong>Advance ID:</strong> {{ $advance->id }}</p>
    <p><strong>Employee ID:</strong> {{ $advance->users_id }}</p>
    <p><strong>Employee Name:</strong> {{ $advance->employee?->name ?? 'N/A' }}</p>
    <p><strong>Amount Requested:</strong> ₹{{ number_format($advance->total_requested_amount ?? 0, 2) }}</p>
    <p><strong>Reason / Remarks:</strong> {{ $advance->advance_reason }}</p>
    <p>Please review this request in the Expense Management System.</p>
</body>
</html>
