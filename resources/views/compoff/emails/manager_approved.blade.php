<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

            <h2>Expense Request Approved By Manager</h2>

        <p>Your expense request has been approved by your manager.</p>

        <p>
        <b>Expense ID:</b> {{ $expense->id }}
        </p>

        <p>
        <b>Amount:</b> ₹{{ $expense->amount }}
        </p>

        <p>
        <b>Manager Remarks:</b>
        {{ $expense->manager_remarks }}
        </p>

        <p>
        The request has been forwarded to account for processing.
        </p>
    
</body>
</html>