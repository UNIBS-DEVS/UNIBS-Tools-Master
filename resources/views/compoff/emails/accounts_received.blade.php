<!DOCTYPE html>
<html>
<head>
    <title>Expense Request Approved by Manager</title>
</head>
<body>

<h2>Expense Request Ready For Processing</h2>

<p>
A reimbursement request has been approved by the manager and is awaiting action from account.
</p>

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
<b>Expense Date:</b> {{ $expense->expense_date }}
</p>

<p>
<b>Reason:</b> {{ $expense->expense_reason }}
</p>

<p>
<b>Manager Remarks:</b>
{{ $expense->manager_remarks }}
</p>

<p>
Please login to the Expense Management System and process this request.
</p>

</body>
</html>