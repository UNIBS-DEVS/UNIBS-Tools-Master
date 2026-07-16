@extends('layouts.app')

@section('title', 'Expense Request Details | Unibs Tools')

@section('content')

    <div class="container mt-4">

        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="mb-1 fw-semibold">
                        Expense Request #{{ $expense->id }} Details
                    </h5>

                    <div class="text-muted small">
                        Title / Purpose: {{ $expense->user_remarks ?? '-' }}
                    </div>
                </div>

                <div class="d-flex gap-2 align-items-center">
                    <span class="badge 
                                @if($expense->status == 'Approved')
                                    bg-success
                                @elseif($expense->status == 'Rejected')
                                    bg-danger
                                @elseif($expense->status == 'Submitted')
                                    bg-warning text-dark
                                @else
                                    bg-secondary
                                @endif">
                        {{ $expense->status }}
                    </span>

                    <a href="{{ route('manager.requests') }}" class="btn btn-outline-secondary btn-sm">
                        Back
                    </a>
                </div>

            </div>

            <div class="card-body">

                @include('partials.message')

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="border rounded px-3 py-2 bg-light">
                            <div class="text-muted small">Employee</div>
                            <div class="fw-semibold">{{ $expense->employee?->name ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded px-3 py-2 bg-light">
                            <div class="text-muted small">Total Items</div>
                            <div class="fw-semibold">{{ $expense->items->count() }}</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded px-3 py-2 bg-light">
                            <div class="text-muted small">Total Amount</div>
                            <div class="fw-semibold">₹ {{ number_format($expense->items->sum('amount'), 2) }}</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded px-3 py-2 bg-light">
                            <div class="text-muted small">Submission Date</div>
                            <div class="fw-semibold">{{ $expense->created_at?->format('d/M/Y') }}</div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-semibold mb-3">Requested Expense Items</h6>

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
                                                    <a href="{{ asset('storage/' . $attachment->attachment_path) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary py-0 px-2 small">
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

                @if($expense->status == 'Submitted')
                    <hr>
                    <div class="mt-4">
                        <h6 class="fw-semibold mb-3">Manager Action</h6>
                        <form method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="manager_remarks" class="form-label fw-semibold">
                                    Manager Remarks <span class="text-danger">*</span>
                                </label>
                                <textarea name="manager_remarks" id="manager_remarks" rows="3" class="form-control"
                                    placeholder="Provide reason for approval or rejection"
                                    required>{{ old('manager_remarks') }}</textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" formaction="{{ route('manager.approve', $expense->id) }}"
                                    class="btn btn-success">
                                    <i class="fa fa-check me-1"></i> Approve
                                </button>
                                <button type="submit" formaction="{{ route('manager.reject', $expense->id) }}"
                                    class="btn btn-danger">
                                    <i class="fa fa-times me-1"></i> Reject
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    @if($expense->manager_remarks || $expense->manager_action_at)
                        <hr>
                        <div class="mt-4 p-3 bg-light rounded border">
                            <h6 class="fw-semibold mb-2">Manager Action Details</h6>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="text-muted small">Action Taken On</div>
                                    <div class="fw-semibold">
                                        {{ $expense->manager_action_at ? \Carbon\Carbon::parse($expense->manager_action_at)->format('d/M/Y H:i') : '-' }}
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="text-muted small">Decision</div>
                                    <div class="fw-semibold">
                                        <span class="badge @if($expense->status == 'Approved') bg-success @else bg-danger @endif">
                                            {{ $expense->status }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="text-muted small">Remarks / Feedback</div>
                                    <div class="bg-white p-2 rounded border mt-1">
                                        {{ $expense->manager_remarks ?? 'No remarks provided.' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

            </div>

        </div>

    </div>

@endsection