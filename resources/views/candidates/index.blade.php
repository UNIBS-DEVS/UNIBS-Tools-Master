@extends('layouts.app')

@section('title', 'Candidates | Unibs Tools')

@section('content')

    {{-- Flash Messages --}}
    @include('partials.message')

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-2 sales-header">

        <div class="lh-sm">

            <h4 class="page-title mb-0">
                Candidate - Job Mapping
            </h4>

            <span class="page-subtitle">
                Manage all candidates, interviews & hiring pipeline
            </span>

        </div>

        <div class="d-flex align-items-center gap-1">

            <a href="{{ route('candidates.create') }}" class="btn btn-primary top-btn">

                <i class="fa fa-plus"></i>

            </a>

            <a href="{{ route('candidates.export', request()->query()) }}" class="btn btn-success top-btn">

                <i class="fa fa-file-excel"></i>

            </a>

        </div>

    </div>

    {{-- FILTERS --}}
    <div class="card filter-card border-0 mb-3">

        <div class="card-body py-2">

            <form method="POST" action="{{ route('candidates.filter') }}" id="filterForm">

                @csrf

                <div class="row g-2 align-items-end">

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

                    {{-- Mobile --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">
                            Mobile
                        </label>

                        <input type="text" name="mobile" class="form-control compact-input filter-input"
                            value="{{ $filters['mobile'] ?? '' }}" placeholder="Search mobile">

                    </div>

                    {{-- Email --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">
                            Email
                        </label>

                        <input type="text" name="email" class="form-control compact-input filter-input"
                            value="{{ $filters['email'] ?? '' }}" placeholder="Search email">

                    </div>

                    {{-- STATUS --}}
                    @php
                        $statuses = [
                            'Mapped',
                            'Under Discussion',
                            'Shared with Customer',
                            'Under Interview',
                            'Offered',
                            'Joined',
                            'Back Out',
                            'Closed',
                            'Rejected',
                        ];

                        $defaultStatuses = [
                            'Mapped',
                            'Under Discussion',
                            'Shared with Customer',
                            'Under Interview',
                            'Offered',
                        ];

                        $selectedStatuses = array_key_exists('status', $filters)
                            ? (array) $filters['status']
                            : $defaultStatuses;
                    @endphp

                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">
                            Status
                        </label>

                        <div class="dropdown">

                            <button class="btn compact-btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">

                                Select Status

                            </button>

                            <div class="dropdown-menu w-100 p-2 compact-dropdown">

                                <input type="text" class="form-control compact-input mb-2 search-box"
                                    data-target=".status-item" placeholder="Search status...">

                                <div class="form-check border-bottom pb-2 mb-2">

                                    <input type="checkbox" class="form-check-input select-all-checkbox"
                                        data-target=".status-checkbox" id="selectAllStatus">

                                    <label class="form-check-label fw-bold" for="selectAllStatus">

                                        Select All

                                    </label>

                                </div>

                                <div style="max-height:250px;overflow-y:auto;">

                                    @foreach ($statuses as $status)
                                        <div class="form-check status-item">

                                            <input class="form-check-input status-checkbox" type="checkbox" name="status[]"
                                                value="{{ $status }}" id="status_{{ md5($status) }}"
                                                {{ in_array($status, $selectedStatuses) ? 'checked' : '' }}>

                                            <label class="form-check-label" for="status_{{ md5($status) }}">

                                                {{ $status }}

                                            </label>

                                        </div>
                                    @endforeach

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- NOTICE PERIOD --}}
                    @php
                        $noticePeriods = [
                            'Immediate',
                            'Serving Notice',
                            'Under 15 Days',
                            'Under 30 Days',
                            'Under 60 Days',
                            '60 Days and Above',
                        ];

                        $defaultNotice = [
                            'Immediate',
                            'Serving Notice',
                            'Under 15 Days',
                            'Under 30 Days',
                            'Under 60 Days',
                        ];

                        $selectedNoticePeriods = array_key_exists('notice_period', $filters)
                            ? (array) $filters['notice_period']
                            : $defaultNotice;
                    @endphp

                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">
                            Notice Period
                        </label>

                        <div class="dropdown">

                            <button class="btn compact-btn dropdown-toggle w-100" type="button"
                                data-bs-toggle="dropdown">

                                Notice Period

                            </button>

                            <div class="dropdown-menu w-100 p-2 compact-dropdown">

                                <input type="text" class="form-control compact-input mb-2 search-box"
                                    data-target=".notice-item" placeholder="Search notice period...">

                                <div class="form-check border-bottom pb-2 mb-2">

                                    <input type="checkbox" class="form-check-input select-all-checkbox"
                                        data-target=".notice-checkbox" id="selectAllNotice">

                                    <label class="form-check-label fw-bold" for="selectAllNotice">

                                        Select All

                                    </label>

                                </div>

                                <div style="max-height:250px;overflow-y:auto;">

                                    @foreach ($noticePeriods as $period)
                                        <div class="form-check notice-item">

                                            <input class="form-check-input notice-checkbox" type="checkbox"
                                                name="notice_period[]" value="{{ $period }}"
                                                id="notice_{{ md5($period) }}"
                                                {{ in_array($period, $selectedNoticePeriods) ? 'checked' : '' }}>

                                            <label class="form-check-label" for="notice_{{ md5($period) }}">

                                                {{ $period }}

                                            </label>

                                        </div>
                                    @endforeach

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- CREATED BY --}}
                    @php
                        $selectedCreatedBy = array_key_exists('created_by', $filters)
                            ? (array) $filters['created_by']
                            : $createdByOptions->pluck('id')->toArray();
                    @endphp

                    <div class="col-lg-2 col-md-6">

                        <label class="filter-label">
                            Created By
                        </label>

                        <div class="dropdown">

                            <button class="btn compact-btn dropdown-toggle w-100" type="button"
                                data-bs-toggle="dropdown">

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

                        <a href="{{ route('candidates.reset') }}" class="btn btn-secondary compact-reset btn-sm">

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

        @forelse ($candidates as $candidate)

            <div class="col-12">

                <div class="card sales-card border-0">

                    <div class="card-body">

                        <div class="row g-2 align-items-center">

                            {{-- LEFT --}}
                            <div class="col-lg-4">

                                <div class="d-flex gap-3 align-items-start">


                                    {{-- CONTENT --}}
                                    <div class="flex-grow-1 min-width-0">

                                        {{-- AVATAR --}}
                                        <div>
                                            {{-- NAME --}}
                                            <h6 class="sales-name mb-1">

                                                {{ $candidate->candidate_name ?? '-' }}

                                            </h6>
                                            <div class="sales-email mb-1 text-break"> <i class="fa fa-building"></i>
                                                {{ $candidate->current_company ?? '-' }}</div>

                                            {{-- EMAIL --}}
                                            <div class="sales-email mb-2 text-break">

                                                {{ $candidate->email ?? '-' }} | {{ $candidate->mobile ?? '-' }}
                                                | <i class="fa fa-user"></i> {{ $candidate->gender ?? '-' }}

                                            </div>

                                        </div>

                                        {{-- BADGES --}}
                                        <div class="d-flex flex-wrap gap-1 text-center">

                                            <span class="mini-badge">

                                                <i class="fa fa-briefcase text-primary"></i>

                                                Exp:
                                                {{ $candidate->experience_years ?? 0 }} Yrs
                                                @if (($candidate->experience_months ?? 0) > 0)
                                                    | {{ $candidate->experience_months }} Mon
                                                @endif

                                                <span class="dot"></span> Rel:
                                                {{ $candidate->relevant_experience_years ?? 0 }} Yrs
                                                @if (($candidate->relevant_experience_months ?? 0) > 0)
                                                    | {{ $candidate->relevant_experience_months }} Mon
                                                @endif

                                            </span>

                                            <span class="mini-badge">

                                                <i class="fa fa-indian-rupee-sign text-success"></i>

                                                {{ $candidate->current_fixed_ctc ?? '0' }}
                                                -
                                                {{ $candidate->expected_ctc ?? '0' }} LPA

                                            </span>

                                            <span class="mini-badge">

                                                <i class="fa fa-location-dot text-danger"></i>

                                                @php
                                                    $current_location = Str::ucfirst(
                                                        $candidate->current_location ?? 'None',
                                                    );

                                                    $preferred_location = Str::ucfirst(
                                                        $candidate->preferred_location ?? 'None',
                                                    );
                                                @endphp

                                                {{ $current_location }}
                                                -
                                                {{ $preferred_location }}

                                            </span>

                                            <span class="mini-badge">

                                                <i class="fa fa-building text-primary"></i>

                                                @php
                                                    $customer = Str::ucfirst($candidate->customer?->customer ?? 'None');

                                                    $position = Str::ucfirst($candidate->position?->position ?? 'None');
                                                @endphp

                                                {{ $customer }}
                                                -
                                                {{ $position }}

                                            </span>

                                        </div>

                                    </div>

                                </div>
                            </div>

                            {{-- CENTER --}}
                            <div class="col-lg-4">

                                <div class="d-flex flex-wrap gap-1">

                                    {{-- Interview Date --}}
                                    <span class="mini-badge">
                                        <i class="fa fa-calendar-check me-1"></i>
                                        {{ $candidate->interview_date ? \Carbon\Carbon::parse($candidate->interview_date)->format('d M, Y h:i A') : 'None' }}
                                    </span>

                                    {{-- Interview Level --}}
                                    <span class="mini-badge">
                                        <i class="fa fa-layer-group me-1"></i>
                                        {{ $candidate->interview_level ?? 'None' }}
                                    </span>

                                    {{-- Education --}}
                                    <span class="mini-badge">
                                        <i class="fa fa-layer-group me-1"></i>
                                        {{ $candidate->education ?? 'None' }}
                                    </span>

                                </div>

                                {{-- SKILLS --}}
                                <div class="mb-2">

                                    @php
                                        $skills = array_slice(explode(',', $candidate->skill ?? ''), 0, 6);
                                    @endphp

                                    @foreach ($skills as $skill)
                                        @if (trim($skill))
                                            <span class="mini-badge">

                                                {{ trim($skill) }}

                                            </span>
                                        @endif
                                    @endforeach

                                </div>

                                {{-- STATUS --}}
                                <div class="d-flex flex-wrap gap-1 p-0">

                                    <span
                                        class="mini-badge
                                    @if ($candidate->status == 'Mapped') bg-success text-white
                                    @elseif($candidate->status == 'Under Discussion')
                                        bg-warning text-dark
                                    @elseif($candidate->status == 'Shared with Customer')
                                        bg-info text-dark
                                    @elseif($candidate->status == 'Under Interview')
                                        bg-primary text-white
                                    @elseif($candidate->status == 'Offered')
                                        bg-secondary text-white
                                    @elseif($candidate->status == 'Joined')
                                        bg-dark text-white
                                    @elseif($candidate->status == 'Back Out')
                                        bg-danger text-white
                                    @elseif($candidate->status == 'Closed')
                                        bg-muted text-dark
                                    @else
                                        bg-light text-dark @endif">

                                        {{ $candidate->status }}

                                    </span>

                                    <span class="mini-badge bg-dark text-white">

                                        {{ $candidate->notice_period ?? '-' }}

                                    </span>

                                    <span class="mini-badge text-danger">

                                        <i class="fa fa-calendar"></i>LWD :
                                        {{ $candidate->last_working_day ? \Carbon\Carbon::parse($candidate->last_working_day)->format('d M, Y') : 'None' }}

                                    </span>

                                </div>

                            </div>

                            {{-- RIGHT --}}
                            <div class="col-lg-4">

                                <div class="d-flex justify-content-lg-center justify-content-center gap-1 flex-wrap">

                                    @php
                                        $message = urlencode('👋 Greetings from UNI Business Solutions Pvt. Ltd.');
                                    @endphp

                                    @if ($candidate->mobile)
                                        <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $candidate->mobile) }}?text={{ $message }}"
                                            target="_blank" class="btn btn-success action-btn">

                                            <i class="fab fa-whatsapp"></i>

                                        </a>
                                    @endif

                                    <a href="mailto:{{ $candidate->email }}"
                                        class="btn btn-outline-secondary action-btn">

                                        <i class="fa fa-envelope"></i>

                                    </a>

                                    @if ($candidate->resume_path)
                                        <a href="{{ $candidate->resume_path }}" target="_blank"
                                            class="btn btn-outline-success action-btn">

                                            <i class="fa fa-link"></i>

                                        </a>
                                    @endif

                                    <a href="{{ route('candidates.show', $candidate->id) }}"
                                        class="btn btn-outline-info action-btn">

                                        <i class="fa fa-eye"></i>

                                    </a>

                                    <a href="{{ route('candidates.edit', [
                                        'candidate' => $candidate->id,
                                        'page' => request('page'),
                                    ]) }}"
                                        class="btn btn-outline-warning action-btn">
                                        <i class="fa fa-pen"></i>
                                    </a>

                                    @if (auth()->user()->hasRole('admin'))
                                        <form action="{{ route('candidates.destroy', $candidate->id) }}" method="POST"
                                            class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-outline-danger action-btn"
                                                onclick="return confirm('Delete candidate?')">

                                                <i class="fa fa-trash"></i>

                                            </button>

                                        </form>
                                    @endif

                                    <div class="w-100"></div>

                                    <small class="text-muted d-block" style="font-size:11px">

                                        <strong> Source By :</strong>
                                        {{ $candidate->creator?->name ?? '-' }}
                                        |
                                        {{ optional($candidate->created_at)->format('d M Y h:i A') }}

                                    </small>

                                    <small class="text-muted d-block" style="font-size:11px">

                                        <strong> Updated By :</strong>

                                        {{ $candidate->updater?->name ?? '-' }}
                                        |
                                        {{ optional($candidate->updated_at)->format('d M Y h:i A') }}

                                    </small>
                                    <div class="w-100"></div>

                                    @if ($candidate->latestRemark)
                                        <small class="text-muted d-block" style="font-size:11px;">
                                            <strong>Latest Remark :</strong>


                                            <span class="text-dark">
                                                {{ Str::limit($candidate->latestRemark->remarks, 50) }}
                                            </span>
                                        </small>
                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">

                <div class="alert alert-warning text-center rounded-3 py-2">

                    No candidates found

                </div>

            </div>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    {{-- <div class="mt-3">

        {{ $candidates->links() }}

    </div> --}}

    <div class="d-flex justify-content-between align-items-center mt-3">

        <div class="text-muted small">
            Showing {{ $candidates->firstItem() ?? 0 }}
            to {{ $candidates->lastItem() ?? 0 }}
            of {{ $candidates->total() }} results
        </div>

        @if ($candidates->hasPages())
            {{ $candidates->links() }}
        @endif

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
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {

            // CUSTOMER SEARCH
            // $('#customerSearch').on('keyup', function() {

            //     let value = $(this).val().toLowerCase();

            //     $('.customer-item').filter(function() {

            //         $(this).toggle(
            //             $(this).text().toLowerCase().indexOf(value) > -1
            //         );

            //     });

            // });


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

            // SELECT ALL
            // $('.select-all-checkbox').on('change', function() {

            //     let target = $(this).data('target');
            //     let checked = $(this).is(':checked');

            //     $(target + ':visible').prop(
            //         'checked',
            //         checked
            //     );

            // });

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

            // // AUTO SUBMIT ON DROPDOWN CLOSE
            // $('.dropdown').on('hide.bs.dropdown', function() {

            //     $('#filterForm').submit();

            // });

            // // TEXT FILTERS
            // $('.filter-input[type="text"]').on('blur', function() {

            //     $('#filterForm').submit();

            // });

            // // DATE FILTERS
            // $('.filter-input[type="date"]').on('change', function() {

            //     $('#filterForm').submit();

            // });

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
@endpush
