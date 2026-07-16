@extends('layouts.app')

@section('title', 'Timesheet Approval | Unibs Tools')

@section('content')
    <div class="container-fluid mt-4 mb-5">

        @include('partials.message')

        <!-- Header -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">

                <div>

                    <h4 class="fw-semibold mb-1">
                        <i class="fa-solid fa-user-check text-primary me-2"></i>
                        Timesheet Approval
                    </h4>

                    <div class="text-muted">
                        Employee :
                        <strong>
                            {{ $timesheet->user->name }}
                        </strong>
                    </div>

                    <div class="text-muted">
                        Week :
                        {{ $timesheet->week_start?->format('d M Y') }}
                        →
                        {{ $timesheet->week_end?->format('d M Y') }}
                    </div>

                    <div class="text-muted">
                        Submitted :
                        {{ $timesheet->user_submission_at?->format('d M Y h:i A') ?? '-' }}
                    </div>

                </div>

                <div>

                    <a href="{{ route('timesheet-approvals.index') }}" class="btn btn-outline-secondary">

                        <i class="fa-solid fa-arrow-left"></i>

                    </a>

                </div>

            </div>
        </div>

        <!-- Status + Total -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                @if ($timesheet->status === 'submitted')
                    <span class="badge bg-primary-subtle text-primary px-3 py-2">
                        Submitted
                    </span>
                @elseif($timesheet->status === 'approved')
                    <span class="badge bg-success-subtle text-success px-3 py-2">
                        Approved
                    </span>
                @elseif($timesheet->status === 'rejected')
                    <span class="badge bg-danger-subtle text-danger px-3 py-2">
                        Rejected
                    </span>
                @endif

            </div>

            <h5 class="mb-0">

                Total :

                <strong class="text-primary">
                    {{ number_format($totalHours, 2) }} hrs
                </strong>

            </h5>

        </div>

        <!-- User Remarks -->
        @if ($timesheet->user_remarks)
            <div class="card mb-3 border-0 shadow-sm">

                <div class="card-body bg-light">

                    <h6 class="fw-bold mb-2">

                        <i class="fa-solid fa-note-sticky me-1"></i>

                        Employee Remarks

                    </h6>

                    <p class="mb-0">
                        {{ $timesheet->user_remarks }}
                    </p>

                </div>

            </div>
        @endif

        <!-- Manager Action History -->
        @if ($timesheet->manager_action_at)

            <div class="card border-0 shadow-sm mb-3">

                <div class="card-body">

                    <h6 class="fw-bold mb-3">

                        <i class="fa-solid fa-user-shield me-1"></i>

                        Manager Action

                    </h6>

                    <div class="row">

                        <div class="col-md-4">

                            <strong>Manager</strong>

                            <div>
                                {{ $timesheet->manager->name ?? '-' }}
                            </div>

                        </div>

                        <div class="col-md-4">

                            <strong>Status</strong>

                            <div>
                                {{ ucfirst($timesheet->status) }}
                            </div>

                        </div>

                        <div class="col-md-4">

                            <strong>Action At</strong>

                            <div>
                                {{ $timesheet->manager_action_at?->format('d M Y h:i A') }}
                            </div>

                        </div>

                    </div>

                    @if ($timesheet->manager_remarks)
                        <hr>

                        <strong>Manager Remarks</strong>

                        <p class="mb-0 mt-2">
                            {{ $timesheet->manager_remarks }}
                        </p>
                    @endif

                </div>

            </div>

        @endif

        <!-- Entries -->
        @foreach ($entries->groupBy('work_date') as $date => $dayEntries)
            <div class="card mb-4 shadow-sm border-0">

                <div class="card-header bg-white d-flex justify-content-between align-items-center">

                    <strong>

                        {{ \Carbon\Carbon::parse($date)->format('d M Y (D)') }}

                    </strong>

                    <span class="text-muted">

                        Day Total :

                        <strong>

                            {{ number_format($dayEntries->sum('hours'), 2) }}

                            hrs

                        </strong>

                    </span>

                </div>

                <div class="table-responsive">

                    <table class="table table-bordered align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>Project</th>

                                <th>Activity</th>

                                <th>Sub Activity</th>

                                <th>Customer</th>

                                <th>Request #</th>

                                <th class="text-end">Hours</th>

                                <th>Task Remarks</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($dayEntries as $entry)
                                <tr>

                                    <td>
                                        {{ $entry->subActivity->activity->project->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $entry->subActivity->activity->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $entry->subActivity->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $entry->customer->customer ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $entry->request_id ?? '-' }}
                                    </td>

                                    <td class="text-end">
                                        {{ number_format($entry->hours, 2) }}
                                    </td>

                                    <td>
                                        {{ $entry->remarks ?? '-' }}
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>
        @endforeach

        <!-- Approval Actions -->
        @if ($timesheet->status === 'submitted')
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="fa-solid fa-check-circle me-1"></i>
                        Approval Action
                    </h6>
                    <hr>

                    <form id="actionForm" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Manager Remarks
                                <span id="remarksRequired" class="text-danger d-none">*</span>
                            </label>
                            <textarea name="manager_remarks" id="managerRemarks" class="form-control" rows="3"
                                placeholder="Add remarks here..."></textarea>
                            <small class="text-muted" id="remarksHelp">
                                Remarks are optional for approval but required for rejection.
                            </small>
                            <div class="invalid-feedback" id="remarksError">
                                Please provide a reason for rejecting the timesheet (minimum 5 characters).
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" formaction="{{ route('timesheet-approvals.approve', $timesheet->id) }}"
                                class="btn btn-success" id="approveBtn">
                                <i class="fa fa-check me-1"></i>
                                Approve
                            </button>

                            <button type="submit" formaction="{{ route('timesheet-approvals.reject', $timesheet->id) }}"
                                class="btn btn-danger" id="rejectBtn">
                                <i class="fa fa-times me-1"></i>
                                Reject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('actionForm');
                    const remarksField = document.getElementById('managerRemarks');
                    const approveBtn = document.getElementById('approveBtn');
                    const rejectBtn = document.getElementById('rejectBtn');
                    const remarksError = document.getElementById('remarksError');
                    const remarksRequired = document.getElementById('remarksRequired');
                    const remarksHelp = document.getElementById('remarksHelp');

                    // Show required indicator when focusing on remarks
                    remarksField.addEventListener('focus', function() {
                        remarksRequired.classList.remove('d-none');
                    });

                    remarksField.addEventListener('blur', function() {
                        if (!this.value.trim()) {
                            remarksRequired.classList.add('d-none');
                        }
                    });

                    // Clear validation state when user types
                    remarksField.addEventListener('input', function() {
                        if (this.classList.contains('is-invalid')) {
                            this.classList.remove('is-invalid');
                            remarksError.style.display = 'none';
                            remarksHelp.style.color = '';
                        }
                    });

                    // Handle Approve button click
                    approveBtn.addEventListener('click', function(e) {
                        // Reset validation state
                        remarksField.classList.remove('is-invalid');
                        remarksError.style.display = 'none';
                        remarksHelp.style.color = '';

                        // No validation needed for approval, just confirm
                        if (!confirm('Are you sure you want to approve this timesheet?')) {
                            e.preventDefault();
                            return false;
                        }

                        // Allow form submission
                        return true;
                    });

                    // Handle Reject button click
                    rejectBtn.addEventListener('click', function(e) {
                        const remarks = remarksField.value.trim();

                        // Reset validation state
                        remarksField.classList.remove('is-invalid');
                        remarksError.style.display = 'none';
                        remarksHelp.style.color = '';

                        // Validate remarks for rejection
                        if (!remarks) {
                            e.preventDefault();
                            remarksField.classList.add('is-invalid');
                            remarksError.textContent = 'Please provide a reason for rejecting the timesheet.';
                            remarksError.style.display = 'block';
                            remarksHelp.style.color = '#dc3545';
                            remarksField.focus();
                            return false;
                        }

                        if (remarks.length < 5) {
                            e.preventDefault();
                            remarksField.classList.add('is-invalid');
                            remarksError.textContent = 'Rejection reason must be at least 5 characters.';
                            remarksError.style.display = 'block';
                            remarksHelp.style.color = '#dc3545';
                            remarksField.focus();
                            return false;
                        }

                        // If validation passes, confirm rejection
                        if (!confirm('Are you sure you want to reject this timesheet?')) {
                            e.preventDefault();
                            return false;
                        }

                        // Allow form submission
                        return true;
                    });

                    // Form submit handler for additional safety
                    form.addEventListener('submit', function(e) {
                        // Check which button was clicked
                        const activeButton = document.activeElement;

                        if (activeButton && activeButton.id === 'rejectBtn') {
                            const remarks = remarksField.value.trim();

                            // Reset validation state
                            remarksField.classList.remove('is-invalid');
                            remarksError.style.display = 'none';
                            remarksHelp.style.color = '';

                            if (!remarks) {
                                e.preventDefault();
                                remarksField.classList.add('is-invalid');
                                remarksError.textContent = 'Please provide a reason for rejecting the timesheet.';
                                remarksError.style.display = 'block';
                                remarksHelp.style.color = '#dc3545';
                                remarksField.focus();
                                return false;
                            }

                            if (remarks.length < 5) {
                                e.preventDefault();
                                remarksField.classList.add('is-invalid');
                                remarksError.textContent = 'Rejection reason must be at least 5 characters.';
                                remarksError.style.display = 'block';
                                remarksHelp.style.color = '#dc3545';
                                remarksField.focus();
                                return false;
                            }
                        }
                    });
                });
            </script>

            <style>
                #remarksError {
                    display: none;
                }

                .is-invalid {
                    border-color: #dc3545 !important;
                }

                .is-invalid:focus {
                    border-color: #dc3545 !important;
                    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
                }

                #remarksHelp.text-danger {
                    color: #dc3545 !important;
                }
            </style>
        @endpush



        <!-- Pagination -->
        <div class="mt-3">

            {{ $entries->links() }}

        </div>

    </div>
@endsection
