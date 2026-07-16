@extends('layouts.app')

@section('title', 'Advance Request Details | Unibs Tools')

@section('content')

    <div class="container mt-4">

        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="mb-1 fw-semibold">
                        Advance Request #{{ $advance->id }} Details
                    </h5>

                    <div class="text-muted small">
                        Purpose: {{ $advance->advance_reason ?? '-' }}
                    </div>
                </div>

                <div class="d-flex gap-2 align-items-center">
                    <span class="badge 
                                @if($advance->status == 'Approved')
                                    bg-success
                                @elseif($advance->status == 'Rejected')
                                    bg-danger
                                @elseif($advance->status == 'Submitted')
                                    bg-warning text-dark
                                @elseif($advance->status == 'Paid')
                                    bg-primary
                                @elseif($advance->status == 'Partially Settled')
                                    bg-info text-dark
                                @elseif($advance->status == 'Fully Settled')
                                    bg-success
                                @else
                                    bg-secondary
                                @endif">
                        {{ $advance->status }}
                    </span>

                    <a href="{{ route('manager.advances.index') }}" class="btn btn-outline-secondary btn-sm">
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
                            <div class="fw-semibold">{{ $advance->employee?->name ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded px-3 py-2 bg-light">
                            <div class="text-muted small">Total Items</div>
                            <div class="fw-semibold">{{ $advance->items->count() }}</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded px-3 py-2 bg-light">
                            <div class="text-muted small">Total Requested Amount</div>
                            <div class="fw-semibold">₹ {{ number_format($advance->items->sum('requested_amount'), 2) }}</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded px-3 py-2 bg-light">
                            <div class="text-muted small">Submission Date</div>
                            <div class="fw-semibold">{{ $advance->created_at?->format('d/M/Y') }}</div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-semibold mb-3">Requested Advance Items</h6>

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

                @if($advance->status == 'Submitted')
                    <hr>
                    <div class="mt-4">
                        <h6 class="fw-semibold mb-3">Manager Action</h6>
                        <form method="POST">
                            @csrf
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="approved_amount" class="form-label fw-semibold">
                                        Approved Amount <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" name="approved_amount" id="approved_amount" step="0.01" min="0" max="{{ $advance->items->sum('requested_amount') }}" class="form-control"
                                            value="{{ old('approved_amount', $advance->items->sum('requested_amount')) }}" required>
                                    </div>
                                    <div class="form-text">Specify the amount to approve (prefilled with requested total).</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="manager_remarks" class="form-label fw-semibold">
                                    Manager Remarks <span class="text-danger">*</span>
                                </label>
                                <textarea name="manager_remarks" id="manager_remarks" rows="3" class="form-control"
                                    placeholder="Provide reason for approval or rejection"
                                    required>{{ old('manager_remarks') }}</textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" formaction="{{ route('manager.advances.approve', $advance->id) }}"
                                    class="btn btn-success">
                                    <i class="fa fa-check me-1"></i> Approve
                                </button>
                                <button type="submit" formaction="{{ route('manager.advances.reject', $advance->id) }}"
                                    class="btn btn-danger" onclick="document.getElementById('approved_amount').removeAttribute('required')">
                                    <i class="fa fa-times me-1"></i> Reject
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    @if($advance->manager_remarks || $advance->manager_action_at)
                        <hr>
                        <div class="mt-4 p-3 bg-light rounded border">
                            <h6 class="fw-semibold mb-2">Manager Action Details</h6>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="text-muted small">Action Taken On</div>
                                    <div class="fw-semibold">
                                        {{ $advance->manager_action_at ? \Carbon\Carbon::parse($advance->manager_action_at)->format('d/M/Y H:i') : '-' }}
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="text-muted small">Decision</div>
                                    <div class="fw-semibold">
                                        <span class="badge 
                                            @if($advance->status == 'Approved' || $advance->status == 'Fully Settled')
                                                bg-success
                                            @elseif($advance->status == 'Rejected')
                                                bg-danger
                                            @elseif($advance->status == 'Paid')
                                                bg-primary
                                            @elseif($advance->status == 'Partially Settled')
                                                bg-info text-dark
                                            @else
                                                bg-secondary
                                            @endif">
                                            {{ $advance->status }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="text-muted small">Approved Amount</div>
                                    <div class="fw-semibold">
                                        ₹ {{ number_format($advance->approved_amount ?? 0, 2) }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="text-muted small">Remarks / Feedback</div>
                                    <div class="bg-white p-2 rounded border mt-1">
                                        {{ $advance->manager_remarks ?? 'No remarks provided.' }}
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
