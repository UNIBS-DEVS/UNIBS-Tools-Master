<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpenseModuleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $model;
    public $type;
    public $status;
    public $secondaryModel;

    /**
     * Create a new message instance.
     *
     * @param mixed $model           Primary model (Expense, Reimbursement, AdvanceRequest, AdvancePayment)
     * @param string $type           'expense' or 'advance'
     * @param string $status         'submitted', 'manager_approved', 'manager_rejected', 'accounts_received', 'accounts_approved', 'accounts_rejected'
     * @param mixed $secondaryModel  Optional payment/reimbursement model instance
     */
    public function __construct($model, string $type, string $status, $secondaryModel = null)
    {
        $this->model = $model;
        $this->type = $type;
        $this->status = $status;
        $this->secondaryModel = $secondaryModel;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $typeLabel = ucfirst($this->type); // e.g. Expense or Advance

        if ($this->status === 'submitted') {
            $subject = "New {$typeLabel} Request Submitted";
        } elseif ($this->status === 'accounts_received') {
            $subject = "{$typeLabel} Request Ready For Processing";
        } elseif ($this->status === 'accounts_approved') {
            $subject = ($this->type === 'expense') ? 'Reimbursement Processed Successfully' : 'Advance Payment Processed';
        } else {
            $actor = str_contains($this->status, 'manager') ? 'Manager' : 'Accounts';
            $action = str_contains($this->status, 'approved') ? 'Approved' : 'Rejected';
            $subject = "{$typeLabel} Request {$action} By {$actor}";
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.expense_module_template',
            with: [
                'type' => $this->type,
                'status' => $this->status,
                'model' => $this->model,
                'secondaryModel' => $this->secondaryModel,
                'tableData' => $this->extractDetails(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Extract attributes dynamically for the email table.
     */
    private function extractDetails(): array
    {
        $details = [];

        // Determine what primary request we are describing
        if ($this->type === 'expense') {
            // $this->model is either Expense or Reimbursement
            $expense = ($this->model instanceof \App\Models\Reimbursement)
                ? \App\Models\Expense::find($this->model->expense_id)
                : $this->model;

            $employee = $expense->employee ?? null;
            $employeeName = $employee->name ?? 'N/A';

            $details['Expense ID'] = $expense->id ?? 'N/A';
            $details['Employee Name'] = $employeeName;
            $details['Amount'] = '₹' . number_format($expense->amount ?? 0, 2);
            $details['Expense Date'] = $expense->expense_date ?? '-';
            $details['Reason'] = $expense->expense_reason ?? '-';

            if (str_contains($this->status, 'manager')) {
                $details['Manager Remarks'] = $expense->manager_remarks ?? '-';
            }

            if ($this->status === 'accounts_approved' && $this->model instanceof \App\Models\Reimbursement) {
                $reimbursement = $this->model;
                $details['Reimbursement ID'] = $reimbursement->id;
                $details['Amount Paid'] = '₹' . number_format($reimbursement->amount_paid ?? 0, 2);
                $details['Payment Method'] = $reimbursement->payment_method ?? '-';
                $details['Transaction Reference'] = $reimbursement->transaction_reference ?? '-';
                $details['Accounts Remarks'] = $reimbursement->accounts_remarks ?? '-';
            } elseif ($this->status === 'accounts_rejected' && $this->model instanceof \App\Models\Reimbursement) {
                $details['Accounts Remarks'] = $this->model->accounts_remarks ?? '-';
            }
        } elseif ($this->type === 'advance') {
            // $this->model is either AdvanceRequest or AdvancePayment
            $advance = ($this->model instanceof \App\Models\AdvancePayment)
                ? $this->model->advanceRequest
                : $this->model;

            $employeeName = $advance->employee?->name ?? 'N/A';

            $details['Advance ID'] = $advance->id ?? 'N/A';
            $details['Employee Name'] = $employeeName;
            $details['Amount Requested'] = '₹' . number_format($advance->total_requested_amount ?? 0, 2);
            if ($advance->approved_amount > 0) {
                $details['Approved Amount'] = '₹' . number_format($advance->approved_amount, 2);
            }
            $details['Reason'] = $advance->advance_reason ?? '-';

            if (str_contains($this->status, 'manager')) {
                $details['Manager Remarks'] = $advance->manager_remarks ?? '-';
            }

            if ($this->status === 'accounts_approved') {
                $payment = $this->secondaryModel ?: $this->model;
                if ($payment instanceof \App\Models\AdvancePayment) {
                    $details['Payment ID'] = $payment->id;
                    $details['Amount Paid'] = '₹' . number_format($payment->paid_amount ?? 0, 2);
                    $details['Payment Mode'] = $payment->payment_mode ?? '-';
                    $details['Reference No'] = $payment->reference_no ?? '-';
                    $details['Accounts Remarks'] = $payment->remarks ?? '-';
                }
            } elseif ($this->status === 'accounts_rejected') {
                $details['Accounts Remarks'] = $advance->accounts_remarks ?? '-';
            }
        }

        return $details;
    }
}
