@extends('layouts.app')

@section('title', 'Sales | Unibs Tools')

@section('content')

    @include('partials.message')

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-2 sales-header">

        <div class="lh-sm">

            <h4 class="page-title mb-0">
                Sales
            </h4> - <span class="page-subtitle"> Manage all leads & follow ups</span>

        </div>

        <div class="d-flex align-items-center gap-1">

            <a href="{{ route('sales.create') }}" class="btn btn-primary top-btn">

                <i class="fa fa-plus"></i>

            </a>

            @if (auth()->user()->hasRole('admin'))
                <a href="{{ route('sales.export') }}" class="btn btn-success top-btn">

                    <i class="fa fa-file-excel"></i>

                </a>
            @endif

        </div>

    </div>

    {{-- FILTERS --}}
    <div class="card filter-card border-0 mb-3">

        <div class="card-body py-2">

            <form method="POST" action="{{ route('sales.filter') }}" id="filterForm">

                @csrf

                <div class="row g-2 align-items-end">

                    {{-- Company Name --}}
                    <div class="col-lg-2 col-md-4 col-6">

                        <label class="filter-label">Company Name</label>

                        <input type="text" name="company" class="form-control compact-input filter-input"
                            placeholder="Search Company" value="{{ $filters['company'] ?? '' }}">

                    </div>

                    {{-- MOBILE SEARCH --}}
                    <div class="col-lg-2 col-md-4 col-6">

                        <label class="filter-label">Mobile</label>

                        <input type="text" name="mobile" class="form-control compact-input filter-input"
                            placeholder="Search Mobile" value="{{ $filters['mobile'] ?? '' }}">

                    </div>

                    {{-- TYPE --}}
                    <div class="col-lg-2 col-md-4 col-6">

                        <label class="filter-label">Type</label>

                        @php

                            $types = [
                                'Sourcing',
                                'Training',
                                'Job Seeker',
                                'Microsoft',
                                'Tally',
                                'Google',
                                'Zoho',
                                'Software Services',
                                'Digital Marketing',
                                'Razorpay',
                                'BGC',
                                'Others',
                            ];

                            $defaultTypes = [
                                'Sourcing',
                                'Training',
                                'Job Seeker',
                                'Microsoft',
                                'Tally',
                                'Google',
                                'Zoho',
                                'Software Services',
                                'Digital Marketing',
                                'Razorpay',
                                'BGC',
                                'Others',
                            ];

                            // $defaultTypes = collect($types)->reject(fn($item) => $item == 'Others')->toArray();

                            $selectedTypes = $filters['type'] ?? $defaultTypes;

                        @endphp

                        <div class="dropdown">

                            <button class="btn compact-btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">

                                Select

                            </button>

                            <ul class="dropdown-menu w-100 p-2 compact-dropdown">

                                @foreach ($types as $type)
                                    <li>

                                        <div class="form-check">

                                            <input class="form-check-input filter-input" type="checkbox" name="type[]"
                                                value="{{ $type }}" id="type_{{ md5($type) }}"
                                                {{ in_array($type, $selectedTypes) ? 'checked' : '' }}>

                                            <label class="form-check-label" for="type_{{ md5($type) }}">

                                                {{ $type }}

                                            </label>

                                        </div>

                                    </li>
                                @endforeach

                            </ul>

                        </div>

                    </div>

                    {{-- SOURCE --}}
                    <div class="col-lg-2 col-md-4 col-6">

                        <label class="filter-label">Source</label>

                        @php

                            $sources = [
                                'IndiaMart',
                                'Justdial',
                                'Linkedin',
                                'Facebook',
                                'Instagram',
                                'Twitter',
                                'References',
                                'Others',
                            ];

                            $defaultSources = [
                                'IndiaMart',
                                'Justdial',
                                'Linkedin',
                                'Facebook',
                                'Instagram',
                                'Twitter',
                                'References',
                                'Others',
                            ];

                            // $defaultSources = collect($sources)->reject(fn($item) => $item == 'Others')->toArray();

                            $selectedSources = $filters['source'] ?? $defaultSources;

                        @endphp

                        <div class="dropdown">

                            <button class="btn compact-btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">

                                Select

                            </button>

                            <ul class="dropdown-menu w-100 p-2 compact-dropdown">

                                @foreach ($sources as $source)
                                    <li>

                                        <div class="form-check">

                                            <input class="form-check-input filter-input" type="checkbox" name="source[]"
                                                value="{{ $source }}" id="source_{{ md5($source) }}"
                                                {{ in_array($source, $selectedSources) ? 'checked' : '' }}>

                                            <label class="form-check-label" for="source_{{ md5($source) }}">

                                                {{ $source }}

                                            </label>

                                        </div>

                                    </li>
                                @endforeach

                            </ul>

                        </div>

                    </div>

                    {{-- STATUS --}}
                    <div class="col-lg-2 col-md-4 col-6">

                        <label class="filter-label">Status</label>

                        @php

                            $statuses = [
                                'New',
                                'Won',
                                'Lost',
                                'Under Discussion',
                                'On-Hold',
                                'Fake',
                                'Spam',
                                'Irrelevant',
                                'Repeatedly Unreachable',
                            ];

                            $defaultStatuses = ['New', 'Under Discussion'];

                            $selectedStatuses = $filters['status'] ?? $defaultStatuses;

                        @endphp

                        <div class="dropdown">

                            <button class="btn compact-btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">

                                Select

                            </button>

                            <ul class="dropdown-menu w-100 p-2 compact-dropdown">

                                @foreach ($statuses as $status)
                                    <li>

                                        <div class="form-check">

                                            <input class="form-check-input filter-input" type="checkbox" name="status[]"
                                                value="{{ $status }}" id="status_{{ md5($status) }}"
                                                {{ in_array($status, $selectedStatuses) ? 'checked' : '' }}>

                                            <label class="form-check-label" for="status_{{ md5($status) }}">

                                                {{ $status }}

                                            </label>

                                        </div>

                                    </li>
                                @endforeach

                            </ul>

                        </div>

                    </div>

                    {{-- FOLLOW UP --}}
                    <div class="col-lg-2 col-md-4 col-6">

                        <label class="filter-label">Follow Up</label>

                        @php

                            $followUpOptions = [
                                'null' => 'None',
                                'today' => 'Today',
                                'others' => 'Others',
                            ];

                            $defaultFollowUps = ['today', 'others', 'null'];

                            $selectedFollowUps = $filters['follow_up_date'] ?? $defaultFollowUps;

                        @endphp

                        <div class="dropdown">

                            <button class="btn compact-btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">

                                Select

                            </button>

                            <ul class="dropdown-menu w-100 p-2 compact-dropdown">

                                @foreach ($followUpOptions as $key => $value)
                                    <li>

                                        <div class="form-check">

                                            <input class="form-check-input filter-input" type="checkbox"
                                                name="follow_up_date[]" value="{{ $key }}"
                                                id="follow_up_{{ $key }}"
                                                {{ in_array($key, $selectedFollowUps) ? 'checked' : '' }}>

                                            <label class="form-check-label" for="follow_up_{{ $key }}">

                                                {{ $value }}

                                            </label>

                                        </div>

                                    </li>
                                @endforeach

                            </ul>

                        </div>

                    </div>

                    {{-- CREATED BY --}}
                    <div class="col-lg-2 col-md-3 col-6">

                        <label class="filter-label">
                            Created By
                        </label>

                        @php
                            $selectedCreatedBy = $filters['created_by'] ?? [auth()->id()];
                        @endphp

                        <div class="dropdown">

                            <button class="btn compact-btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">

                                Select

                            </button>

                            <ul class="dropdown-menu w-100 p-2 compact-dropdown">

                                @foreach ($createdBySales as $user)
                                    <li>

                                        <div class="form-check">

                                            <input class="form-check-input filter-input" type="checkbox"
                                                name="created_by[]" value="{{ $user->id }}"
                                                id="created_by_{{ $user->id }}"
                                                {{ in_array((string) $user->id, array_map('strval', $selectedCreatedBy)) ? 'checked' : '' }}>

                                            <label class="form-check-label" for="created_by_{{ $user->id }}">

                                                {{ $user->name }}

                                            </label>

                                        </div>

                                    </li>
                                @endforeach

                            </ul>

                        </div>

                    </div>

                    {{-- FROM --}}
                    <div class="col-lg-2 col-md-4 col-6">

                        <label class="filter-label">From</label>

                        {{-- <input type="date" name="from_date" class="form-control compact-input filter-input"
                            value="{{ $filters['from_date'] ?? now()->toDateString() }}"> --}}

                        <input type="date" name="from_date" class="form-control compact-input filter-input"
                            value="{{ $filters['from_date'] ?? '' }}">

                    </div>

                    {{-- TO --}}
                    <div class="col-lg-2 col-md-4 col-4">

                        <label class="filter-label">To</label>

                        {{-- <input type="date" name="to_date" class="form-control compact-input filter-input"
                            value="{{ $filters['to_date'] ?? now()->toDateString() }}"> --}}

                        <input type="date" name="to_date" class="form-control compact-input filter-input"
                            value="{{ $filters['to_date'] ?? '' }}">

                    </div>

                    {{-- RESET --}}
                    <div class="col-lg-1 col-md-2 col-2">

                        <a href="{{ route('sales.reset') }}" class="btn btn-secondary compact-reset">

                            <i class="fa fa-rotate"></i>

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>

    {{-- SALES LIST --}}
    <div class="row g-2">

        @forelse ($sales as $sale)
            <div class="col-12">

                <div class="card sales-card border-0">

                    <div class="card-body">

                        <div class="row g-2 align-items-center">

                            {{-- LEFT --}}
                            <div class="col-lg-3">

                                <div class="d-flex align-items-center gap-2">

                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($sale->client_contact) }}&background=0d6efd&color=fff&size=120"
                                        class="sales-avatar">

                                    <div class="flex-grow-1 overflow-hidden">

                                        <h6 class="sales-name mb-1 text-truncate">

                                            {{ $sale->client_contact ?? 'None' }}

                                        </h6>

                                        <div class="sales-email text-truncate">

                                            {{ \Illuminate\Support\Str::limit($sale->email ?? 'None', 30) }}

                                        </div>

                                    </div>

                                </div>

                            </div>

                            {{-- CENTER --}}
                            <div class="col-lg-5">

                                <div class="d-flex flex-wrap gap-1 mb-2">

                                    <span class="mini-badge">
                                        <i class="fa fa-building text-primary"></i>
                                        {{ $sale->company ?? 'None' }}
                                    </span>

                                    <span class="mini-badge">
                                        <i class="fa fa-phone text-success"></i>
                                        {{ $sale->mobile ?? 'None' }}
                                    </span>

                                    <span class="mini-badge">
                                        <i class="fa fa-location-dot text-danger"></i>
                                        {{ $sale->location ?? 'None' }}
                                    </span>

                                    <span class="mini-badge text-info">
                                        <i class="fa fa-calendar-check"></i>
                                        {{ $sale->follow_up_date ?? 'None' }}
                                    </span>

                                </div>

                                <div class="requirement-text mb-2">

                                    {{ \Illuminate\Support\Str::limit($sale->requirement, 120) }}

                                </div>

                                <div class="d-flex flex-wrap gap-1">

                                    <span class="tag-light">

                                        {{ $sale->type ?? 'None' }}

                                    </span>

                                    <span class="tag-info">

                                        {{ $sale->source ?? 'None' }}

                                    </span>

                                    <span class="status-badge bg-danger text-white"> {{ $sale->status }}</span>

                                </div>

                            </div>

                            {{-- RIGHT --}}
                            <div class="col-lg-4">

                                <div class="d-flex justify-content-lg-center justify-content-center gap-1 flex-wrap">

                                    {{-- ACTION BUTTONS --}}
                                    <div class="d-flex gap-1 flex-wrap">
                                        @php

                                            $message = urlencode(
                                                "👋 Greetings from UNI Business Solutions Pvt. Ltd.
                                        
                                        ✅ Licensing:
                                        • Microsoft
                                        • Google Workspace
                                        • Tally
                                        • Zoho
                                        
                                        💼 Services:
                                        • IT Development
                                        • Staffing
                                        • SAP Consulting
                                        • Corporate Training",
                                            );

                                        @endphp

                                        <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $sale->mobile) }}?text={{ $message }}"
                                            target="_blank" class="btn btn-success action-btn">

                                            <i class="fab fa-whatsapp"></i>

                                        </a>

                                        <a href="mailto:{{ $sale->email }}"
                                            class="btn btn-outline-secondary action-btn">

                                            <i class="fa fa-envelope"></i>

                                        </a>

                                        <a href="{{ route('sales.show', $sale->id) }}"
                                            class="btn btn-outline-info action-btn">

                                            <i class="fa fa-eye"></i>

                                        </a>

                                        {{-- <a href="{{ route('sales.edit', $sale->id) }}"
                                            class="btn btn-outline-warning action-btn">

                                            <i class="fa fa-pen"></i>

                                        </a> --}}

                                        <a href="{{ route('sales.edit', [
                                            'sale' => $sale->id,
                                            'page' => request('page'),
                                        ]) }}"
                                            class="btn btn-outline-warning action-btn">
                                            <i class="fa fa-pen"></i>
                                        </a>


                                        @if (auth()->user()->hasRole('admin'))
                                            <form action="{{ route('sales.destroy', $sale->id) }}" method="POST"
                                                class="d-inline">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-outline-danger action-btn"
                                                    onclick="return confirm('Delete sale?')">

                                                    <i class="fa fa-trash"></i>

                                                </button>

                                            </form>
                                        @endif

                                    </div>

                                    {{-- SPACE --}}
                                    <div class="w-100 my-1"></div>

                                    {{-- CREATED --}}
                                    <small class="text-muted d-block" style="font-size: 11px">

                                        Created:
                                        <strong>{{ $sale->creator?->name ?? 'None' }}</strong>

                                        •

                                        {{ optional($sale->created_at)->format('d M Y h:i A') }}

                                    </small>

                                    {{-- UPDATED --}}
                                    <small class="text-muted d-block" style="font-size: 11px">

                                        Updated:
                                        <strong>{{ $sale->updater?->name ?? 'None' }}</strong>

                                        •

                                        {{ optional($sale->updated_at)->format('d M Y h:i A') }}

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

                    No sales found

                </div>

            </div>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    <div class="mt-3">

        {{ $sales->links() }}

    </div>

@endsection

@push('styles')
    <style>
        body {
            /* font-size: 11px; */
            background: #f4f6fb;
            color: #2b2f38;
        }

        .sales-header {
            margin-bottom: 10px !important;
        }

        .page-title {
            display: inline;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -.3px;
        }

        .page-subtitle {
            font-size: 10px;
            color: #7b8190;
            margin-top: 1px;
            line-height: 1.2;
        }

        .top-btn {
            padding: 0;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
        }

        @media(max-width:768px) {

            .page-title {
                font-size: 16px;
            }

            .page-subtitle {
                font-size: 9px;
            }

            .top-btn {
                border-radius: 5px;
            }

        }

        /* PAGE */
        .page-title {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -.3px;
            line-height: 1;
        }

        .page-subtitle {
            font-size: 11px;
            color: #7b8190;
            margin-top: 2px;
        }

        /* TOP BUTTONS */
        .top-btn {
            width: 34px;
            height: 34px;
            padding: 0;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
        }

        /* FILTER CARD */
        .filter-card {
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 2px 18px rgba(0, 0, 0, .04);
            overflow: visible !important;
        }

        .filter-card .card-body {
            padding: 14px;
        }

        .filter-label {
            font-size: 10px;
            font-weight: 700;
            color: #6c757d;
            margin-bottom: 4px;
            display: block;
        }

        /* INPUTS */
        .compact-btn,
        .compact-input {
            height: 36px;
            border-radius: 12px;
            font-size: 11px;
            border: 1px solid #dfe3e8;
            background: #fff;
            box-shadow: none !important;
        }

        .compact-btn {
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .compact-btn:hover,
        .compact-input:hover {
            border-color: #cfd6dd;
        }

        .compact-btn:focus,
        .compact-input:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .12rem rgba(13, 110, 253, .15) !important;
        }

        .compact-reset {
            /* height: 25px;
                                                                                                                                                                                                                                                                                                                                    width: 25px; */
            border-radius: 12px;
        }

        /* DROPDOWN */
        .dropdown {
            position: relative;
        }

        .compact-dropdown {
            border: 0;
            border-radius: 14px;
            padding: 8px !important;
            min-width: 100%;
            margin-top: 6px !important;
            max-height: 260px;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 0 12px 28px rgba(0, 0, 0, .10);
        }

        .compact-dropdown::-webkit-scrollbar {
            width: 5px;
        }

        .compact-dropdown::-webkit-scrollbar-thumb {
            background: #d0d7de;
            border-radius: 20px;
        }

        .compact-dropdown li {
            list-style: none;
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

        /* SALES CARD */
        .sales-card {
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 2px 16px rgba(0, 0, 0, .04);
            transition: .2s ease;
            overflow: hidden;
            border: 1px solid #eef1f4;
        }

        .sales-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 26px rgba(0, 0, 0, .08);
        }

        .sales-card .card-body {
            padding: 14px;
        }

        .sales-card .card-footer {
            padding: 8px 14px;
            background: #fcfcfc;
            border-top: 1px solid #f1f3f5;
            font-size: 10px;
        }

        /* AVATAR */
        .sales-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
        }

        /* NAME */
        .sales-name {
            font-size: 15px;
            font-weight: 700;
            color: #0d6efd;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .sales-email {
            font-size: 10px;
            color: #7b8190;
            line-height: 1.35;
            word-break: break-word;
        }

        /* BADGES */
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
            color: #495057;
        }

        /* REQUIREMENT */
        .requirement-text {
            font-size: 11px;
            line-height: 1.5;
            color: #4f5662;
        }

        /* TAGS */
        .tag-light,
        .tag-info,
        .status-badge {
            padding: 4px 9px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .tag-light {
            background: #f1f3f5;
            border: 1px solid #e9ecef;
            color: #495057;
        }

        .tag-info {
            background: #cff4fc;
            color: #055160;
        }

        /* ACTION BUTTONS */
        .action-btn {
            width: 31px;
            height: 31px;
            border-radius: 10px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            transition: .18s ease;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        /* CREATED UPDATED */
        .sales-meta {
            width: 100%;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px dashed #e9ecef;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sales-meta small {
            font-size: 10px;
            color: #6c757d;
            line-height: 1.4;
        }

        .sales-meta strong {
            color: #343a40;
        }

        /* PAGINATION */
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
            color: #495057;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
        }

        .pagination .active .page-link {
            background: #0d6efd;
            color: #fff;
        }

        /* EMPTY */
        .alert-warning {
            border: 0;
            font-size: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .04);
        }

        /* MOBILE */
        @media(max-width: 991px) {

            .sales-meta {
                margin-top: 10px;
            }

            .compact-dropdown {
                position: absolute !important;
                inset: auto !important;
                transform: none !important;
            }

        }

        @media(max-width: 768px) {

            body {
                font-size: 10px;
            }

            .page-title {
                font-size: 18px;
            }

            .page-subtitle {
                font-size: 10px;
            }

            .sales-card .card-body {
                padding: 11px;
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
                border-radius: 8px;
            }

            .compact-btn,
            .compact-input,
            .compact-reset {
                height: 34px;
            }

            .sales-meta {
                gap: 2px;
            }

        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {

            // Text INPUTS
            $('.filter-input[type="text"]').on('blur', function() {

                $('#filterForm').submit();

            });


            // DATE INPUTS
            $('.filter-input[type="date"]').on('change', function() {

                $('#filterForm').submit();

            });

            // NORMAL SELECT
            $('select.filter-input').on('change', function() {

                $('#filterForm').submit();

            });

            // SUBMIT WHEN DROPDOWN CLOSES
            $('.dropdown').on('hide.bs.dropdown', function() {

                $('#filterForm').submit();

            });

        });
    </script>
@endpush
