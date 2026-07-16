<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    

        <h2>Expense Request Rejected By Accounts</h2>

    <p>Your expense request has been rejected.</p>

    <p>
    <b>Expense ID:</b> {{ $reimbursement->expense_id ?? 'N/A' }}
    </p>

    <p>
    <b>Accounts Remarks:</b>
    {{ $reimbursement->accounts_remarks ?? '-' }}
    </p>

    <p>
    You may edit and resubmit the request if required.
    </p>


</body>
</html>