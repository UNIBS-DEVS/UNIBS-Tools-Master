@extends('layouts.app')

@section('title', 'Tender | Unibs Tools')

@section('content')

    @include('partials.message')

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-2 tender-header">

        <div class="lh-sm">

            <h4 class="page-title mb-0">
                Tenders
            </h4> - <span class="page-subtitle"> Manage all Tenders</span>

        </div>

        <div class="d-flex align-items-center gap-1">

            <a href="{{ route('tenders.create') }}" class="btn btn-primary top-btn">

                <i class="fa fa-plus"></i>

            </a>

            @if (auth()->user()->hasRole('admin'))
                <a href="{{ route('tenders.export') }}" class="btn btn-success top-btn">

                    <i class="fa fa-file-excel"></i>

                </a>
            @endif

        </div>

    </div>

    {{-- FILTERS --}}
    <div class="card filter-card border-0 mb-3">
        <div class="card-body py-2">

            <form method="POST" action="{{ route('tenders.filter') }}" id="filterForm">
                @csrf

                <div class="row g-2 align-items-end">

                    {{-- TENDER NUMBER --}}
                    <div class="col-lg-3 col-md-4 col-6">
                        <label class="filter-label">Tender Number</label>

                        <input type="text" name="tender_num" class="form-control compact-input filter-input"
                            placeholder="Search Tender Number" value="{{ $filters['tender_num'] ?? '' }}">
                    </div>

                    {{-- PRIMARY USER --}}
                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="filter-label">Primary</label>

                        <select name="primary_user_id" class="form-select compact-input filter-input">

                            <option value="">All</option>

                            @foreach ($tenderUsers as $user)
                                <option value="{{ $user->id }}"
                                    {{ ($filters['primary_user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    {{-- SECONDARY USER --}}
                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="filter-label">Secondary</label>

                        <select name="secondary_user_id" class="form-select compact-input filter-input">

                            <option value="">All</option>

                            @foreach ($tenderUsers as $user)
                                <option value="{{ $user->id }}"
                                    {{ ($filters['secondary_user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    {{-- STATUS --}}
                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="filter-label">Status</label>

                        @php
                            $statuses = ['Submitted', 'Under Evaluation', 'Won', 'Lost', 'Pending'];
                            $selectedStatus = $filters['status'] ?? 'Pending';
                        @endphp

                        <select name="status" class="form-select compact-input filter-input">
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" {{ $selectedStatus == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- STATE --}}
                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="filter-label">State</label>

                        <select name="state" class="form-select compact-input filter-input">

                            <option value="">All States</option>

                            @foreach ($states as $state)
                                <option value="{{ $state }}"
                                    {{ ($filters['state'] ?? '') == $state ? 'selected' : '' }}>
                                    {{ $state }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    {{-- RESET --}}
                    <div class="col-lg-1 col-md-2 col-2">
                        <a href="{{ route('tenders.reset') }}" class="btn btn-secondary compact-reset">

                            <i class="fa fa-rotate"></i>

                        </a>
                    </div>

                </div>

            </form>

        </div>
    </div>

    {{-- TENDER LIST --}}
    <div class="row g-2">

        @forelse ($tenders as $tender)
            <div class="col-12">

                <div class="card tender-card border-0">

                    <div class="card-body">

                        <div class="row g-2 align-items-center">

                            {{-- LEFT --}}
                            <div class="col-lg-3">

                                <div class="d-flex align-items-center gap-2">

                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($tender->tender_num) }}&background=0d6efd&color=fff&size=120"
                                        class="tender-avatar">

                                    <div class="flex-grow-1 overflow-hidden">

                                        <h6 class="tender-name mb-1 text-truncate">
                                            {{ $tender->tender_num }}
                                        </h6>

                                        <div class="tender-email text-truncate">
                                            {{ $tender->department ?? 'No Department' }}
                                        </div>

                                    </div>

                                </div>

                            </div>

                            {{-- CENTER --}}
                            <div class="col-lg-5">

                                <div class="d-flex flex-wrap gap-1 mb-2">

                                    <span class="mini-badge">
                                        <i class="fa fa-user text-primary"></i>
                                        {{ $tender->primaryUser?->name ?? 'None' }}
                                    </span>

                                    <span class="mini-badge">
                                        <i class="fa fa-users text-success"></i>
                                        {{ $tender->secondaryUser?->name ?? 'None' }}
                                    </span>

                                    <span class="mini-badge">
                                        <i class="fa fa-map-marker-alt text-danger"></i>
                                        {{ $tender->state ?? 'None' }}
                                    </span>

                                    <span class="mini-badge text-info">
                                        <i class="fa fa-calendar"></i>
                                        {{ optional($tender->submission_date)->format('d M Y') }}
                                    </span>

                                </div>

                                <div class="requirement-text mb-2">

                                    <strong>Estimated Value:</strong>
                                    ₹ {{ $tender->estimated_value ?? '0' }} Lakhs

                                    |
                                    <span class="mini-badge text-danger">
                                        <i class="fa fa-calendar"></i>
                                        {{ optional($tender->due_date)->format('d M Y') }}
                                    </span>

                                    <br>

                                    <strong>Bid Price:</strong>
                                    ₹ {{ $tender->bid_price ?? '0' }}

                                </div>

                                <div class="d-flex flex-wrap gap-1">

                                    <span class="tag-light">
                                        {{ $tender->type }}
                                    </span>

                                    <span class="tag-info">
                                        {{ $tender->platform }}
                                    </span>

                                    <span
                                        class="status-badge
                                        @if ($tender->status == 'Won') bg-success text-white
                                        @elseif($tender->status == 'Lost') bg-danger text-white
                                        @elseif($tender->status == 'Under Evaluation') bg-warning text-dark
                                        @elseif($tender->status == 'Submitted') bg-primary text-white
                                        @else bg-secondary text-white @endif">

                                        {{ $tender->status }}

                                    </span>

                                </div>

                            </div>

                            {{-- RIGHT --}}
                            <div class="col-lg-4 d-flex align-items-center justify-content-center">

                                <div class="text-center w-100">

                                    {{-- ACTION BUTTONS --}}
                                    <div class="d-flex justify-content-center gap-1 flex-wrap mb-2">

                                        <a href="{{ route('tenders.show', $tender->id) }}"
                                            class="btn btn-outline-info action-btn">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        <a href="{{ route('tenders.edit', $tender->id) }}"
                                            class="btn btn-outline-warning action-btn">
                                            <i class="fa fa-pen"></i>
                                        </a>

                                        @if (auth()->user()->hasRole('admin'))
                                            <form action="{{ route('tenders.destroy', $tender->id) }}" method="POST"
                                                class="d-inline">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-outline-danger action-btn"
                                                    onclick="return confirm('Delete Tender?')">

                                                    <i class="fa fa-trash"></i>

                                                </button>

                                            </form>
                                        @endif

                                    </div>

                                    {{-- CREATED --}}
                                    <small class="text-muted d-block mb-1">

                                        Created:
                                        <strong>
                                            {{ $tender->creator?->name ?? 'System' }}
                                        </strong>

                                        {{ optional($tender->created_at)->format('d M Y h:i A') }}

                                    </small>

                                    {{-- UPDATED --}}
                                    <small class="text-muted d-block">

                                        Updated:
                                        <strong>
                                            {{ $tender->updater?->name ?? 'System' }}
                                        </strong>

                                        {{ optional($tender->updated_at)->format('d M Y h:i A') }}

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

                    No tender found

                </div>

            </div>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    <div class="mt-3">

        {{ $tenders->links() }}

    </div>

@endsection

@push('styles')
    <style>
        body {
            /* font-size: 11px; */
            background: #f4f6fb;
            color: #2b2f38;
        }

        .tender-header {
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

        /* tender CARD */
        .tender-card {
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 2px 16px rgba(0, 0, 0, .04);
            transition: .2s ease;
            overflow: hidden;
            border: 1px solid #eef1f4;
        }

        .tender-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 26px rgba(0, 0, 0, .08);
        }

        .tender-card .card-body {
            padding: 14px;
        }

        .tender-card .card-footer {
            padding: 8px 14px;
            background: #fcfcfc;
            border-top: 1px solid #f1f3f5;
            font-size: 10px;
        }

        /* AVATAR */
        .tender-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
        }

        /* NAME */
        .tender-name {
            font-size: 15px;
            font-weight: 700;
            color: #0d6efd;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .tender-email {
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
        .tender-meta {
            width: 100%;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px dashed #e9ecef;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .tender-meta small {
            font-size: 10px;
            color: #6c757d;
            line-height: 1.4;
        }

        .tender-meta strong {
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

            .tender-meta {
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

            .tender-card .card-body {
                padding: 11px;
            }

            .tender-avatar {
                width: 44px;
                height: 44px;
            }

            .tender-name {
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

            .tender-meta {
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
