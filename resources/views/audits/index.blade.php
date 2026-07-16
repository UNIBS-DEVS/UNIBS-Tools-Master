@extends('layouts.app')

@section('title', 'Audit Report | Unibs Tools')

@push('styles')
    <style>
        body {
            background: #f4f6fb;
            color: #2b2f38;
        }

        .sales-header {
            margin-bottom: 10px !important;
        }

        .page-title {
            font-size: 20px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -.3px;
        }

        .page-subtitle {
            font-size: 10px;
            color: #7b8190;
        }

        .top-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .filter-card {
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 2px 18px rgba(0, 0, 0, .04);
        }

        .filter-label {
            font-size: 10px;
            font-weight: 700;
            color: #6c757d;
            margin-bottom: 4px;
            display: block;
        }

        .compact-btn,
        .compact-input {
            /* height: 36px; */
            border-radius: 10px;
            font-size: 11px;
            border: 1px solid #dfe3e8;
            background: #fff;
        }

        .compact-btn {
            font-weight: 600;
        }

        .compact-reset {
            border-radius: 10px;
        }

        .compact-search {
            border-radius: 10px;
        }

        .sales-card {
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 2px 16px rgba(0, 0, 0, .04);
            border: 1px solid #eef1f4;
            transition: .2s ease;
        }

        .sales-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 26px rgba(0, 0, 0, .08);
        }

        .sales-card .card-body {
            padding: 14px;
        }

        .sales-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }

        .sales-name {
            font-size: 15px;
            font-weight: 700;
            color: #0d6efd;
            margin-bottom: 2px;
        }

        .sales-email {
            font-size: 10px;
            color: #7b8190;
        }

        .mini-badge {
            background: #f8f9fa;
            border: 1px solid #edf0f2;
            border-radius: 8px;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 4px;
        }

        .status-badge {
            padding: 4px 9px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 700;
        }

        .action-btn {
            width: 31px;
            height: 31px;
            border-radius: 10px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
        }

        .pagination {
            gap: 4px;
        }

        .pagination .page-link {
            border: 0;
            min-width: 31px;
            height: 31px;
            border-radius: 9px !important;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            border: 0;
            border-radius: 14px;
            padding: 8px !important;
            min-width: 100%;
            width: 100%;
            margin-top: 6px !important;
            max-height: 280px;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 0 12px 28px rgba(0, 0, 0, .10);
            z-index: 1055;
        }

        .dropdown-menu::-webkit-scrollbar {
            width: 5px;
        }

        .dropdown-menu::-webkit-scrollbar-thumb {
            background: #d0d7de;
            border-radius: 20px;
        }

        .dropdown-menu .form-check {
            margin: 0;
            min-height: auto;
            padding: 7px 8px 7px 30px;
            border-radius: 10px;
            transition: .15s ease;
            position: relative;
        }

        .dropdown-menu .form-check:hover {
            background: #f6f8fb;
        }

        .dropdown-menu .form-check-input {
            position: absolute;
            left: 8px;
            top: 9px;
            margin: 0;
            width: 15px;
            height: 15px;
            cursor: pointer;
        }

        .dropdown-menu .form-check-label {
            font-size: 11px;
            font-weight: 500;
            color: #495057;
            cursor: pointer;
            display: block;
            line-height: 1.3;
        }

        @media(max-width:768px) {

            .page-title {
                font-size: 17px;
            }

            .sales-avatar {
                width: 48px;
                height: 48px;
            }

            .sales-name {
                font-size: 13px;
            }

            .action-btn {
                width: 28px;
                height: 28px;
            }

        }

        .dot {
            width: 4px;
            height: 4px;
            background: #98a2b3;
            border-radius: 50%;
        }

        .table tbody tr td,
        .table thead tr th {
            padding: .15rem .5rem;
            font-size: 13px;
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')

    {{-- Flash Messages --}}
    @include('partials.message')

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-2 sales-header">

        <div class="lh-sm">

            <h4 class="page-title mb-0">
                Audit Report
            </h4>

        </div>

        <div class="d-flex align-items-center gap-1">

            <a href="{{ route('audit.export', request()->query()) }}" class="btn btn-success top-btn">

                <i class="fa fa-file-excel"></i>

            </a>

        </div>

    </div>

    {{-- FILTERS --}}
    <div class="card filter-card border-0 mb-3">

        <div class="card-body py-2">

            <form method="POST" action="{{ route('audit.filter') }}" id="filterForm">

                @csrf

                <div class="row g-2 align-items-end">

                    {{-- {{}} --}}
                    {{-- CUSTOMER --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">Customers</label>

                        @php

                            $selectedCustomers = !empty($filters['customer_id'])
                                ? (array) $filters['customer_id']
                                : $customerOptions->pluck('id')->toArray();
                        @endphp

                        <div class="dropdown">

                            <button class="btn compact-btn dropdown-toggle w-100" type="button"
                                data-bs-toggle="dropdown">Select Customers</button>

                            <div class="dropdown-menu w-100 p-2 compact-dropdown">

                                <input type="text" class="form-control compact-input mb-2 search-box"
                                    data-target=".customer-item" placeholder="Search customer...">

                                <div class="form-check border-bottom pb-2 mb-2">

                                    <input type="checkbox" class="form-check-input select-all-checkbox"
                                        data-target=".customer-checkbox" id="selectAllCustomers">

                                    <label class="form-check-label fw-bold" for="selectAllCustomers">Select All</label>

                                </div>

                                <div style="max-height:250px; overflow-y:auto;">

                                    @foreach ($customerOptions as $customer)
                                        <div class="form-check customer-item">

                                            <input class="form-check-input filter-input customer-checkbox search-box"
                                                type="checkbox" name="customer_id[]" value="{{ $customer->id }}"
                                                id="customer_{{ $customer->id }}"
                                                {{ in_array((string) $customer->id, array_map('strval', $selectedCustomers)) ? 'checked' : '' }}>

                                            <label class="form-check-label customer-label"
                                                for="customer_{{ $customer->id }}">

                                                {{ $customer->customer }}

                                            </label>

                                        </div>
                                    @endforeach

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- Candidate --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">
                            Candidate
                        </label>

                        <input type="text" name="candidate" class="form-control compact-input filter-input"
                            value="{{ $filters['candidate'] ?? '' }}" placeholder="Search candidate">

                    </div>

                    {{-- Skill --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">
                            Skill
                        </label>

                        <input type="text" name="skill" class="form-control compact-input filter-input"
                            value="{{ $filters['skill'] ?? '' }}" placeholder="Search skill">

                    </div>

                    {{-- Job --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">
                            Job
                        </label>

                        <input type="text" name="job" class="form-control compact-input filter-input"
                            value="{{ $filters['job'] ?? '' }}" placeholder="Search job">

                    </div>

                    {{-- CREATED BY --}}
                    @php
                        $selectedCreatedBy = array_key_exists('created_by', $filters)
                            ? (array) $filters['created_by']
                            : $createdByOptions->pluck('id')->toArray();
                    @endphp

                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">
                            Recruiter
                        </label>

                        <div class="dropdown">

                            <button class="btn compact-btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">

                                Select Recruiter

                            </button>

                            <div class="dropdown-menu w-100 p-2 compact-dropdown">

                                <input type="text" class="form-control compact-input mb-2 search-box"
                                    data-target=".createdby-item" placeholder="Search recruiter...">

                                <div class="form-check border-bottom pb-2 mb-2">

                                    <input type="checkbox" class="form-check-input select-all-checkbox"
                                        data-target=".createdby-checkbox" id="selectAllCreatedBy">

                                    <label class="form-check-label fw-bold" for="selectAllCreatedBy">

                                        Select All

                                    </label>

                                </div>

                                <div style="max-height:250px;overflow-y:auto;">

                                    @foreach ($createdByOptions as $user)
                                        <div class="form-check createdby-item">

                                            <input class="form-check-input createdby-checkbox" type="checkbox"
                                                name="created_by[]" value="{{ $user->id }}"
                                                id="created_by_{{ $user->id }}"
                                                {{ in_array((string) $user->id, array_map('strval', $selectedCreatedBy)) ? 'checked' : '' }}>

                                            <label class="form-check-label" for="created_by_{{ $user->id }}">

                                                {{ $user->name }}

                                            </label>

                                        </div>
                                    @endforeach

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- FROM DATE --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">
                            From Date
                        </label>

                        <input type="date" name="from_date" class="form-control compact-input filter-input"
                            value="{{ $filters['from_date'] ?? '' }}">

                    </div>

                    {{-- TO DATE --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">
                            To Date
                        </label>

                        <input type="date" name="to_date" class="form-control compact-input filter-input"
                            value="{{ $filters['to_date'] ?? '' }}">

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

                            <option value="today" {{ ($filters['date_range'] ?? '') == 'today' ? 'selected' : '' }}>
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

                        <a href="{{ route('audit.reset') }}" class="btn btn-secondary compact-reset btn-sm">

                            <i class="fa fa-rotate"></i>
                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>

    {{-- CANDIDATES --}}
    <div class="row g-2">
        {{-- AUDIT TABLE --}}

        <h6 class="">
            Change Events
        </h6>

        @if ($changes->count())

            <div class="card sales-card border-0">

                <div class="card-body p-2">

                    <div class="table-responsive">

                        <table id="auditTable" class="table table-bordered table-hover align-middle mb-0">

                            <thead class="table-dark">

                                <tr>
                                    <th>Customer</th>
                                    <th>Job</th>
                                    <th>Candidate</th>
                                    <th>Skill</th>
                                    <th>Field</th>
                                    <th>Old Value</th>
                                    <th>New Value</th>
                                    <th>Recruiter</th>
                                    <th>Timestamp</th>
                                </tr>

                                <tr class="table-light">

                                    <th>
                                        <input type="text" class="form-control form-control-sm audit-filter"
                                            data-col="0">
                                    </th>

                                    <th>
                                        <input type="text" class="form-control form-control-sm audit-filter"
                                            data-col="1">
                                    </th>

                                    <th>
                                        <input type="text" class="form-control form-control-sm audit-filter"
                                            data-col="2">
                                    </th>

                                    <th>
                                        <input type="text" class="form-control form-control-sm audit-filter"
                                            data-col="3">
                                    </th>

                                    <th>
                                        <input type="text" class="form-control form-control-sm audit-filter"
                                            data-col="4">
                                    </th>

                                    <th>
                                        <input type="text" class="form-control form-control-sm audit-filter"
                                            data-col="5">
                                    </th>

                                    <th>
                                        <input type="text" class="form-control form-control-sm audit-filter"
                                            data-col="6">
                                    </th>

                                    <th>
                                        <input type="text" class="form-control form-control-sm audit-filter"
                                            data-col="7">
                                    </th>

                                    <th>
                                        <input type="text" class="form-control form-control-sm audit-filter"
                                            data-col="8">
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($changes as $change)
                                    <tr>

                                        <td>
                                            {{ $change->candidate?->customer?->customer ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $change->candidate?->position?->position ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $change->candidate?->candidate_name ?? '-' }}
                                        </td>

                                        <td>
                                            {{ Str::limit($change->candidate?->skill ?? '-', 40) }}
                                        </td>



                                        <td>
                                            <span class="fw-bold text-dark">
                                                {{ $change->changed_field }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ Str::limit($change->old_value, 50) }}
                                        </td>

                                        <td>
                                            {{ Str::limit($change->new_value, 50) }}
                                        </td>

                                        <td>
                                            {{ $change->creator?->name ?? '-' }}
                                        </td>

                                        <td>
                                            {{ optional($change->created_at)->format('d M Y h:i A') }}
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            {{-- PAGINATION --}}
            <div class="mt-3">

                {{ $changes->links() }}

            </div>
        @else
            <div class="alert alert-warning text-center rounded-3 py-2">

                No audit records found

            </div>

        @endif


        <hr class="my-4">

        <h6 class="">
            Remarks History
        </h6>

        @if ($remarks->count())

            <div class="card sales-card border-0">

                <div class="card-body p-2">

                    <div class="table-responsive">

                        <table id="remarksTable" class="table table-bordered table-hover align-middle mb-0">

                            <thead class="table-dark">

                                <tr>
                                    <th>Customer</th>
                                    <th>Job</th>
                                    <th>Candidate</th>

                                    <th>Type</th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                    <th>Recruiter</th>
                                    <th>Timestamp</th>
                                </tr>

                                <tr class="table-light">

                                    <th>
                                        <input type="text" class="form-control form-control-sm remarks-filter"
                                            data-col="0">
                                    </th>

                                    <th>
                                        <input type="text" class="form-control form-control-sm remarks-filter"
                                            data-col="1">
                                    </th>

                                    <th>
                                        <input type="text" class="form-control form-control-sm remarks-filter"
                                            data-col="2">
                                    </th>

                                    <th>
                                        <input type="text" class="form-control form-control-sm remarks-filter"
                                            data-col="3">
                                    </th>

                                    <th>
                                        <input type="text" class="form-control form-control-sm remarks-filter"
                                            data-col="4">
                                    </th>

                                    <th>
                                        <input type="text" class="form-control form-control-sm remarks-filter"
                                            data-col="5">
                                    </th>

                                    <th>
                                        <input type="text" class="form-control form-control-sm remarks-filter"
                                            data-col="6">
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($remarks as $remark)
                                    <tr>

                                        <td>
                                            {{ $remark->candidate?->customer?->customer ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $remark->candidate?->position?->position ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $remark->candidate?->candidate_name ?? '-' }}
                                        </td>


                                        <td> {{ $remark->remark_type }}</td>


                                        <td style="min-width:350px">

                                            {{ $remark->remarks }}

                                        </td>

                                        <td>

                                            <span class="fw-bold text-dark">

                                                {{ $remark->candidate?->status ?? '-' }}

                                            </span>

                                        </td>

                                        <td>
                                            {{ $remark->creator?->name ?? '-' }}
                                        </td>

                                        <td>
                                            {{ optional($remark->created_at)->format('d M Y h:i A') }}
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <div class="mt-3">

                {{ $remarks->links() }}

            </div>
        @else
            <div class="alert alert-warning text-center">

                No remarks history found

            </div>

        @endif

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
                '.status-checkbox',
                '#selectAllStatus'
            );

            updateSelectAll(
                '.notice-checkbox',
                '#selectAllNotice'
            );

            updateSelectAll(
                '.createdby-checkbox',
                '#selectAllCreatedBy'
            );

            $(document).on(
                'change',
                '.customer-checkbox,.status-checkbox,.notice-checkbox,.createdby-checkbox',
                function() {

                    updateSelectAll(
                        '.customer-checkbox',
                        '#selectAllCustomers'
                    );

                    updateSelectAll(
                        '.status-checkbox',
                        '#selectAllStatus'
                    );

                    updateSelectAll(
                        '.notice-checkbox',
                        '#selectAllNotice'
                    );

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
                '[name="candidate"],[name="skill"],[name="mobile"],[name="email"],[name="from_date"],[name="to_date"]'
            ).forEach(function(element) {

                element.addEventListener('input', toggleSearchButton);
                element.addEventListener('change', toggleSearchButton);

            });

            toggleSearchButton();
        });
    </script>

    <script>
        function toggleSearchButton() {

            let candidate = document.querySelector('[name="candidate"]').value.trim();
            let skill = document.querySelector('[name="skill"]').value.trim();
            let mobile = document.querySelector('[name="mobile"]').value.trim();
            let email = document.querySelector('[name="email"]').value.trim();
            let fromDate = document.querySelector('[name="from_date"]').value.trim();
            let toDate = document.querySelector('[name="to_date"]').value.trim();

            let enable =
                candidate !== '' ||
                skill !== '' ||
                mobile !== '' ||
                email !== '' ||
                (fromDate !== '' && toDate !== '');

            searchBtn.disabled = !enable;
        }
    </script>

    <script>
        $(document).ready(function() {

            $('.audit-filter').on('keyup change', function() {

                let filters = [];

                $('.audit-filter').each(function() {
                    filters.push($(this).val().toLowerCase().trim());
                });

                $('#auditTable tbody tr').each(function() {

                    let show = true;

                    $(this).find('td').each(function(index) {

                        if (index >= 9) {
                            return false;
                        }

                        let filter = filters[index];

                        if (filter !== '') {

                            let text = $(this).text().toLowerCase().trim();

                            if (text.indexOf(filter) === -1) {
                                show = false;
                                return false;
                            }
                        }
                    });

                    $(this).toggle(show);
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {

            $('.remarks-filter').on('keyup change', function() {

                let filters = [];

                $('.remarks-filter').each(function() {
                    filters.push($(this).val().toLowerCase().trim());
                });

                $('#remarksTable tbody tr').each(function() {

                    let show = true;

                    $(this).find('td').each(function(index) {

                        if (index >= 7) {
                            return false;
                        }

                        let filter = filters[index];

                        if (filter !== '') {

                            let text = $(this).text().toLowerCase().trim();

                            if (text.indexOf(filter) === -1) {

                                show = false;

                                return false;
                            }
                        }
                    });

                    $(this).toggle(show);
                });
            });
        });
    </script>
@endpush
