<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Expense Request</title>
</head>
<body>
    <h2>New expense request submitted</h2>

    <p>An employee has submitted a new expense request.</p>

    <p><strong>Expense ID:</strong> {{ $expense->id }}</p>
    <p><strong>Amount:</strong> {{ $expense->amount }}</p>
    <p><strong>Date:</strong> {{ $expense->expense_date }}</p>
    <p><strong>Status:</strong> {{ $expense->status }}</p>

    <p>Please review it in the application.</p>
</body>
</html>