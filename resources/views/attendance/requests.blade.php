<style>
    .approval-btn {
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 50%;
        color: #fff;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s;
        margin: 0 5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .approve-btn {
        background: #1dbb1d;
    }

    .reject-btn {
        background: #e20d0d;
    }

    .approval-btn:hover {
        transform: scale(1.08);
    }
</style>

@extends('layouts.app')

@section('title', 'Manager Attendance Approvals | Unibs Tools')

@push('styles')
    <style>
        .table tbody tr td,
        .table thead tr th {
            padding: .3rem .5rem;
            font-size: 13px;
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <div class="container mt-4">

        @include('partials.message')

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-semibold text-primary">
                    Employee Punch In Requests
                </h5>

                <form method="GET" action="{{ route('manager.attendance.requests') }}">
                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                        <option value="pending" {{ request('status', 'pending') == 'pending' ? 'selected' : '' }}>
                            Pending
                        </option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                            Approved
                        </option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                            Rejected
                        </option>
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>
                            All
                        </option>
                    </select>
                </form>

            </div>

            <div class="card-body table-responsive">

                <table id="attendanceRequestsTable" class="table table-bordered table-hover align-middle bg-white">

                    <thead class="table-dark">
                        <tr>
                            <th>Employee</th>
                            <th>Punch Date</th>
                            <th>Punch Time</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Remarks / Reason</th>
                            <th>Manager Remarks</th>
                            <th width="150" class="text-center">Actions</th>
                        </tr>
                        <tr class="table-light filter-row">
                            <th><input type="text" class="form-control form-control-sm att-filter" data-col="0"></th>
                            <th><input type="text" class="form-control form-control-sm att-filter" data-col="1"></th>
                            <th><input type="text" class="form-control form-control-sm att-filter" data-col="2"></th>
                            <th><input type="text" class="form-control form-control-sm att-filter" data-col="3"></th>
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm att-filter" data-col="5"></th>
                            <th><input type="text" class="form-control form-control-sm att-filter" data-col="6"></th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td>{{ $request->user?->name ?? '-' }}</td>
                                <td>{{ $request->attendance_date ? \Carbon\Carbon::parse($request->attendance_date)->format('d-M-Y') : '-' }}</td>
                                <td class="fw-bold">
                                    {{ $request->punch_at ? $request->punch_at->format('h:i A') : '-' }} ({{ ucfirst($request->punch_type) }})
                                </td>
                                <td>{{ $request->attendanceLocation?->location_name ?? '-' }}</td>
                                <td>
                                    <span class="badge 
                                        @if(strtolower($request->status) == 'approved') bg-success
                                        @elseif(strtolower($request->status) == 'rejected') bg-danger
                                        @elseif(strtolower($request->status) == 'pending') bg-warning text-dark
                                        @else bg-secondary @endif">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $request->remarks ?? 'None' }}</small>
                                </td>
                                <td>
                                    @if(strtolower($request->status) == 'pending')
                                        <textarea name="manager_remarks" form="process-form-{{ $request->id }}" class="form-control form-control-sm"
                                            rows="2" required placeholder="Enter remarks..."></textarea>
                                    @else
                                        {{ $request->remarks ?? '-' }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(strtolower($request->status) == 'pending')
                                        <form id="process-form-{{ $request->id }}" method="POST"
                                            action="{{ route('manager.attendance.process', $request->id) }}" class="d-inline-flex gap-1">
                                            @csrf
                                            <button type="submit" name="status" value="approved" class="approval-btn approve-btn" title="Approve">
                                                ✓
                                            </button>

                                            <button type="submit" name="status" value="rejected" class="approval-btn reject-btn" title="Reject">
                                                ✕
                                            </button>
                                        </form>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No punch requests found.
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

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.att-filter').on('keyup change', function () {
                $('#attendanceRequestsTable tbody tr').each(function () {
                    let show = true;

                    let employee = $(this).find('td:eq(0)').text().toLowerCase();
                    let employeeFilter = $('.att-filter[data-col="0"]').val().toLowerCase();

                    let date = $(this).find('td:eq(1)').text().toLowerCase();
                    let dateFilter = $('.att-filter[data-col="1"]').val().toLowerCase();

                    let time = $(this).find('td:eq(2)').text().toLowerCase();
                    let timeFilter = $('.att-filter[data-col="2"]').val().toLowerCase();

                    let location = $(this).find('td:eq(3)').text().toLowerCase();
                    let locationFilter = $('.att-filter[data-col="3"]').val().toLowerCase();

                    let remarks = $(this).find('td:eq(5)').text().toLowerCase();
                    let remarksFilter = $('.att-filter[data-col="5"]').val().toLowerCase();

                    let mRemarks = $(this).find('td:eq(6)').text().toLowerCase();
                    let mRemarksFilter = $('.att-filter[data-col="6"]').val().toLowerCase();

                    if (employeeFilter && !employee.includes(employeeFilter)) show = false;
                    if (dateFilter && !date.includes(dateFilter)) show = false;
                    if (timeFilter && !time.includes(timeFilter)) show = false;
                    if (locationFilter && !location.includes(locationFilter)) show = false;
                    if (remarksFilter && !remarks.includes(remarksFilter)) show = false;
                    if (mRemarksFilter && !mRemarks.includes(mRemarksFilter)) show = false;

                    $(this).toggle(show);
                });
            });
        });
    </script>
@endpush
