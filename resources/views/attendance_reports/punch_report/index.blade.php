@extends('layouts.app')

@section('title', 'Punch Report | Unibs Tools')

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
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0 fw-semibold text-primary">
                    Employee Punch Report
                </h5>

                <a href="{{ route('attendance-reports.punch-report.export', request()->query()) }}"
                    class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel me-1"></i>
                    Export
                </a>
            </div>

            {{-- FILTERS --}}
            <div class="card filter-card border-0 mb-3">

                <div class="card-body py-2">

                    <form method="GET" action="{{ route('attendance-reports.punch-report.index') }}" id="filterForm">

                        @csrf

                        <div class="row align-items-end">

                            {{-- EMPLOYEE --}}
                            <div class="col-lg-4 col-md-6">

                                <label class="filter-label">
                                    Employee
                                </label>

                                <select name="employee" class="form-select compact-input">

                                    <option value="">All Employees</option>

                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ request('employee') == $employee->id ? 'selected' : '' }}>

                                            {{ $employee->name }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            {{-- FROM DATE --}}
                            <div class="col-lg-2 col-md-6">

                                <label class="filter-label">
                                    From Date
                                </label>

                                <input type="date" name="from_date" class="form-control compact-input filter-input"
                                    value="{{ request('from_date') }}">

                            </div>

                            {{-- TO DATE --}}
                            <div class="col-lg-2 col-md-6">

                                <label class="filter-label">
                                    To Date
                                </label>

                                <input type="date" name="to_date" class="form-control compact-input filter-input"
                                    value="{{ request('to_date') }}">

                            </div>

                            {{-- DATE RANGE --}}
                            <div class="col-lg-2 col-md-6">

                                <label class="filter-label">
                                    Date Range
                                </label>

                                <select name="date_range" id="dateRange" class="form-select compact-btn">

                                    <option value="custom"
                                        {{ ($filters['date_range'] ?? 'custom') == 'custom' ? 'selected' : '' }}>
                                        Custom
                                    </option>

                                    <option value="today"
                                        {{ ($filters['date_range'] ?? '') == 'today' ? 'selected' : '' }}>
                                        Today
                                    </option>

                                    <option value="this_week"
                                        {{ ($filters['date_range'] ?? '') == 'this_week' ? 'selected' : '' }}>
                                        This Week
                                    </option>

                                    <option value="last_two_weeks"
                                        {{ ($filters['date_range'] ?? '') == 'last_two_weeks' ? 'selected' : '' }}>
                                        Last Two Weeks
                                    </option>

                                    <option value="this_month"
                                        {{ ($filters['date_range'] ?? '') == 'this_month' ? 'selected' : '' }}>
                                        This Month
                                    </option>

                                    <option value="last_two_months"
                                        {{ ($filters['date_range'] ?? '') == 'last_two_months' ? 'selected' : '' }}>
                                        Last Two Months
                                    </option>

                                </select>

                            </div>

                            {{-- SEARCH --}}
                            <div class="col-lg-1 col-md-2 col-2 mb-1 text-center">

                                <button type="submit" id="searchBtn" class="btn btn-primary compact-search btn-sm">

                                    <i class="fa fa-search"></i>
                                    Search

                                </button>

                            </div>

                            {{-- RESET --}}
                            <div class="col-lg-1 col-md-2 col-2 mb-1 text-center">

                                <a href="{{ route('attendance-reports.punch-report.index') }}"
                                    class="btn btn-secondary compact-reset btn-sm">

                                    <i class="fa fa-rotate"></i>
                                    Reset

                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

            <hr class="my-0">

            <div class="row g-2">

                @forelse($reports as $report)
                    <div class="col-12">

                        <div class="card sales-card border-0">

                            <div class="card-body">
                                <div class="row align-items-center g-2">

                                    <!-- Employee -->
                                    <div class="col-lg-3">
                                        <h6 class="sales-name mb-1">
                                            {{ $report['user']->name }}
                                        </h6>

                                    </div>

                                    <!-- Attendance -->
                                    <div class="col-lg-6">
                                        <div class="d-flex flex-wrap gap-2">

                                            <span class="mini-badge">
                                                <i class="fa fa-calendar text-primary"></i>
                                                {{ $report['attendance_date']->format('d-M-Y') }}
                                            </span>

                                            <span class="mini-badge">
                                                <i class="fa fa-sign-in-alt text-success"></i>
                                                First In :
                                                {{ optional($report['first_in'])->format('h:i A') ?? '-' }}
                                            </span>

                                            <span class="mini-badge">
                                                <i class="fa fa-sign-out-alt text-danger"></i>
                                                Last Out :
                                                {{ optional($report['last_out'])->format('h:i A') ?? '-' }}
                                            </span>

                                            <span class="mini-badge">
                                                <i class="fa fa-clock text-warning"></i>
                                                {{ $report['working_hours'] }}
                                            </span>

                                        </div>
                                    </div>

                                    <!-- Button -->
                                    <div class="col-lg-3 text-lg-end">
                                        @if (count($report['sessions']))
                                            <button class="btn btn-sm btn-outline-primary" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#sessions{{ $loop->index }}"
                                                aria-expanded="false" aria-controls="sessions{{ $loop->index }}">
                                                <i class="fa fa-eye"></i>
                                                Details ({{ count($report['sessions']) }})
                                            </button>
                                        @endif
                                    </div>

                                </div>

                                @if (count($report['sessions']))
                                    <div class="collapse mt-3" id="sessions{{ $loop->index }}">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered table-hover align-middle mb-0">

                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="35%">Punch In</th>
                                                        <th width="35%">Punch Out</th>
                                                        <th width="30%">Duration</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($report['sessions'] as $session)
                                                        <tr>
                                                            <td>{{ $session['in']->format('h:i:s A') }}</td>

                                                            <td>
                                                                {{ optional($session['out'])->format('h:i:s A') ?? '-' }}
                                                            </td>

                                                            <td>
                                                                @php
                                                                    $totalMinutes = $session['minutes'];

                                                                    $hours = floor($totalMinutes / 60);
                                                                    $minutes = floor($totalMinutes % 60);
                                                                    $seconds = round(
                                                                        ($totalMinutes - floor($totalMinutes)) * 60,
                                                                    );
                                                                @endphp

                                                                {{ $hours }}h {{ $minutes }}m
                                                                {{ $seconds }}s
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                @endif

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="col-12">

                        <div class="alert alert-warning text-center">

                            No punch report found.

                        </div>

                    </div>
                @endforelse

            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            // SEARCH
            $('.search-box').on('keyup', function() {

                let value = $(this).val().toLowerCase();
                let target = $(this).data('target');

                $(target).each(function() {

                    $(this).toggle(
                        $(this).text().toLowerCase().includes(value)
                    );

                });

            });

            $('.select-all-checkbox').on('change', function() {

                let target = $(this).data('target');
                let checked = $(this).is(':checked');

                $(target).prop('checked', checked);

            });

            // UPDATE SELECT ALL STATUS
            function updateSelectAll(target, selectAll) {

                let total = $(target).length;
                let checked = $(target + ':checked').length;

                $(selectAll).prop(
                    'checked',
                    total > 0 && total === checked
                );
            }

            updateSelectAll(
                '.createdby-checkbox',
                '#selectAllCreatedBy'
            );

            $(document).on(
                'change',
                '.createdby-checkbox',
                function() {
                    updateSelectAll(
                        '.createdby-checkbox',
                        '#selectAllCreatedBy'
                    );
                }
            );
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const dateRange = document.getElementById('dateRange');
            const fromDate = document.querySelector('[name="from_date"]');
            const toDate = document.querySelector('[name="to_date"]');

            function formatDate(date) {

                let year = date.getFullYear();
                let month = String(date.getMonth() + 1).padStart(2, '0');
                let day = String(date.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            }

            function applyDateRange() {

                const today = new Date();

                let from = '';
                let to = formatDate(today);

                switch (dateRange.value) {

                    case 'today':

                        from = formatDate(today);
                        break;

                    case 'this_week':

                        let monday = new Date(today);

                        let day = monday.getDay();
                        let diff = day === 0 ? -6 : 1 - day;

                        monday.setDate(monday.getDate() + diff);

                        from = formatDate(monday);

                        break;

                    case 'last_two_weeks':

                        let startOfCurrentWeek = new Date(today);

                        let currentDay = startOfCurrentWeek.getDay();
                        let currentWeekDiff = currentDay === 0 ? -6 : 1 - currentDay;

                        startOfCurrentWeek.setDate(
                            startOfCurrentWeek.getDate() + currentWeekDiff
                        );

                        // Go back one full week
                        startOfCurrentWeek.setDate(
                            startOfCurrentWeek.getDate() - 7
                        );

                        from = formatDate(startOfCurrentWeek);

                        break;

                    case 'this_month':

                        let firstDay = new Date(
                            today.getFullYear(),
                            today.getMonth(),
                            1
                        );

                        from = formatDate(firstDay);

                        break;

                    case 'last_two_months':

                        let twoMonths = new Date(
                            today.getFullYear(),
                            today.getMonth() - 1,
                            1
                        );

                        from = formatDate(twoMonths);

                        break;

                    default:

                        fromDate.value = '';
                        toDate.value = '';

                        return;
                }

                fromDate.value = from;
                toDate.value = to;

                toggleSearchButton();
            }

            dateRange.addEventListener('change', applyDateRange);

            applyDateRange();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const searchBtn = document.getElementById('searchBtn');

            document.querySelectorAll(
                '[name="from_date"],[name="to_date"]'
            ).forEach(function(element) {

                element.addEventListener('input', toggleSearchButton);
                element.addEventListener('change', toggleSearchButton);

            });

            toggleSearchButton();
        });
    </script>

    <script>
        function toggleSearchButton() {

            let fromDate = document.querySelector('[name="from_date"]').value.trim();
            let toDate = document.querySelector('[name="to_date"]').value.trim();

            let enable =
                (fromDate !== '' && toDate !== '');

            searchBtn.disabled = !enable;
        }
    </script>
@endpush
