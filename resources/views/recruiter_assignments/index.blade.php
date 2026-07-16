@extends('layouts.app')

@section('title', 'Recruiter Assignment Grid')

@section('content')

    <div class="container-fluid">

        {{-- Flash --}}
        <div id="ajax-message"></div>

        {{-- Toolbar --}}
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body py-2">

                <div class="d-flex align-items-center gap-2 flex-wrap">

                    <label class="fw-bold mb-0">
                        Select Date:
                    </label>

                    <input type="date" id="assignment_date" class="form-control" style="width:180px;"
                        value="{{ request('date', now()->format('Y-m-d')) }}">

                    <button type="button" id="saveAssignments" class="btn btn-success btn-sm">
                        Save
                    </button>
                </div>

            </div>
        </div>

        {{-- Grid --}}
        <div class="assignment-wrapper">

            <table class="table table-bordered assignment-table mb-0">

                <thead>

                    <tr>

                        <th class="sticky-col-1">
                            Job Position
                        </th>

                        <th class="sticky-col-2">
                            Skill Set
                        </th>

                        @foreach ($recruiters as $recruiter)
                            <th class="text-center recruiter-col">
                                {{ $recruiter->name }}
                            </th>
                        @endforeach

                    </tr>

                </thead>

                <tbody>

                    @forelse ($customers as $customer)

                        @continue($customer->jobs->isEmpty())

                        {{-- Customer Header --}}
                        <tr class="customer-row">
                            <td class="sticky-col-1">
                                <strong>{{ $customer->customer }}</strong>
                            </td>

                            <td class="sticky-col-2"></td>

                            @foreach ($recruiters as $recruiter)
                                <td></td>
                            @endforeach
                        </tr>

                        @foreach ($customer->jobs as $job)
                            <tr>

                                <td class="sticky-col-1 text-start">

                                    {{-- <a href="{{ route('') }}">{{ $job->position }}</a> --}}

                                    <a href="{{ route('customer-jobs.show', $job->id) }}"
                                        class="text-primary action-btn px-1" target="_blank" style="text-decoration:none;">

                                        {{ $job->position }}

                                    </a>

                                </td>

                                <td class="sticky-col-2 text-start">

                                    {{ $job->skill }}

                                </td>

                                @foreach ($recruiters as $recruiter)
                                    <td class="text-center">

                                        @php
                                            $assigned = $job->recruiterAssignments
                                                ->where('recruiter_id', $recruiter->id)
                                                ->isNotEmpty();
                                        @endphp

                                        <input type="checkbox" class="form-check-input recruiter-checkbox"
                                            data-job="{{ $job->id }}" data-recruiter="{{ $recruiter->id }}"
                                            {{ $assigned ? 'checked' : '' }}>

                                    </td>
                                @endforeach

                            </tr>
                        @endforeach

                    @empty

                        <tr>

                            <td colspan="{{ 2 + $recruiters->count() }}" class="text-center py-4">

                                No Open Jobs Found

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection

@push('styles')
    <style>
        .assignment-wrapper {
            overflow: auto;
            max-height: 75vh;
            border: 1px solid #dfe3e8;
            border-radius: 12px;
            background: #fff;
            width: 100%;
        }

        .assignment-table {
            min-width: 1200px;
        }

        .assignment-table th {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .sticky-col-1 {
            position: sticky;
            left: 0;
            min-width: 250px;
            background: #fff;
            z-index: 5;
        }

        .sticky-col-2 {
            position: sticky;
            left: 250px;
            min-width: 220px;
            background: #fff;
            z-index: 5;
        }

        thead .sticky-col-1,
        thead .sticky-col-2 {
            z-index: 15;
        }

        .customer-row .sticky-col-1,
        .customer-row .sticky-col-2 {
            z-index: 6;
        }

        .customer-row .sticky-col-1 {
            position: sticky;
            left: 0;
            background: #5f748a !important;
            color: #fff;
        }

        .customer-row .sticky-col-2 {
            position: sticky;
            background: #5f748a !important;
        }

        .customer-row td {
            background: #5f748a !important;
            color: #fff;
            font-weight: 700;
            padding: 12px;
        }

        .assignment-table td {
            vertical-align: middle;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .recruiter-checkbox {
            width: 20px;
            height: 20px;
            border: 1px solid #618dbc !important;
            background-color: #fff;
        }

        .recruiter-checkbox:not(:checked) {
            background-color: #f8f9fa;
        }

        .assignment-table,
        .assignment-table th,
        .assignment-table td {
            border: 1px solid #618dbc !important;
        }



        #ajax-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            min-width: 320px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let assignments = [];

        $(document).on('change', '.recruiter-checkbox', function() {

            let item = {
                customer_job_id: $(this).data('job'),
                recruiter_id: $(this).data('recruiter'),
                checked: $(this).is(':checked') ? 1 : 0
            };

            assignments.push(item);
        });

        $('#saveAssignments').on('click', function() {

            if (assignments.length === 0) {

                $('#ajax-message').html(`
        <div class="alert alert-warning alert-dismissible fade show shadow-sm">
            No changes to save.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);

                return;
            }

            $.ajax({
                url: "{{ route('recruiter-assignments.toggle') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    assignment_date: $('#assignment_date').val(),
                    assignments: assignments
                },

                success: function(response) {

                    $('#ajax-message').html(`
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                <i class="fa-solid fa-circle-check me-2"></i>
                Assignments saved successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);

                    assignments = [];

                    setTimeout(function() {
                        $('#ajax-message').html('');
                    }, 3000);
                },

                error: function() {

                    $('#ajax-message').html(`
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                Failed to save assignments.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
                }
            });

        });

        // $(document).on('change', '.recruiter-checkbox', function() {

        //     let checkbox = $(this);

        //     $.ajax({
        //         url: "{{ route('recruiter-assignments.toggle') }}",
        //         method: "POST",
        //         data: {
        //             _token: "{{ csrf_token() }}",
        //             customer_job_id: checkbox.data('job'),
        //             recruiter_id: checkbox.data('recruiter'),
        //             assignment_date: $('#assignment_date').val(),
        //             checked: checkbox.is(':checked') ? 1 : 0
        //         },
        //         success: function(response) {

        //             $('#ajax-message').html(`
    //                     <div class="alert alert-success alert-dismissible fade show shadow-sm">
    //                         <i class="fa-solid fa-circle-check me-2"></i>
    //                         Assignment saved successfully.
    //                         <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    //                     </div>
    //                 `);

        //             setTimeout(function() {
        //                 $('#ajax-message').html('');
        //             }, 1000);
        //         },
        //         error: function(xhr) {

        //             $('#ajax-message').html(`
    //                     <div class="alert alert-danger alert-dismissible fade show shadow-sm">
    //                         <i class="fa-solid fa-circle-xmark me-2"></i>
    //                         Failed to save assignment.
    //                         <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    //                     </div>
    //                 `);

        //             checkbox.prop('checked', !checkbox.prop('checked'));

        //             setTimeout(function() {
        //                 $('#ajax-message').html('');
        //             }, 3000);
        //         }
        //     });

        // });

        $('#assignment_date').change(function() {

            let date = $(this).val();

            window.location.href =
                "{{ route('recruiter-assignments.index') }}" +
                "?date=" + date;

        });
    </script>
@endpush
