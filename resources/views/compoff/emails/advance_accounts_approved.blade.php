<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advance Payment Processed</title>
</head>
<body>
    <h2>Advance Payment Processed Successfully</h2>
    <p>Your advance request payment has been processed.</p>
    <p><strong>Payment ID:</strong> {{ $payment->id }}</p>
    <p><strong>Advance ID:</strong> {{ $payment->advance_id }}</p>
    <p><strong>Amount Paid:</strong> ₹{{ number_format($payment->paid_amount ?? 0, 2) }}</p>
    <p><strong>Payment Mode:</strong> {{ $payment->payment_mode }}</p>
    <p><strong>Reference No:</strong> {{ $payment->reference_no }}</p>
    <p><strong>Accounts Remarks:</strong> {{ $payment->remarks ?? '-' }}</p>
</body>
</html>
