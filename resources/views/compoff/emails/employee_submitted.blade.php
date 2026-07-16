<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    
     <h2>New Expense Request Submitted</h2>

        <p>A new expense reimbursement request has been submitted.</p>

        <p>
        <b>Expense ID:</b> {{ $expense->id }}
        </p>

        <p>
        <b>Employee ID:</b> {{ $expense->employee_id }}
        </p>

        <p>
        <b>Amount:</b> ₹{{ $expense->amount }}
        </p>

        <p>
        <b>Reason:</b> {{ $expense->expense_reason }}
        </p>

        <p>
        Please review this request in the Expense Management System.
        </p>


</body>
</html>