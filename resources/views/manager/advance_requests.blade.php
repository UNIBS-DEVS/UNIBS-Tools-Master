@extends('layouts.app')

@section('title', 'Manager Approval | Unibs Tools')

@push('styles')

    <style>
        .table tbody tr td,
        .table thead tr th {
            padding: .15rem .5rem;
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

                <h5 class="mb-0 fw-semibold">
                    Employee advance Requests
                </h5>

                <form method="GET" action="{{ route('manager.advances.index') }}">

                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">

                        <option value="Submitted" {{ request('status', 'Submitted') == 'Submitted' ? 'selected' : '' }}>
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

                <table id="managerTable" class="table table-bordered table-hover align-middle bg-white">

                    <thead class="table-dark">

                        <tr>
                            <th>Employee</th>
                            <th>Request Date</th>
                            <th>Requested Amount</th>
                            <th>Advance Reason</th>
                            <th>Status</th>
                            <th>Manager Remarks</th>
                            <th>Manager Action Time</th>
                            <th width="100" class="text-center">
                                Actions
                            </th>
                        </tr>

                        <tr class="table-light filter-row">

                            <th>
                                <input type="text" class="form-control form-control-sm manager-filter" data-col="0">
                            </th>

                            <th>
                                <input type="text" class="form-control form-control-sm manager-filter" data-col="1">
                            </th>

                            <th>
                                <input type="text" class="form-control form-control-sm manager-filter" data-col="2">
                            </th>

                            <th>
                                <input type="text" class="form-control form-control-sm manager-filter" data-col="3">
                            </th>

                            <th></th>

                            <th>
                                <input type="text" class="form-control form-control-sm manager-filter" data-col="5">
                            </th>

                            <th>
                                <input type="text" class="form-control form-control-sm manager-filter" data-col="6">
                            </th>



                            <th></th>

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
                                    ₹ {{ number_format($advance->items_sum_requested_amount ?? 0, 2) }}
                                </td>

                                <td>
                                    {{ $advance->advance_reason }}
                                </td>



                                {{--
                                <td>

                                    @php
                                    $hasAttachment = false;
                                    @endphp

                                    @foreach($advance->items as $item)

                                    @foreach($item->attachments as $attachment)

                                    @php
                                    $hasAttachment = true;
                                    @endphp

                                    <a href="{{ asset('storage/' . $attachment->attachment_path) }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm mb-1">

                                        <i class="fa fa-file"></i>

                                    </a>

                                    @endforeach

                                    @endforeach

                                    @if(!$hasAttachment)

                                    <span class="text-muted">
                                        No File
                                    </span>

                                    @endif

                                </td> --}}

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
                                                    @endif">

                                        {{ $advance->status }}

                                    </span>

                                </td>

                                <td>
                                    {{ $advance->manager_remarks ?? '-' }}
                                </td>

                                <td>
                                    {{ $advance->manager_action_at ? \Carbon\Carbon::parse($advance->manager_action_at)->format('d/M/Y H:i') : '-' }}
                                </td>

                                <td class="text-center">

                                    <a href="{{ route('manager.advances.show', $advance->id) }}"
                                        class="btn btn-outline-info btn-sm" title="View Details">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9" class="text-center text-muted">

                                    No advance requests found.

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

            $('.manager-filter').on('keyup change', function () {

                $('#managerTable tbody tr').each(function () {

                    let show = true;

                    // Employee
                    let employee = $(this).find('td:eq(0)').text().toLowerCase();
                    let employeeFilter = $('.manager-filter[data-col="0"]').val().toLowerCase();

                    // Date
                    let date = $(this).find('td:eq(1)').text().toLowerCase();
                    let dateFilter = $('.manager-filter[data-col="1"]').val().toLowerCase();

                    // Amount
                    let amount = $(this).find('td:eq(2)').text().toLowerCase();
                    let amountFilter = $('.manager-filter[data-col="2"]').val().toLowerCase();

                    // Title
                    let title = $(this).find('td:eq(3)').text().toLowerCase();
                    let titleFilter = $('.manager-filter[data-col="3"]').val().toLowerCase();

                    // Status
                    let status = $(this).find('td:eq(5)').text().toLowerCase();
                    let statusFilter = $('.manager-filter[data-col="5"]').val().toLowerCase();

                    // Manager Remarks
                    let managerRemarks = $(this).find('td:eq(6)').text().toLowerCase();
                    let managerRemarksFilter = $('.manager-filter[data-col="6"]').val().toLowerCase();

                    // Manager Action Time
                    let managerActionTime = $(this).find('td:eq(7)').text().toLowerCase();
                    let managerActionTimeFilter = $('.manager-filter[data-col="7"]').val().toLowerCase();

                    if (employeeFilter && !employee.includes(employeeFilter))
                        show = false;

                    if (dateFilter && !date.includes(dateFilter))
                        show = false;

                    if (amountFilter && !amount.includes(amountFilter))
                        show = false;

                    if (titleFilter && !title.includes(titleFilter))
                        show = false;

                    if (statusFilter && !status.includes(statusFilter))
                        show = false;

                    if (managerRemarksFilter && !managerRemarks.includes(managerRemarksFilter))
                        show = false;

                    if (managerActionTimeFilter && !managerActionTime.includes(managerActionTimeFilter))
                        show = false;

                    $(this).toggle(show);

                });

            });

        });
    </script>
@endpush