@extends('layouts.app')

@section('title', 'Process Reimbursement')

@section('content')

    <div class="card">

        <div class="card-header">

            <h4 class="mb-0">
                Process Reimbursement
            </h4>

        </div>

        <div class="card-body">


            <form method="POST" action="{{ route('account.process', $expense->id) }}">


                @csrf

                <div class="row g-4">

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Expense ID
                        </label>

                        <input type="text" value="{{ $expense->id }}" readonly class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Employee
                        </label>

                        <input type="text" value="{{ $expense->employee?->name }}" readonly class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Amount Requested
                        </label>

                        <input type="text" value="₹ {{ number_format($expense->total_amount, 2) }}" readonly
                            class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Submission Date
                        </label>

                        <input type="text" value="{{ $expense->created_at?->format('d-M-Y') }}" readonly
                            class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Expense Reason
                        </label>

                        <textarea rows="3" readonly class="form-control">{{ $expense->expense_reason }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Manager Remarks - {{ $expense->employee?->manager?->name ?? 'N/A' }}
                        </label>

                        <textarea rows="3" readonly class="form-control">{{ $expense->manager_remarks }}</textarea>
                    </div>

                    <!-- Outstanding Advances Section -->
                    <div class="col-md-12 mt-3">
                        <div class="card border-warning">
                            <div
                                class="card-header bg-warning text-dark d-flex justify-content-between align-items-center py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="fa fa-info-circle me-1"></i> Outstanding Advances for
                                    {{ $expense->employee?->name }}
                                </h6>
                                <button class="btn btn-sm btn-outline-dark py-0 px-2 fw-semibold text-xs" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#outstandingAdvancesCollapse"
                                    aria-expanded="true" aria-controls="outstandingAdvancesCollapse">
                                    Toggle View
                                </button>
                            </div>
                            <div class="collapse show" id="outstandingAdvancesCollapse">
                                <div class="card-body p-0">
                                    @if($outstandingAdvances->isEmpty())
                                        <div class="p-3 text-muted text-center text-sm">
                                            No outstanding advances found for this employee.
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped table-hover mb-0 align-middle text-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Advance Reason</th>
                                                        <th>Approved Amount</th>
                                                        <th>Pending</th>
                                                        <th>Status</th>
                                                        <th>Approved Date</th>
                                                        <th class="pe-3 text-end py-2" style="width: 160px;">Settle Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($outstandingAdvances as $adv)
                                                        <tr>
                                                            <td>{{ $adv->advance_reason }}</td>
                                                            <td>₹ {{ number_format($adv->approved_amount, 2) }}</td>
                                                            <td class="text-danger fw-semibold">₹
                                                                {{ number_format($adv->pending_amount, 2) }}
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-warning text-dark">{{ $adv->status }}</span>
                                                            </td>
                                                            <td class="text-muted py-2">
                                                                {{ $adv->manager_action_at ? $adv->manager_action_at->format('d-M-Y') : '-' }}
                                                            </td>
                                                            <td class="pe-3 text-end py-2">
                                                                <input type="number" min="0" max="{{ $adv->pending_amount }}"
                                                                    name="settlements[{{ $adv->id }}]"
                                                                    class="form-control form-control-sm text-end settlement-input d-inline-block"
                                                                    data-max="{{ $adv->pending_amount }}" value="0.00"
                                                                    style="width: 110px;">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <h6 class="fw-semibold mt-3 mb-3">Requested Expense Items</h6>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover align-middle bg-white">
                                <thead class="table-light">
                                    <tr>

                                        <th>Category</th>
                                        <th>Item Reason</th>
                                        <th>Expense Date</th>
                                        <th>Amount</th>
                                        <th>Attachments</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($expense->items as $item)
                                        <tr>

                                            <td>{{ $item->category?->category_name ?? '-' }}</td>
                                            <td>{{ $item->expense_reason ?? '-' }}</td>
                                            <td>{{ $item->expense_date ? \Carbon\Carbon::parse($item->expense_date)->format('d-M-Y') : '-' }}
                                            </td>
                                            <td>₹ {{ number_format($item->amount ?? 0, 2) }}</td>
                                            <td>
                                                @if($item->attachments->isNotEmpty())
                                                    @foreach($item->attachments as $attachment)
                                                        <div class="mb-1">
                                                            <a href="{{ asset('storage/' . $attachment->attachment_path) }}"
                                                                target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2 small">
                                                                <i class="fa fa-file me-1"></i> {{ $attachment->attachment_name }}
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted small">No attachments</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                No items found for this expense request.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Amount Paid
                        </label>

                        <input type="number" step="0.01" min="0" max="{{ $expense->total_amount }}" name="amount_paid" id="amount_paid" class="form-control"
                            value="{{ $expense->total_amount }}" required>

                    </div>

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Payment Method
                        </label>

                        <select name="payment_method" class="form-select" required>

                            <option value="UPI">UPI</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cash">Cash</option>
                            <option value="Cheque">Cheque</option>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Transaction Reference
                        </label>

                        <input type="text" name="transaction_reference" id="transaction_reference" class="form-control">

                    </div>

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <select name="status" id="status" class="form-select" required>

                            <option value="Paid">Paid</option>
                            <option value="Failed">Failed</option>
                            <option value="Pending">Pending</option>
                            <option value="Rejected">Rejected</option>

                        </select>

                    </div>

                    <div class="col-md-12">

                        <label class="form-label fw-semibold">
                            Accounts Remarks
                        </label>

                        <textarea name="accounts_remarks" rows="4" class="form-control" required></textarea>

                    </div>

                </div>

                <hr>

                <button type="submit" class="btn btn-success">

                    Process Reimbursement

                </button>

                <button type="button" onclick="rejectReimbursement()" class="btn btn-danger">
                    Reject Reimbursement
                </button>

                <a href="{{ route('account.requests') }}" class="btn btn-secondary">

                    Back

                </a>

            </form>

        </div>

    </div>

    <script>

        function rejectReimbursement() {
            document.getElementById('status').value = 'Rejected';
            updateFields();

            let remarks = document.getElementsByName('accounts_remarks')[0];
            if (!remarks.value.trim()) {
                alert('Please enter Accounts Remarks before rejecting.');
                remarks.focus();
                return;
            }

            if (confirm('Are you sure you want to reject this reimbursement?')) {
                remarks.form.submit();
            }
        }

        const expenseTotal = parseFloat('{{ $expense->total_amount }}') || 0;

        function updateFields() {
            let status = document.getElementById('status').value;

            let amountPaid =
                document.getElementById('amount_paid');

            let transactionRef =
                document.getElementById('transaction_reference');

            if (
                status === 'Failed' ||
                status === 'Pending' ||
                status === 'Rejected'
            ) {
                amountPaid.value = 0;
                amountPaid.readOnly = true;

                transactionRef.value = 'None';
                transactionRef.readOnly = true;
            }
            else {
                amountPaid.readOnly = false;
                transactionRef.readOnly = false;

                // Calculate total settled
                let totalSettled = 0;
                document.querySelectorAll('.settlement-input').forEach(function (input) {
                    let val = parseFloat(input.value) || 0;
                    let maxVal = parseFloat(input.getAttribute('data-max')) || 0;
                    if (val < 0) {
                        val = 0;
                        input.value = '0.00';
                    } else if (val > maxVal) {
                        val = maxVal;
                        input.value = maxVal.toFixed(2);
                    }
                    totalSettled += val;
                });

                let suggestedPaid = Math.max(0, expenseTotal - totalSettled);
                amountPaid.value = suggestedPaid.toFixed(2);
                amountPaid.max = suggestedPaid.toFixed(2);
            }
        }

        document.querySelectorAll('.settlement-input').forEach(function (input) {
            input.addEventListener('input', function () {
                updateFields();
            });
            input.addEventListener('change', function () {
                let val = parseFloat(this.value) || 0;
                this.value = val.toFixed(2);
                updateFields();
            });
        });

        document
            .getElementById('status')
            .addEventListener(
                'change',
                updateFields
            );

        updateFields();

    </script>

@endsection