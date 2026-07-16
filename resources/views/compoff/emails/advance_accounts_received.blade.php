<!DOCTYPE html>
<html>
<head>
    <title>Advance Request Approved by Manager</title>
</head>
<body>
    <h2>Advance Request Ready For Processing</h2>
    <p>An advance request has been approved by the manager and is awaiting action from accounts.</p>
    <p><strong>Advance ID:</strong> {{ $advance->id }}</p>
    <p><strong>Employee ID:</strong> {{ $advance->users_id }}</p>
    <p><strong>Employee Name:</strong> {{ $advance->employee?->name ?? 'N/A' }}</p>
    <p><strong>Approved Amount:</strong> ₹{{ number_format($advance->approved_amount ?? 0, 2) }}</p>
    <p><strong>Reason / Remarks:</strong> {{ $advance->advance_reason }}</p>
    <p><strong>Manager Remarks:</strong> {{ $advance->manager_remarks }}</p>
    <p>Please login to the Expense Management System and process this request.</p>
</body>
</html>
