<style>
    .approval-btn {
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 50%;
        color: #fff;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s;
        margin: 0 10px;
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

@section('title', 'Manager Comp Off Approvals | Unibs Tools')

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
                    Employee Comp Off Requests
                </h5>

                <form method="GET" action="{{ route('manager.compoff.requests') }}">
                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                        <option value="submitted" {{ request('status', 'submitted') == 'submitted' ? 'selected' : '' }}>
                            Submitted
                        </option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>
                            Approved
                        </option>
                        <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>
                            Rejected
                        </option>
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>
                            All
                        </option>
                    </select>
                </form>

            </div>

            <div class="card-body table-responsive">

                <table id="compoffTable" class="table table-bordered table-hover align-middle bg-white">

                    <thead class="table-dark">
                        <tr>
                            <th>Employee</th>
                            <th>Worked Date</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Manager Remarks</th>
                            {{-- <th>Remarks</th> --}}
                            <th>Manager Action Time</th>
                            <th width="180" class="text-center">Actions</th>
                        </tr>
                        <tr class="table-light filter-row">
                            <th><input type="text" class="form-control form-control-sm compoff-filter" data-col="0"></th>
                            <th><input type="text" class="form-control form-control-sm compoff-filter" data-col="1"></th>
                            <th><input type="text" class="form-control form-control-sm compoff-filter" data-col="2"></th>
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm compoff-filter" data-col="4"></th>
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm compoff-filter" data-col="6"></th>

                        </tr>
                    </thead>

                    <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td>{{ $request->employee?->name ?? '-' }}</td>
                                <td>{{ $request->day_worked ? \Carbon\Carbon::parse($request->day_worked)->format('d-M-Y') : '-' }}
                                </td>
                                <td>{{ $request->reason }}</td>
                                <td>
                                    <span class="badge 
                                                                  @if(strtolower($request->status) == 'approved') bg-success
                                                                  @elseif(strtolower($request->status) == 'rejected') bg-danger
                                                                  @elseif(strtolower($request->status) == 'submitted') bg-warning text-dark
                                                                  @else bg-secondary @endif">
                                        @if(strtolower($request->status) == 'submitted')
                                            Submitted
                                        @else
                                            {{ ucfirst($request->status) }}
                                        @endif
                                    </span>
                                </td>
                                {{-- <td>{{ $request->status !== 'submitted' ? ($request->manager_remarks ?? '-') : '-' }}</td>
                                --}}
                                <td>
                                    @if(strtolower($request->status) == 'submitted')
                                        <textarea name="manager_remarks" form="process-form-{{ $request->id }}" class="form-control"
                                            rows="2" required placeholder="Enter remarks..."></textarea>
                                    @else
                                        {{ $request->manager_remarks ?? '-' }}
                                    @endif
                                </td>
                                <td>
                                    {{ $request->manager_action_at ? \Carbon\Carbon::parse($request->manager_action_at)->format('d-M-Y H:i') : '-' }}
                                </td>
                                <td class="text-center">
                                    @if(strtolower($request->status) == 'submitted')
                                        <form id="process-form-{{ $request->id }}" method="POST"
                                            action="{{ route('manager.compoff.process', $request->id) }}"
                                            class="d-inline-flex gap-1">
                                            @csrf
                                            <button type="submit" name="status" value="approved" class="approval-btn approve-btn">
                                                ✓
                                            </button>

                                            <button type="submit" name="status" value="rejected" class="approval-btn reject-btn">
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
                                <td colspan="8" class="text-center text-muted">
                                    No Comp Off requests found.
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
            $('.compoff-filter').on('keyup change', function () {
                $('#compoffTable tbody tr').each(function () {
                    let show = true;

                    let employee = $(this).find('td:eq(0)').text().toLowerCase();
                    let employeeFilter = $('.compoff-filter[data-col="0"]').val().toLowerCase();

                    let date = $(this).find('td:eq(1)').text().toLowerCase();
                    let dateFilter = $('.compoff-filter[data-col="1"]').val().toLowerCase();

                    let reason = $(this).find('td:eq(2)').text().toLowerCase();
                    let reasonFilter = $('.compoff-filter[data-col="2"]').val().toLowerCase();

                    let remarks = $(this).find('td:eq(4)').text().toLowerCase();
                    let remarksFilter = $('.compoff-filter[data-col="4"]').val().toLowerCase();

                    let actionTime = $(this).find('td:eq(6)').text().toLowerCase();
                    let actionTimeFilter = $('.compoff-filter[data-col="6"]').val().toLowerCase();

                    if (employeeFilter && !employee.includes(employeeFilter)) show = false;
                    if (dateFilter && !date.includes(dateFilter)) show = false;
                    if (reasonFilter && !reason.includes(reasonFilter)) show = false;
                    if (remarksFilter && !remarks.includes(remarksFilter)) show = false;
                    if (actionTimeFilter && !actionTime.includes(actionTimeFilter)) show = false;

                    $(this).toggle(show);
                });
            });
        });
    </script>
@endpush