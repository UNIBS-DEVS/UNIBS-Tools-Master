@extends('layouts.app')

@section('title', 'Advance Requests | Unibs Tools')

@section('content')

    <div class="container mt-4">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-semibold">
                    Advance Requests
                </h5>

                <div class="d-flex gap-2 align-items-center">

                    <form method="GET" action="{{ route('advances.index') }}" class="d-flex">

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

                            <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>
                                Paid
                            </option>

                            <option value="Partially Settled" {{ request('status') == 'Partially Settled' ? 'selected' : '' }}>
                                Partially Settled
                            </option>

                            <option value="Fully Settled" {{ request('status') == 'Fully Settled' ? 'selected' : '' }}>
                                Fully Settled
                            </option>

                            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>

                        </select>

                    </form>

                    <a href="{{ route('advances.create') }}" class="btn btn-primary btn-sm">

                        Create Request

                    </a>

                </div>

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>
                            <th>Reason</th>
                            <th>Date</th>
                            <th>Requested Amount</th>
                            <th>Approved Amount</th>
                            <th>Status</th>
                            <th>Manager Remarks</th>
                            <th>Accounts Remarks</th>
                            <th class="text-center">Actions</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($advances as $advance)

                            @php
                                $item = $advance->items->first();

                                if ($item) {
                                    $item->requested_amount =
                                        $advance->items_sum_requested_amount
                                        ?? $advance->items->sum('requested_amount');
                                }
                            @endphp

                            <tr>

                                <td>
                                    {{ $advance->advance_reason ?? '-' }}
                                </td>

                                <td>
                                    {{ $item?->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d-M-Y') : '-' }}
                                </td>

                                <td>
                                    ₹ {{ number_format($item?->requested_amount ?? 0, 2) }}
                                </td>

                                <td>
                                    {{ $advance->approved_amount !== null ? '₹ ' . number_format($advance->approved_amount, 2) : '-' }}
                                </td>

                                <td>

                                    <span class="badge
                                                     @if($advance->status == 'Approved')
                                                         bg-success
                                                     @elseif($advance->status == 'Rejected')
                                                         bg-danger
                                                     @elseif($advance->status == 'Pending')
                                                         bg-secondary
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

                                </td>

                                <td>
                                    {{ $advance->manager_remarks ?? '-' }}
                                </td>

                                <td>
                                    {{ $advance->accounts_remarks ?? '-' }}
                                </td>

                                <td class="text-center">

                                    @if(
                                            $advance->status == 'Pending' ||
                                            $advance->status == 'Rejected'
                                        )

                                        <a href="{{ route('advances.items.index', $advance->id) }}"
                                            class="btn btn-outline-warning btn-sm" title="Edit Request">

                                            <i class="fa-solid fa-pen"></i>

                                        </a>

                                    @else

                                        <span class="text-muted">-</span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8" class="text-center text-muted">

                                    No advance requests found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

                <div class="mt-3">
                    {{ $advances->links() }}
                </div>

            </div>

        </div>


    </div>

@endsection