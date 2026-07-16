@extends('layouts.app')

@section('title', 'Accounts Advance Processing | Unibs Tools')

@section('content')

    <div class="container mt-4">

        @include('partials.message')

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-semibold">
                    Accounts Advance Processing
                </h5>

                <form method="GET" action="{{ route('accounts.advances.requests') }}">
                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                        <option value="Approved" {{ $status == 'Approved' ? 'selected' : '' }}>
                            Approved
                        </option>
                        <option value="Paid" {{ $status == 'Paid' ? 'selected' : '' }}>
                            Paid
                        </option>
                        <option value="Rejected" {{ $status == 'Rejected' ? 'selected' : '' }}>
                            Rejected
                        </option>
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>
                            All
                        </option>
                    </select>
                </form>

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Request Date</th>
                            <th>Requested Amount</th>
                            <th>Approved Amount</th>
                            <th>Advance Reason</th>
                            <th>Status</th>
                            <th class="text-center">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($requests as $advance)
                            <tr>
                                <td>
                                    {{ $advance->employee?->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $advance->created_at?->format('d/M/Y') }}
                                </td>

                                <td>
                                    ₹ {{ number_format($advance->items->sum('requested_amount'), 2) }}
                                </td>

                                <td>
                                    ₹ {{ number_format($advance->approved_amount ?? 0, 2) }}
                                </td>

                                <td>
                                    {{ $advance->advance_reason }}
                                </td>

                                <td>
                                    <span class="badge
                                        @if($advance->status == 'Approved')
                                            bg-success
                                        @elseif($advance->status == 'Rejected')
                                            bg-danger
                                        @elseif($advance->status == 'Pending')
                                            bg-warning text-dark
                                        @elseif($advance->status == 'Paid')
                                            bg-primary
                                        @elseif($advance->status == 'Partially Settled')
                                            bg-info text-dark
                                        @elseif($advance->status == 'Fully Settled')
                                            bg-success
                                        @else
                                            bg-secondary
                                        @endif
                                    ">
                                        {{ $advance->status }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if($advance->status == 'Approved')
                                        <a href="{{ route('accounts.advances.showProcess', $advance->id) }}"
                                            class="btn btn-outline-primary btn-sm" title="Process Advance">
                                            <i class="fa fa-money-check-dollar"></i> Process
                                        </a>
                                    @else
                                        <a href="{{ route('accounts.advances.showProcess', $advance->id) }}"
                                            class="btn btn-outline-secondary btn-sm" title="View Advance Details">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No Advance Requests Found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>

                <div class="mt-3">
                    {{ $requests->links() }}
                </div>

            </div>

        </div>

    </div>

@endsection
