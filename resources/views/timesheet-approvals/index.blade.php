@extends('layouts.app')

@section('title', 'Timesheet Approvals | Unibs Tools')

@section('content')

    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="fw-bold mb-0">
                <i class="fa-solid fa-user-check text-primary me-2"></i>
                Timesheet Approvals
            </h4>

            <small class="text-muted">
                Total Records : {{ $timesheets->total() }}
            </small>
        </div>

        <form method="GET">

            <select name="status" class="form-select" onchange="this.form.submit()">

                <option value="submitted" {{ $status == 'submitted' ? 'selected' : '' }}>
                    Submitted
                </option>

                <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>
                    Approved
                </option>

                <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>
                    Rejected
                </option>

                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>
                    All
                </option>

            </select>

        </form>

    </div>

    <div class="card border-0 shadow-sm rounded-4">

        <div class="table-responsive">

            <table class="table align-middle mb-0">

                <thead class="bg-light">

                    <tr>

                        <th>Employee</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Submitted At</th>
                        <th>Hours</th>
                        <th>Status</th>
                        <th width="120" class="text-center">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($timesheets as $timesheet)
                        <tr>

                            <td>
                                {{ $timesheet->user->name }}
                            </td>

                            <td>
                                {{ $timesheet->week_start?->format('d M Y') }}
                            </td>

                            <td>
                                {{ $timesheet->week_end?->format('d M Y') }}
                            </td>

                            <td>

                                @if ($timesheet->user_submission_at)
                                    {{ $timesheet->user_submission_at->format('d M Y h:i A') }}
                                @else
                                    -
                                @endif

                            </td>

                            <td>
                                {{ number_format($timesheet->total_hours, 2) }}
                            </td>

                            <td>

                                @if ($timesheet->status === 'submitted')
                                    <span class="badge bg-primary-subtle text-primary">
                                        Submitted
                                    </span>
                                @elseif($timesheet->status === 'approved')
                                    <span class="badge bg-success-subtle text-success">
                                        Approved
                                    </span>
                                @elseif($timesheet->status === 'draft')
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        Draft
                                    </span>
                                @elseif($timesheet->status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger">
                                        Rejected
                                    </span>
                                @endif

                            </td>

                            <td class="text-center">

                                <a href="{{ route('timesheet-approvals.show', $timesheet->id) }}"
                                    class="btn btn-light border btn-sm">

                                    <i class="fa fa-eye text-info"></i>

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="text-center py-4">

                                No Timesheets Found

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="card-footer bg-white border-0">

            {{ $timesheets->links() }}

        </div>

    </div>

@endsection
