@extends('layouts.app')

@section('title', 'Expense Details')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 text-dark mb-0 fw-bold">Expense Request Details</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Expense #{{ $expense->id }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('expenses.index') }}"
                class="btn btn-outline-secondary btn-sm shadow-sm d-flex align-items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>

        @include('partials.message')

        <div class="row g-4">
            <!-- Left Column: Details Cards -->
            <div class="col-lg-8">
                <!-- Expense Basic Info Card -->
                <div class="card border border-light-subtle shadow-sm mb-4">
                    <div class="card-header bg-light py-3 border-light-subtle">
                        <h5 class="card-title mb-0 fw-semibold text-dark">
                            <i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Expense Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <span class="text-muted small text-uppercase font-monospace d-block">Purpose / Title</span>
                                <span class="fw-semibold text-dark fs-5">{{ $expense->user_remarks ?? '-' }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small text-uppercase font-monospace d-block">Status</span>
                                <span class="badge bg-success fs-6 py-2 px-3">{{ ucfirst($expense->status) }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small text-uppercase font-monospace d-block">Total Requested
                                    Amount</span>
                                <span class="fw-bold text-dark fs-4">₹ {{ number_format($expense->total_amount, 2) }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small text-uppercase font-monospace d-block">Requested By</span>
                                <span class="fw-semibold text-dark">{{ $expense->employee->name ?? 'Employee' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expense Items Table -->
                <div class="card border border-light-subtle shadow-sm mb-4">
                    <div class="card-header bg-light py-3 border-light-subtle">
                        <h5 class="card-title mb-0 fw-semibold text-dark">
                            <i class="fa-solid fa-list me-2 text-primary"></i>Items Requested
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Category</th>
                                        <th>Reason / Item Details</th>
                                        <th>Expense Date</th>
                                        <th class="text-end pe-3">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expense->items as $item)
                                        <tr>
                                            <td class="ps-3 fw-semibold text-dark">
                                                {{ $item->category->name ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $item->expense_reason ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $item->expense_date ? \Carbon\Carbon::parse($item->expense_date)->format('d-M-Y') : '-' }}
                                            </td>
                                            <td class="text-end pe-3 fw-semibold text-dark">
                                                ₹ {{ number_format($item->amount, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No items found for this
                                                expense request.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Advance Settlements -->
                <div class="card border border-light-subtle shadow-sm">
                    <div class="card-header bg-light py-3 border-light-subtle">
                        <h5 class="card-title mb-0 fw-semibold text-dark">
                            <i class="fa-solid fa-hand-holding-dollar me-2 text-primary"></i>Advance Settlements
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($expense->advanceRequests && $expense->advanceRequests->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Advance Reason</th>
                                            <th class="text-end">Advance Amount</th>
                                            <th class="text-end">Settled Amount</th>
                                            <th class="text-end">Pending Amount</th>
                                            <th>Settlement Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($expense->advanceRequests as $advance)
                                            <tr>
                                                <td class="fw-semibold text-dark">
                                                    {{ $advance->advance_reason ?? '-' }}
                                                </td>
                                                <td class="text-end">
                                                    ₹ {{ number_format($advance->approved_amount ?? 0, 2) }}
                                                </td>
                                                <td class="text-end text-danger fw-semibold">
                                                    - ₹ {{ number_format($advance->pivot->settled_amount ?? 0, 2) }}
                                                </td>
                                                <td class="text-end fw-semibold text-dark">
                                                    ₹ {{ number_format($advance->pivot->pending_amount ?? 0, 2) }}
                                                </td>
                                                <td>
                                                    {{ $advance->pivot->created_at ? \Carbon\Carbon::parse($advance->pivot->created_at)->format('d-M-Y') : '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0 small"><i class="fa-solid fa-circle-info me-1"></i> No advances were
                                settled against this expense request.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Status & Payment Cards -->
            <div class="col-lg-4">
                <!-- Reimbursement Details / Payment Info -->
                @if ($expense->reimbursement)
                    <div class="card border border-light-subtle shadow-sm mb-4">
                        <div class="card-header bg-success-subtle py-3 border-light-subtle">
                            <h5 class="card-title mb-0 fw-semibold text-success-emphasis">
                                <i class="fa-solid fa-credit-card me-2"></i>Payment Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <span class="text-muted small text-uppercase font-monospace d-block">Amount Paid</span>
                                <span class="fw-bold text-success fs-4">₹
                                    {{ number_format($expense->reimbursement->amount_paid ?? 0, 2) }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small text-uppercase font-monospace d-block">Payment Date</span>
                                <span
                                    class="fw-semibold text-dark">{{ $expense->reimbursement->payment_date ? \Carbon\Carbon::parse($expense->reimbursement->payment_date)->format('d-M-Y') : '-' }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small text-uppercase font-monospace d-block">Payment Method</span>
                                <span
                                    class="fw-semibold text-dark">{{ $expense->reimbursement->payment_method ?? '-' }}</span>
                            </div>
                            <div class="mb-0">
                                <span class="text-muted small text-uppercase font-monospace d-block">Transaction
                                    Reference</span>
                                <code
                                    class="text-dark bg-light px-2 py-1 rounded border small">{{ $expense->reimbursement->transaction_reference ?? '-' }}</code>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Manager Remarks Card -->
                <div class="card border border-light-subtle shadow-sm mb-4">
                    <div class="card-header bg-light py-3 border-light-subtle">
                        <h5 class="card-title mb-0 fw-semibold text-dark">
                            <i class="fa-solid fa-comment-dots me-2 text-primary"></i>Manager Review
                        </h5>
                    </div>
                    <div class="card-body">
                        <span class="text-muted small text-uppercase font-monospace d-block mb-1">Manager Remarks</span>
                        <p class="mb-0 text-dark" style="white-space: pre-line;">
                            {{ $expense->manager_remarks ?: 'No remarks provided.' }}</p>
                    </div>
                </div>

                <!-- Accounts Remarks Card -->
                <div class="card border border-light-subtle shadow-sm">
                    <div class="card-header bg-light py-3 border-light-subtle">
                        <h5 class="card-title mb-0 fw-semibold text-dark">
                            <i class="fa-solid fa-comments me-2 text-primary"></i>Accounts Review
                        </h5>
                    </div>
                    <div class="card-body">
                        <span class="text-muted small text-uppercase font-monospace d-block mb-1">Accounts Remarks</span>
                        <p class="mb-0 text-dark" style="white-space: pre-line;">
                            {{ $expense->reimbursement?->accounts_remarks ?: 'No remarks provided.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
