<h2>Reimbursement Processed Successfully</h2>

<p>Your reimbursement request has been processed.</p>

<p>
    <strong>Reimbursement ID:</strong>
    {{ $reimbursement->id }}
</p>

<p>
    <strong>Expense ID:</strong>
    {{ $reimbursement->expense_id ?? 'N/A' }}
</p>

<p>
    <strong>Amount Paid:</strong>
    ₹{{ number_format($reimbursement->amount_paid ?? 0, 2) }}
</p>

<p>
    <strong>Payment Method:</strong>
    {{ $reimbursement->payment_method ?? '-' }}
</p>

<p>
    <strong>Transaction Reference:</strong>
    {{ $reimbursement->transaction_reference ?? '-' }}
</p>

<p>
    <strong>Status:</strong>
    {{ $reimbursement->status }}
</p>

<p>
    <strong>Accounts Remarks:</strong>
    {{ $reimbursement->accounts_remarks ?? '-' }}
</p>

<p>
    The reimbursement has been processed successfully.
</p>