<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advance Request Approved By Manager</title>
</head>
<body>
    <h2>Advance Request Approved By Manager</h2>
    <p>Your advance request has been approved by your manager.</p>
    <p><strong>Advance ID:</strong> {{ $advance->id }}</p>
    <p><strong>Approved Amount:</strong> ₹{{ number_format($advance->approved_amount ?? 0, 2) }}</p>
    <p><strong>Manager Remarks:</strong> {{ $advance->manager_remarks }}</p>
    <p>The request has been forwarded to accounts for payment processing.</p>
</body>
</html>
