@extends('layouts.app')

@section('title', 'Expenses | Unibs Tools')

@section('content')

    <div class="container mt-4">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-semibold">
                    Expenses
                </h5>

                <div class="d-flex gap-2 align-items-center">

                    <form method="GET" action="{{ route('expenses.index') }}" class="d-flex">

                        <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">

                            <option value="" {{ request('status', '') == '' ? 'selected' : '' }}>
                                All Status
                            </option>

                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>
                                Pending (Draft)
                            </option>

                            <option value="Submitted" {{ request('status') == 'Submitted' ? 'selected' : '' }}>
                                Submitted
                            </option>

                            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>
                                Approved
                            </option>

                            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>

                            <option value="Reimbursed" {{ request('status') == 'Reimbursed' ? 'selected' : '' }}>
                                Reimbursed
                            </option>

                        </select>

                    </form>

                    <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">

                        Create Request

                    </a>

                </div>

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Manager Remarks</th>
                            <th>Accounts Remarks</th>
                            <th class="text-center">Actions</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($expenses as $expense)
                            @php
                                $item = $expense->items->first();

                                if ($item) {
                                    $item->amount = $expense->items_sum_amount ?? $expense->items->sum('amount');
                                }
                            @endphp

                            <tr>



                                <td>
                                    {{ $expense->user_remarks ?? '-' }}
                                </td>

                                <td>
                                    {{ $item?->expense_date ? \Carbon\Carbon::parse($item->expense_date)->format('d-M-Y') : '-' }}
                                </td>

                                <td>
                                    ₹ {{ number_format($item?->amount ?? 0, 2) }}
                                </td>





                                <td>
                                    <span
                                        class="badge
                                                                                                                 @if ($expense->status == 'Approved') bg-success
                                                                                                                 @elseif($expense->status == 'Rejected')
                                                                                                                     bg-danger
                                                                                                                 @elseif($expense->status == 'Pending')
                                                                                                                     bg-secondary
                                                                                                                 @elseif($expense->status == 'Submitted')
                                                                                                                     bg-warning text-dark
                                                                                                                 @elseif($expense->status == 'Reimbursed')
                                                                                                                     bg-info
                                                                                                                 @else
                                                                                                                     bg-secondary @endif">
                                        {{ $expense->status }}
                                    </span>
                                </td>

                                <td>
                                    {{ $expense->manager_remarks ?? '-' }}
                                </td>

                                <td>
                                    {{ $expense->reimbursement?->accounts_remarks ?? '-' }}
                                </td>

                                <td class="text-center">

                                    @if ($expense->status == 'Pending' || $expense->status == 'Rejected')
                                        <a href="{{ route('expenses.items.index', $expense->id) }}"
                                            class="btn btn-outline-warning btn-sm" title="Edit Request">

                                            <i class="fa-solid fa-pen"></i>

                                        </a>
                                    @elseif($expense->status == 'Reimbursed')
                                        <a href="{{ route('expenses.show', $expense->id) }}"
                                            class="btn btn-outline-info btn-sm" title="Reimbursement Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center text-muted">

                                    No expenses found.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

                <div class="mt-3">
                    {{ $expenses->links() }}
                </div>

            </div>

        </div>

    </div>

@endsection
