<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advance Request Rejected By Accounts</title>
</head>
<body>
    <h2>Advance Request Rejected By Accounts</h2>
    <p>Your advance request has been rejected by accounts.</p>
    <p><strong>Advance ID:</strong> {{ $advance->id }}</p>
    <p><strong>Accounts Remarks:</strong> {{ $advance->accounts_remarks ?? '-' }}</p>
    <p>You may edit and resubmit the request if required.</p>
</body>
</html>
