@extends('layouts.app')

@section('title', 'Leaves | Unibs Tools')

@section('content')

    <div class="container mt-4">

        @include('partials.message')

        <!-- Balance Summary Cards -->
        <h5 class="mb-3 fw-semibold text-secondary">My Leave Balances</h5>
        <div class="row mb-4">
            @forelse($leaveBalances as $balance)
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm border-0 bg-white p-3 d-flex flex-row align-items-center">
                        <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3">
                            <i class="fa-solid fa-calendar-check fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 text-uppercase font-monospace" style="font-size: 11px;">
                                {{ $balance->leaveType?->leave_name }}
                            </h6>
                            <h3 class="fw-bold text-dark mb-0">{{ number_format($balance->balance, 2) }}</h3>
                            <small class="text-secondary">Days Available</small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info py-2 mb-0 shadow-sm border-0">
                        No leave balances allocated yet. Balances accrue automatically.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-primary">
                    My Leave Requests
                </h5>
                <a href="{{ route('leave-requests.create') }}" class="btn btn-primary btn-sm">
                    Apply Leave
                </a>
            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Remarks</th>
                            <th>Status</th>
                            <th>Manager Remarks</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($leaveRequests as $request)
                            <tr>
                                <td>
                                    {{ $request->leaveType?->leave_name ?? '-' }}
                                </td>
                                <td>
                                    {{ $request->duration }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($request->start_date)->format('d-M-Y') }}
                                </td>
                                <td>
                                    {{ $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d-M-Y') : '-' }}
                                </td>
                                <td>
                                    {{ $request->remarks ?? '-' }}
                                </td>
                                <td>
                                    <span
                                        class="badge
                                                                        @if (strtolower($request->status) == 'approved') bg-success
                                                                        @elseif(strtolower($request->status) == 'rejected') bg-danger
                                                                        @elseif(strtolower($request->status) == 'submitted') bg-warning text-dark
                                                                        @elseif(strtolower($request->status) == 'cancelled') bg-secondary
                                                                        @else bg-secondary @endif">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $request->manager_remarks ?? '-' }}
                                </td>
                                <td class="text-center">
                                    @if (strtolower($request->status) == 'rejected')
                                        <a href="{{ route('leave-requests.edit', $request->id) }}"
                                            class="btn btn-outline-warning btn-sm" title="Edit Request">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    @elseif(strtolower($request->status) == 'submitted')
                                        <form action="{{ route('leave-requests.cancel', $request->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to cancel this leave request?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                title="Cancel Request">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No Leave Requests Found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>

        </div>
    @endsection
