@extends('layouts.app')

@section('title', 'My Timesheets | Unibs Tools')

@section('content')

    @include('partials.message')

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-2">

        <div>
            <h4 class="fw-bold mb-0">
                <i class="fa-solid fa-clock text-primary me-2"></i>
                My Timesheets
            </h4>

            <small class="text-muted">
                Total Timesheets: {{ $timesheets->total() }}
            </small>
        </div>

        <div class="d-flex gap-1">

            <a href="{{ route('timesheets.create') }}" class="btn btn-primary btn-sm rounded-3 shadow-sm">

                <i class="fa fa-plus"></i>

            </a>

        </div>

    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

        <div class="table-responsive">

            <table class="table align-middle mb-0">

                <thead class="bg-light">

                    <tr>

                        <th class="px-3 py-2 small fw-semibold text-secondary">
                            WEEK
                        </th>

                        <th class="small fw-semibold text-secondary">
                            HOURS
                        </th>

                        <th width="130" class="text-center small fw-semibold text-secondary">
                            STATUS
                        </th>

                        <th width="180" class="text-center small fw-semibold text-secondary">
                            ACTIONS
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($timesheets as $timesheet)
                        <tr class="border-top">

                            {{-- Week --}}
                            <td class="px-3 py-2">

                                <div class="d-flex align-items-center">

                                    <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center me-2"
                                        style="width:34px;height:34px;">

                                        <i class="fa-solid fa-calendar-week"></i>

                                    </div>

                                    <div class="fw-semibold text-dark">
                                        {{ $timesheet->week_range }}
                                    </div>

                                </div>

                            </td>

                            {{-- Hours --}}
                            <td>

                                <span class="fw-semibold">
                                    {{ number_format($timesheet->total_hours, 2) }}
                                </span>

                                <small class="text-muted">
                                    hrs
                                </small>

                            </td>

                            {{-- Status --}}
                            <td class="text-center">

                                @if ($timesheet->status === 'draft')
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">
                                        Draft
                                    </span>
                                @elseif($timesheet->status === 'submitted')
                                    <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                                        Submitted
                                    </span>
                                @elseif($timesheet->status === 'approved')
                                    <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                                        Approved
                                    </span>
                                @elseif($timesheet->status === 'rejected')
                                    <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-2">
                                        Rejected
                                    </span>
                                @endif

                            </td>

                            {{-- Actions --}}
                            <td class="text-center">

                                <div class="d-flex justify-content-center gap-1">

                                    @if ($timesheet->status === 'draft')
                                        {{-- Edit --}}
                                        <a href="{{ route('timesheets.edit', $timesheet->id) }}"
                                            class="btn btn-light border btn-sm rounded-3" title="Edit Timesheet">

                                            <i class="fa-solid fa-pen text-warning"></i>

                                        </a>

                                        {{-- Submit --}}
                                        <form action="{{ route('timesheets.submit', $timesheet->id) }}" method="POST"
                                            class="d-inline">

                                            @csrf

                                            <button type="submit" class="btn btn-light border btn-sm rounded-3"
                                                onclick="return confirm('Submit this timesheet?')" title="Submit Timesheet">

                                                <i class="fa-solid fa-check-circle text-success"></i>

                                            </button>

                                        </form>

                                        {{-- Delete --}}
                                        <form action="{{ route('timesheets.destroy', $timesheet->id) }}" method="POST"
                                            class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-light border btn-sm rounded-3"
                                                onclick="return confirm('Delete this timesheet?')" title="Delete Timesheet">

                                                <i class="fa-solid fa-trash text-danger"></i>

                                            </button>

                                        </form>
                                    @endif


                                    {{-- View --}}
                                    <a href="{{ route('timesheets.show', $timesheet->id) }}"
                                        class="btn btn-light border btn-sm rounded-3" title="View Timesheet">

                                        <i class="fa fa-eye text-info"></i>

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="text-center py-4">

                                <i class="fa-solid fa-folder-open text-secondary fs-2 mb-2"></i>

                                <div class="fw-semibold">
                                    No Timesheets Found
                                </div>

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        @if ($timesheets->hasPages())
            <div class="card-footer bg-white border-0 py-2 px-3">

                {{ $timesheets->links() }}

            </div>
        @endif

    </div>

@endsection

@push('styles')
    <style>
        .table td,
        .table th {
            vertical-align: middle;
        }

        .badge {
            font-size: 11px;
            font-weight: 600;
        }

        .btn-sm {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border-radius: 18px;
        }
    </style>
@endpush
