@extends('layouts.app')

@section('title', 'Sales Report')

@push('styles')
    <style>
        .table tbody tr td,
        .table thead tr th {
            padding: .15rem .5rem;
            font-size: 13px;
        }
    </style>
@endpush

@section('content')

    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-2">

        <h5>Sourcing Report</h5>

        <div class="d-flex align-items-center gap-1">

            <form method="GET" action="{{ route('reports.sourcing') }}">

                <select name="report_type" class="form-select form-select-sm" onchange="this.form.submit()">

                    <option value="daily_summary" {{ $type == 'daily_summary' ? 'selected' : '' }}>
                        Daily Summary
                    </option>

                    <option value="interview_schedule" {{ $type == 'interview_schedule' ? 'selected' : '' }}>
                        Interview Schedule
                    </option>

                    <option value="closures" {{ $type == 'closures' ? 'selected' : '' }}>
                        Closures
                    </option>

                    <option value="customer_status" {{ $type == 'customer_status' ? 'selected' : '' }}>
                        Customer Status
                    </option>

                </select>

            </form>

            {{-- <form id="exportForm" action="{{ route('reports.sourcing.export') }}" method="GET" style="display:inline;">

                <input type="hidden" name="report_type" value="{{ $type }}">

                <input type="hidden" name="visible_ids" id="visible_ids">

                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel"></i>
                </button>

            </form> --}}

            <form id="exportForm" action="{{ route('reports.sourcing.export') }}" method="GET">

                <input type="hidden" name="report_type" value="{{ $type }}">

                <input type="hidden" name="visible_ids" id="visible_ids">

                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel"></i>
                </button>
            </form>
        </div>

    </div>

    @if ($type == 'daily_summary')
        @include('reports.sourcing.daily_summary')
    @elseif ($type == 'interview_schedule')
        @include('reports.sourcing.interview_schedule')
    @elseif ($type == 'closures')
        @include('reports.sourcing.closures')
    @elseif ($type == 'customer_status')
        @include('reports.sourcing.customer_status')
    @endif

@endsection

@push('scripts')
    <script>
        $('#exportForm').on('submit', function() {

            let ids = [];

            $('#reportTable tbody tr:visible').each(function() {
                ids.push($(this).data('id'));
            });

            $('#visible_ids').val(ids.join(','));
        });
    </script>
@endpush
