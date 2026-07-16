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

        <h5>Sales Report</h5>

        <div class="d-flex align-items-center gap-1">

            <form method="GET" action="{{ route('reports.sales') }}">

                <select name="report_type" class="form-select form-select-sm" onchange="this.form.submit()">

                    <option value="daily_summary" {{ $type == 'daily_summary' ? 'selected' : '' }}>
                        Daily Summary
                    </option>

                    <option value="weekly_summary" {{ $type == 'weekly_summary' ? 'selected' : '' }}>
                        Weekly Summary
                    </option>

                    <option value="follow_up" {{ $type == 'follow_up' ? 'selected' : '' }}>
                        Follow Up
                    </option>

                    <option value="closures" {{ $type == 'closures' ? 'selected' : '' }}>
                        Closures
                    </option>

                    <option value="licence_report" {{ $type == 'licence_report' ? 'selected' : '' }}>
                        Licence Report
                    </option>

                    <option value="tender_report" {{ $type == 'tender_report' ? 'selected' : '' }}>
                        Tender Report
                    </option>

                </select>

            </form>

            <form id="exportForm" action="{{ route('reports.sales.export') }}" method="GET" style="display:inline;">

                <input type="hidden" name="report_type" value="{{ $type }}">

                <input type="hidden" name="visible_ids" id="visible_ids">

                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel"></i>
                </button>

            </form>

        </div>

    </div>

    @if ($type == 'daily_summary')
        @include('reports.sales.daily_summary')
    @elseif ($type == 'weekly_summary')
        @include('reports.sales.weekly_summary')
    @elseif ($type == 'follow_up')
        @include('reports.sales.follow_up')
    @elseif ($type == 'closures')
        @include('reports.sales.closures')
    @elseif ($type == 'licence_report')
        @include('reports.sales.licence_report')
    @elseif ($type == 'tender_report')
        @include('reports.sales.tender_report')
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
