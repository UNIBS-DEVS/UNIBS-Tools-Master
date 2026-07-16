@extends('layouts.app')

@section('title', 'View Timesheet | Unibs Tools')

@section('content')
    <div class="container-fluid mt-4 mb-5">

        @include('partials.message')

        <!-- Header -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">
                        <i class="fa-solid fa-calendar-week text-primary me-2"></i>
                        Timesheet – {{ $timesheet->user->name }}
                    </h4>
                    <div class="text-muted">
                        {{ \Carbon\Carbon::parse($timesheet->week_start)->format('d M Y') }}
                        →
                        {{ \Carbon\Carbon::parse($timesheet->week_end)->format('d M Y') }}
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('timesheets.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>

                    @if ($timesheet->status === 'draft')
                        <a href="{{ route('timesheets.edit', $timesheet->id) }}" class="btn btn-primary">
                            <i class="fa-solid fa-pen"></i>
                        </a>

                        <form action="{{ route('timesheets.destroy', $timesheet->id) }}" method="POST"
                            onsubmit="return confirm('This will permanently delete the timesheet. Continue?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status & Total -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-info text-dark px-3 py-2">
                {{ ucfirst($timesheet->status) }}
            </span>

            <h5 class="mb-0">
                Total:
                <strong class="text-primary">
                    {{ number_format($totalHours, 2) }} hrs
                </strong>
            </h5>
        </div>

        <!-- Overall Remarks -->
        @if ($timesheet->user_remarks)
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body bg-light">
                    <h6 class="fw-bold mb-2">
                        <i class="fa-solid fa-note-sticky me-1"></i>
                        Overall Remarks
                    </h6>
                    <p class="mb-0">{{ $timesheet->user_remarks }}</p>
                </div>
            </div>
        @endif

        <!-- Entries Grouped By Date -->
        @foreach ($entries->groupBy('work_date') as $date => $dayEntries)
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>
                        {{ \Carbon\Carbon::parse($date)->format('d M Y (D)') }}
                    </strong>
                    <span class="text-muted">
                        Day Total:
                        <strong>
                            {{ number_format($dayEntries->sum('hours'), 2) }} hrs
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
                                    <td>{{ $entry->subActivity->activity->project->name ?? '-' }}</td>
                                    <td>{{ $entry->subActivity->activity->name ?? '-' }}</td>
                                    <td>{{ $entry->subActivity->name ?? '-' }}</td>
                                    <td>{{ $entry->customer->customer ?? '-' }}</td>
                                    <td>{{ $entry->request_id ?? '-' }}</td>
                                    <td class="text-end">
                                        {{ number_format($entry->hours, 2) }}
                                    </td>
                                    <td>{{ $entry->remarks ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        <div class="mt-3">
            {{ $entries->links() }}
        </div>

    </div>
@endsection
