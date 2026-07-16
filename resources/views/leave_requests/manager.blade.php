@extends('layouts.app')

@section('title', 'Leave Applications')

@section('content')

    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <h4>Approver Applications</h4>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">

                <table class="table table-bordered table-striped">

                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th style="widows: 500px">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($leaveRequests as $leave)
                            <tr>

                                <td>
                                    <div class="d-flex align-items-center">


                                        <div>
                                            <div class="fw-semibold">
                                                {{ $leave->employee->name ?? '-' }}
                                            </div>


                                        </div>

                                    </div>
                                </td>

                                <td>{{ $leave->leaveType->leave_name ?? '' }}</td>

                                <td>{{ $leave->duration }}</td>

                                <td>{{ $leave->start_date->format('d M Y') }}</td>

                                <td>{{ $leave->end_date->format('d M Y') }}</td>

                                <td>
                                    @if (strtolower($leave->status) == 'submitted')
                                        <span class="badge bg-warning text-dark">
                                            Submitted
                                        </span>
                                    @elseif (strtolower($leave->status) == 'approved')
                                        <span class="badge bg-success">
                                            Approved
                                        </span>
                                    @elseif (strtolower($leave->status) == 'rejected')
                                        <span class="badge bg-danger">
                                            Rejected
                                        </span>
                                    @elseif (strtolower($leave->status) == 'cancelled')
                                        <span class="badge bg-secondary">
                                            Cancelled
                                        </span>
                                    @else
                                        <span class="badge bg-dark">
                                            {{ $leave->status ?? '-' }}
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $leave->remarks }}</td>

                                <td>
                                    @if (strtolower($leave->status) == 'submitted')
                                        <form method="POST">
                                            @csrf

                                            <div class="input-group input-group-sm">

                                                <input type="text" name="manager_remarks" class="form-control"
                                                    placeholder="Manager Remarks" required>

                                                <button type="submit"
                                                    formaction="{{ route('leave-requests.approved', $leave->id) }}"
                                                    class="btn btn-outline-success"
                                                    onclick="return confirm('Approve this leave request?')">

                                                    <i class="fa-solid fa-circle-check"></i>

                                                </button>

                                                <button type="submit"
                                                    formaction="{{ route('leave-requests.rejected', $leave->id) }}"
                                                    class="btn btn-outline-danger"
                                                    onclick="return confirm('Reject this leave request?')">

                                                    <i class="fa-solid fa-circle-xmark"></i>

                                                </button>

                                            </div>

                                        </form>
                                    @else
                                        {{ $leave->manager_remarks ?: '-' }}
                                    @endif
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="text-center">
                                    No Leave Applications Found
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

                {{ $leaveRequests->withQueryString()->links() }}
            </div>


        </div>

    </div>

@endsection
