@extends('layouts.app')

@section('title', 'Customer Jobs | Unibs Tools')

@section('content')

    @include('partials.message')

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-2 sales-header">

        <div class="lh-sm">

            <h4 class="page-title mb-0">
                Customer Jobs
            </h4>

            <span class="page-subtitle">
                Manage all customer job openings & hiring requirements
            </span>

        </div>

        <div class="d-flex align-items-center gap-1">

            <a href="{{ route('customer-jobs.create') }}" class="btn btn-primary top-btn">
                <i class="fa fa-plus"></i>
            </a>

            <a href="{{ route('customer-jobs.export', request()->query()) }}" class="btn btn-success top-btn">

                <i class="fa fa-file-excel"></i>

            </a>

            <a href="{{ route('customers.index') }}" class="btn btn-secondary top-btn">

                <i class="fa fa-arrow-left"></i>

            </a>

        </div>

    </div>

    {{-- FILTERS --}}
    <div class="card filter-card border-0 mb-3">

        <div class="card-body py-2">

            <form method="POST" action="{{ route('customer-jobs.filter') }}" id="filterForm">

                @csrf



                <div class="row g-2 align-items-end">



                    {{-- CUSTOMER --}}
                    <div class="col-lg-3 col-md-6">

                        @php

                            $selectedCustomers = request()->filled('customer_id')
                                ? (array) request()->customer_id
                                : (array) ($filters['customer_id'] ?? []);

                        @endphp

                        <label class="filter-label">
                            Customers
                        </label>

                        @php
                            $selectedCustomers = (array) ($filters['customer_id'] ?? []);
                        @endphp

                        <div class="dropdown">

                            <button class="btn compact-btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">

                                Select Customers

                            </button>

                            <div class="dropdown-menu w-100 p-2 compact-dropdown">

                                <input type="text" class="form-control compact-input mb-2" id="customerSearch"
                                    placeholder="Search customer...">

                                <div class="form-check border-bottom pb-2 mb-2">

                                    <input type="checkbox" class="form-check-input" id="selectAllCustomers">

                                    <label class="form-check-label fw-bold" for="selectAllCustomers">

                                        Select All

                                    </label>

                                </div>

                                <div style="max-height:250px; overflow-y:auto;">

                                    @foreach ($customers as $customer)
                                        <div class="form-check customer-item">

                                            <input class="form-check-input customer-checkbox" type="checkbox"
                                                name="customer_id[]" value="{{ $customer->id }}"
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

                    {{-- SKILL --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">
                            Skill
                        </label>

                        <input type="text" name="skill" class="form-control compact-input filter-input"
                            value="{{ $filters['skill'] ?? '' }}" placeholder="Search skill">

                    </div>

                    {{-- POSITION --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">
                            Job Position
                        </label>

                        <input type="text" name="job_position" class="form-control compact-input filter-input"
                            value="{{ $filters['job_position'] ?? '' }}" placeholder="Search position">

                    </div>

                    {{-- STATUS --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">
                            Status
                        </label>

                        @php
                            $selectedStatuses = (array) ($filters['status'] ?? ['Open']);

                            $statuses = ['Open', 'Closed', 'On-Hold'];
                        @endphp

                        <div class="dropdown">

                            <button class="btn compact-btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">

                                Select

                            </button>

                            <ul class="dropdown-menu w-100 p-2 compact-dropdown">

                                @foreach ($statuses as $status)
                                    <li>

                                        <div class="form-check">

                                            <input class="form-check-input" type="checkbox" name="status[]"
                                                value="{{ $status }}" id="status_{{ $loop->index }}"
                                                {{ in_array($status, $selectedStatuses) ? 'checked' : '' }}>

                                            <label class="form-check-label" for="status_{{ $loop->index }}">

                                                {{ $status }}

                                            </label>

                                        </div>

                                    </li>
                                @endforeach

                            </ul>

                        </div>

                    </div>

                    {{-- RESET --}}
                    <div class="col-lg-1 col-md-2 col-2">

                        <a href="{{ route('customer-jobs.reset') }}" class="btn btn-secondary compact-reset">

                            <i class="fa fa-rotate"></i>

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>

    {{-- JOB LIST --}}
    <div class="row g-2">

        @forelse ($jobs as $job)
            <div class="col-12">

                <div class="card sales-card border-0">

                    <div class="card-body">

                        <div class="row g-2 align-items-center">

                            {{-- LEFT --}}
                            <div class="col-lg-3">

                                <div class="d-flex align-items-center gap-2">

                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($job->customer?->customer ?? 'Job') }}&background=0d6efd&color=fff&size=120"
                                        class="sales-avatar">

                                    <div class="flex-grow-1 overflow-hidden">

                                        <h6 class="sales-name text-truncate">

                                            {{ $job->position ?? '-' }}

                                        </h6>

                                        <div class="sales-email text-truncate">

                                            {{ $job->customer?->customer ?? '-' }}

                                        </div>

                                    </div>

                                </div>

                            </div>

                            {{-- CENTER --}}
                            <div class="col-lg-5">

                                <div class="d-flex flex-wrap gap-1 mb-2">

                                    <span class="mini-badge">

                                        <i class="fa fa-code text-primary"></i>

                                        {{ $job->skill ?? '-' }}

                                    </span>

                                    <span class="mini-badge">

                                        <i class="fa fa-graduation-cap text-primary"></i>

                                        {{ $job->experience ?? '-' }}

                                    </span>

                                    <span class="mini-badge">

                                        <i class="fa fa-location-dot text-danger"></i>

                                        {{ $job->location ?? '-' }}

                                    </span>

                                    <span class="mini-badge">

                                        <i class="fa fa-indian-rupee-sign text-success"></i>

                                        {{ $job->budget ?? '-' }}

                                    </span>

                                </div>

                                <div class="d-flex flex-wrap gap-1">

                                    <span
                                        class="status-badge
                                        @if ($job->status == 'Open') bg-success
                                        @elseif($job->status == 'Closed')
                                            bg-danger
                                        @else
                                            bg-warning text-dark @endif">

                                        {{ $job->status }}

                                    </span>

                                    <span class="tag-light">

                                        Openings :
                                        {{ $job->count ?? 0 }}

                                    </span>

                                </div>

                            </div>

                            {{-- RIGHT --}}
                            <div class="col-lg-4">

                                <div class="d-flex justify-content-lg-center justify-content-center gap-1 flex-wrap">

                                    @if ($job->jd_path)
                                        <a href="{{ $job->jd_path }}" target="_blank"
                                            class="btn btn-outline-primary action-btn">

                                            <i class="fa fa-link"></i>

                                        </a>
                                    @endif

                                    <a href="{{ route('customer-jobs.show', $job->id) }}"
                                        class="btn btn-outline-info action-btn">

                                        <i class="fa fa-eye"></i>

                                    </a>

                                    <a href="{{ route('customer-jobs.edit', $job->id) }}"
                                        class="btn btn-outline-warning action-btn">

                                        <i class="fa fa-pen"></i>

                                    </a>

                                    @if (auth()->user()->hasRole('admin'))
                                        <form action="{{ route('customer-jobs.destroy', $job->id) }}" method="POST"
                                            class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-outline-danger action-btn"
                                                onclick="return confirm('Delete this position?')">

                                                <i class="fa fa-trash"></i>

                                            </button>

                                        </form>
                                    @endif

                                    <div class="w-100"></div>

                                    <small class="text-muted d-block" style="font-size:11px">

                                        Created :
                                        <strong>
                                            {{ optional($job->created_at)->format('d M Y h:i A') }}
                                        </strong>

                                    </small>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">

                <div class="alert alert-warning text-center rounded-3 py-2">

                    No jobs found

                </div>

            </div>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    <div class="mt-3">

        {{ $jobs->links() }}

    </div>

@endsection

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
            border-radius: 12px;
            font-size: 11px;
            border: 1px solid #dfe3e8;
            background: #fff;
        }

        .compact-btn {
            font-weight: 600;
        }

        .compact-reset {
            border-radius: 12px;
        }

        .dropdown {
            position: relative;
        }

        .compact-dropdown {
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
            position: absolute !important;
            inset: auto !important;
            transform: none !important;
            z-index: 1055;
        }

        .compact-dropdown::-webkit-scrollbar {
            width: 5px;
        }

        .compact-dropdown::-webkit-scrollbar-thumb {
            background: #d0d7de;
            border-radius: 20px;
        }

        .compact-dropdown .form-check {
            margin: 0;
            min-height: auto;
            padding: 7px 8px 7px 30px;
            border-radius: 10px;
            transition: .15s ease;
            position: relative;
        }

        .compact-dropdown .form-check:hover {
            background: #f6f8fb;
        }

        .compact-dropdown .form-check-input {
            position: absolute;
            left: 8px;
            top: 9px;
            margin: 0;
            width: 15px;
            height: 15px;
            cursor: pointer;
        }

        .compact-dropdown .form-check-label {
            font-size: 11px;
            font-weight: 500;
            color: #495057;
            cursor: pointer;
            display: block;
            line-height: 1.3;
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
            margin-bottom: 0px;
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
        }

        .tag-light,
        .status-badge {
            padding: 4px 9px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 700;
        }

        .tag-light {
            background: #f1f3f5;
            border: 1px solid #e9ecef;
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

        @media(max-width:768px) {

            .page-title {
                font-size: 17px;
            }

            .sales-avatar {
                width: 44px;
                height: 44px;
            }

            .sales-name {
                font-size: 13px;
            }

            .action-btn {
                width: 28px;
                height: 28px;
            }

        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {

            // CUSTOMER SEARCH
            $('#customerSearch').on('keyup', function() {

                let value = $(this).val().toLowerCase();

                $('.customer-item').each(function() {

                    $(this).toggle(
                        $(this).text().toLowerCase().includes(value)
                    );

                });
            });

            // SELECT ALL CUSTOMERS
            $('#selectAllCustomers').on('change', function() {

                $('.customer-checkbox:visible').prop(
                    'checked',
                    $(this).is(':checked')
                );

            });

            // UPDATE SELECT ALL STATUS
            function updateSelectAllCustomers() {

                let total = $('.customer-checkbox').length;
                let checked = $('.customer-checkbox:checked').length;

                $('#selectAllCustomers').prop(
                    'checked',
                    total > 0 && total === checked
                );
            }

            // INITIAL LOAD
            updateSelectAllCustomers();

            // INDIVIDUAL CUSTOMER CHECKBOX
            $('.customer-checkbox').on('change', function() {

                updateSelectAllCustomers();

            });

            // SUBMIT FILTER WHEN DROPDOWN CLOSES
            $('.dropdown').on('hide.bs.dropdown', function() {

                $('#filterForm').submit();

            });

            // AUTO SUBMIT TEXT INPUTS
            $('.filter-input').on('blur', function() {

                $('#filterForm').submit();

            });

        });
    </script>
@endpush
