@extends('layouts.app')

@section('title', 'Process Advance Payment')

@section('content')

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white">

            <h4 class="mb-0 fw-semibold">
                Process Advance Payment
            </h4>

        </div>

        <div class="card-body">

            @include('partials.message')

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('accounts.advances.process', $advance->id) }}">
                @csrf

                <div class="row g-4">

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Advance ID
                        </label>
                        <input type="text" value="{{ $advance->id }}" readonly class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Employee
                        </label>
                        <input type="text" value="{{ $advance->employee?->name }}" readonly class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Approved Amount
                        </label>
                        <input type="text" value="₹ {{ number_format($advance->approved_amount, 2) }}" readonly
                            class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Submission Date
                        </label>
                        <input type="text" value="{{ $advance->created_at?->format('d/M/Y') }}" readonly
                            class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Advance Reason / Purpose
                        </label>
                        <textarea rows="3" readonly class="form-control">{{ $advance->advance_reason }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Manager Remarks - {{ $advance->employee?->manager?->name ?? 'N/A' }}
                        </label>
                        <textarea rows="3" readonly class="form-control">{{ $advance->manager_remarks }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <h6 class="fw-semibold mt-3 mb-3">Requested Advance Items</h6>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover align-middle bg-white">
                                <thead class="table-light">
                                    <tr>
                                        <th>Category</th>
                                        <th>Item Reason</th>
                                        <th>Requested Amount</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($advance->items as $item)
                                        <tr>
                                            <td>{{ $item->category?->category_name ?? '-' }}</td>
                                            <td>{{ $item->expense_reason ?? '-' }}</td>
                                            <td>₹ {{ number_format($item->requested_amount ?? 0, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">
                                                No items found for this advance request.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($advance->status == 'Approved')
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                Amount Paid
                            </label>
                            <input type="number" step="0.01" min="0" max="{{ $advance->approved_amount }}" name="paid_amount" id="paid_amount" class="form-control"
                                value="{{ $advance->approved_amount }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                Payment Mode
                            </label>
                            <select name="payment_mode" class="form-select" required>
                                <option value="Bank">Bank Transfer</option>
                                <option value="Cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                Transaction Reference / Ref No.
                            </label>
                            <input type="text" name="reference_no" id="reference_no" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                Payment Date
                            </label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control"
                                value="{{ now()->toDateString() }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                Action Status
                            </label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="Paid">Paid</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Accounts Remarks
                            </label>
                            <textarea name="accounts_remarks" id="accounts_remarks" rows="4" class="form-control" required></textarea>
                        </div>
                    @else
                        <!-- Historical View -->
                        @if($advance->accounts_remarks || $advance->payments->isNotEmpty())
                            <div class="col-12">
                                <hr>
                                <h6 class="fw-semibold mb-3">Accounts Processing Details</h6>
                                <div class="row bg-light p-3 rounded border">
                                    <div class="col-md-4 mb-2">
                                        <div class="text-muted small">Status</div>
                                        <div class="fw-semibold">
                                            <span class="badge @if($advance->status == 'Paid') bg-primary @elseif($advance->status == 'Rejected') bg-danger @else bg-secondary @endif">
                                                {{ $advance->status }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($advance->payments->isNotEmpty())
                                        @php $payment = $advance->payments->first(); @endphp
                                        <div class="col-md-4 mb-2">
                                            <div class="text-muted small">Amount Paid</div>
                                            <div class="fw-semibold">₹ {{ number_format($payment->paid_amount, 2) }}</div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="text-muted small">Payment Mode</div>
                                            <div class="fw-semibold">{{ $payment->payment_mode }}</div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="text-muted small">Reference No.</div>
                                            <div class="fw-semibold">{{ $payment->reference_no ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="text-muted small">Payment Date</div>
                                            <div class="fw-semibold">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d/M/Y') : '-' }}</div>
                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <div class="text-muted small">Remarks / Feedback</div>
                                        <div class="bg-white p-2 rounded border mt-1">
                                            {{ $advance->accounts_remarks ?? 'No remarks provided.' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                </div>

                @if($advance->status == 'Approved')
                    <hr>
                    <button type="submit" class="btn btn-success me-2">
                        Process Advance Payment
                    </button>
                    <button type="button" onclick="rejectAdvance()" class="btn btn-danger">
                        Reject Advance
                    </button>
                @endif

                <a href="{{ route('accounts.advances.requests') }}" class="btn btn-secondary ms-2 mt-3">
                    Back
                </a>

            </form>

        </div>

    </div>

    @if($advance->status == 'Approved')
        <script>
            function rejectAdvance() {
                document.getElementById('status').value = 'Rejected';
                updateFields();

                let remarks = document.getElementById('accounts_remarks');
                if (!remarks.value.trim()) {
                    alert('Please enter Accounts Remarks before rejecting.');
                    remarks.focus();
                    return;
                }

                if (confirm('Are you sure you want to reject this advance request?')) {
                    remarks.form.submit();
                }
            }

            function updateFields() {
                let status = document.getElementById('status').value;
                let paidAmount = document.getElementById('paid_amount');
                let paymentMode = document.getElementsByName('payment_mode')[0];
                let referenceNo = document.getElementById('reference_no');
                let paymentDate = document.getElementById('payment_date');

                if (status === 'Rejected') {
                    paidAmount.value = 0;
                    paidAmount.readOnly = true;
                    paidAmount.removeAttribute('required');

                    paymentMode.value = 'Bank';
                    paymentMode.disabled = true;
                    paymentMode.removeAttribute('required');

                    referenceNo.value = 'None';
                    referenceNo.readOnly = true;
                    referenceNo.removeAttribute('required');

                    paymentDate.removeAttribute('required');
                } else {
                    paidAmount.readOnly = false;
                    paidAmount.setAttribute('required', 'required');
                    
                    paymentMode.disabled = false;
                    paymentMode.setAttribute('required', 'required');
                    
                    referenceNo.readOnly = false;
                    referenceNo.setAttribute('required', 'required');
                    
                    paymentDate.setAttribute('required', 'required');
                }
            }

            document.getElementById('status').addEventListener('change', updateFields);
            updateFields();
        </script>
    @endif

@endsection
