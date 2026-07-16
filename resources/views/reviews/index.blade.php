@extends('layouts.app')

@section('title', 'Reviews | Unibs Tools')

@push('styles')
    <style>
        .select2-container {
            width: 100% !important;
        }

        table th,
        tr {
            text-align: center;
        }

        /* Custom Top Controls */
        #custom-length select {
            border-radius: 6px;
            padding: 4px 8px;
            border: 1px solid #dee2e6;
        }

        #custom-search input {
            border-radius: 8px;
            padding: 6px 12px;
            border: 1px solid #dee2e6;
        }

        /* Pagination Button */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: .15em 0.6em;
        }

        .dataTables_wrapper .dataTables_info {
            padding-top: 10px;
        }

        #listTable_paginate {
            padding-top: 10px;
        }

        .modal-content {
            border-radius: 12px;
        }

        .modal-header {
            border-bottom: 1px solid #eee;
        }

        .modal-footer {
            border-top: 1px solid #eee;
        }

        .history-item:hover {
            background-color: #f1f1f1;
        }

        .active-history {
            background-color: #ffe8a1;
            border-color: #ffc107;
        }

        audio {
            border-radius: 20px;
            height: 32px;
        }

        audio::-webkit-media-controls-panel {
            border-radius: 20px;
        }

        audio {
            width: 150px;
            background: #f1f1f1;
        }
    </style>
@endpush

@section('content')

    @include('partials.message')

    {{-- PAGE TITLE --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Reviews</h4>

        <div class="d-flex gap-2">
            <a href="{{ route('reviews.export.excel') }}" id="exportExcel" class="btn btn-success btn-sm">
                <i class="fa fa-file-excel"></i> Excel
            </a>

            <a href="{{ route('reviews.export.pdf') }}" id="exportPdf" class="btn btn-danger btn-sm">
                <i class="fa fa-file-pdf"></i> PDF
            </a>
        </div>
    </div>

    {{-- FILTER CARD --}}
    <div class="card mb-3 p-3">
        <form method="GET" action="{{ route('reviews.index') }}" id="filterForm">
            <div class="row align-items-end">

                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control"
                        value="{{ request('from_date', now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control"
                        value="{{ request('to_date', now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        Employees
                    </label>

                    <div class="dropdown">
                        <button id="employeeBtn" class="btn btn-outline-secondary dropdown-toggle w-100 text-start"
                            type="button" data-bs-toggle="dropdown">
                            Employees
                        </button>

                        <ul class="dropdown-menu w-100 p-3" style="max-height: 250px; overflow-y: auto;">

                            @foreach ($employees as $employee)
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input employee-checkbox" type="checkbox" name="user_id[]"
                                            value="{{ $employee->id }}" id="employee_{{ Str::slug($employee->name) }}"
                                            {{ in_array($employee->id, (array) request('user_id')) ? 'checked' : '' }}>

                                        <label class="form-check-label" for="employee_{{ Str::slug($employee->name) }}">
                                            {{ ucfirst($employee->name) }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach

                        </ul>
                    </div>
                </div>

                {{-- Call Type --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        Call Type <span class="text-danger">*</span>
                    </label>

                    <div class="dropdown">
                        <button id="collType" class="btn btn-outline-secondary dropdown-toggle w-100 text-start"
                            type="button" data-bs-toggle="dropdown">
                            Call Type
                        </button>

                        <ul class="dropdown-menu w-100 p-3" style="max-height: 250px; overflow-y: auto;">
                            @php
                                $types = ['incoming', 'outgoing', 'missed', 'Rejected'];
                            @endphp

                            @foreach ($types as $type)
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input role-checkbox" type="checkbox" name="types[]"
                                            value="{{ $type }}" id="employee_{{ Str::slug($type) }}"
                                            {{ in_array($type, (array) request('types')) ? 'checked' : '' }}>

                                        <label class="form-check-label" for="employee_{{ Str::slug($type) }}">
                                            {{ ucfirst($type) }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @error('employees')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Duration --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        Duration <span class="text-danger">*</span>
                    </label>

                    <div class="dropdown">
                        <button id="duration" class="btn btn-outline-secondary dropdown-toggle w-100 text-start"
                            type="button" data-bs-toggle="dropdown">
                            Duration
                        </button>

                        <ul class="dropdown-menu w-100 p-3" style="max-height: 250px; overflow-y: auto;">
                            @php
                                $durations = ['Greater then', 'Less then', 'Between'];
                            @endphp

                            @foreach ($durations as $duration)
                                <li>
                                    <div class="form-check">


                                        <label class="form-check-label" for="duration_{{ Str::slug($duration) }}">
                                            <input class="form-check-input duration-radio" type="radio" name="duration"
                                                value="{{ $duration }}" id="duration_{{ Str::slug($duration) }}"
                                                {{ request('duration') == $duration ? 'checked' : '' }}>
                                            {{ ucfirst($duration) }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @error('employees')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Duration Value --}}
                <div class="col-md-2 d-none" id="durationValueBox">
                    <label class="form-label fw-semibold">
                        Duration (sec)
                    </label>

                    <input type="number" name="duration_value" class="form-control"
                        value="{{ request('duration_value') }}" placeholder="Duration">
                </div>

                <div class="col-md-2 d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-filter"></i>
                    </button>

                    <a href="{{ route('reviews.index') }}" class="btn btn-secondary">
                        <i class="fa fa-refresh"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white" id="listTable">

            <thead class="table-dark">
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">From</th>
                    <th class="text-center">To</th>
                    <th class="text-center">Date & Time</th>
                    <th class="text-center">Recording</th>
                    <th class="text-center">Type</th>
                    <th class="text-center">Notes</th>
                </tr>

                <tr class="filter-row text-center">
                    <th></th>
                    <th><input type="text" class="form-control form-control-sm" placeholder="From"></th>
                    <th><input type="text" class="form-control form-control-sm" placeholder="To"></th>
                    <th><input type="text" class="form-control form-control-sm" placeholder="Date & Time"></th>
                    <th></th>
                    <th><input type="text" class="form-control form-control-sm" placeholder="Type"></th>
                    <th></th>
                </tr>
            </thead>

            <tbody id="reviewTableBody">
            </tbody>

        </table>

        <div class="modal fade" id="noteModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Call Note</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            {{-- LEFT: NOTE --}}
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">Call Note</label>
                                <textarea class="form-control" id="noteText" rows="8" placeholder="Write note..."></textarea>
                                <small class="text-muted float-end" id="charCount">0/1000</small>
                            </div>

                            {{-- RIGHT: DETAILS --}}
                            <div class="col-md-5">
                                <h6 class="fw-bold">Call Details</h6>
                                <hr>

                                <p><strong>From:</strong> <span id="modalFrom"></span></p>
                                <p><strong>To:</strong> <span id="modalTo"></span></p>
                                <p><strong>Date:</strong> <span id="modalDate"></span></p>
                                <p><strong>Time:</strong> <span id="modalTime"></span></p>
                                {{-- <p><strong>Duration:</strong> <span id="modalDuration"></span></p> --}}
                                <p><strong>Type:</strong> <span id="modalType"></span></p>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-warning" id="saveNoteBtn">Save</button>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="historyModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    {{-- Header --}}
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa fa-sticky-note text-warning"></i> Call Note History
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    {{-- Body --}}
                    <div class="modal-body">
                        <div class="row">

                            {{-- LEFT SIDE (NOTE CONTENT) --}}
                            <div class="col-md-7 border-end">
                                <p class="text-muted mb-1">
                                    Last Modified: <span id="historyLastModified"></span>
                                </p>

                                <div id="historyNoteText" class="p-2 bg-light rounded" style="min-height:120px;">
                                    Select history...
                                </div>
                            </div>

                            {{-- RIGHT SIDE (TIMELINE) --}}
                            <div class="col-md-5">
                                <div id="historyTimeline">
                                    {{-- Dynamic history list --}}
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // ✅ Get token from URL
        const urlParams = new URLSearchParams(window.location.search);
        const tokenFromUrl = urlParams.get('token');

        if (tokenFromUrl) {
            localStorage.setItem('token', tokenFromUrl);

            window.history.replaceState({}, document.title, window.location.pathname);
        }

        let table;

        $(document).ready(function() {
            // Get values on page load
            let fromDate = $('#fromDate').val();
            let toDate = $('#toDate').val();



            table = $('#listTable').DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50],
                pagingType: "simple_numbers",
                dom: 'rtip',

                columnDefs: [{
                    targets: [0, 4, 6],
                    orderable: false,
                    searchable: false,
                }],

                initComplete: function() {

                    let api = this.api();

                    // Apply column search
                    api.columns().every(function(index) {

                        let column = this;

                        $('input', $('.filter-row th').eq(index))
                            .on('keyup change clear', function() {

                                if (column.search() !== this.value) {

                                    column
                                        .search(this.value)
                                        .draw();
                                }
                            });
                    });
                }
            });

            // ✅ LOAD DATA FIRST TIME WITH PARAMS
            fetchReviews({
                from_date: fromDate,
                to_date: toDate
            });

            // ✅ FILTER FORM
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                fetchReviews($(this).serialize());
            });

        });

        function fetchReviews(params = {}) {

            $.ajax({
                url: "{{ route('reviews.index') }}",
                type: 'GET',
                data: params,

                success: function(res) {

                    console.log(res); // 👈 ADD THIS DEBUG

                    table.clear(); // ✅ IMPORTANT

                    if (res.data.length === 0) {
                        $('#listTable tbody').html(
                            `<tr>
                                <td colspan="7" class="text-center">No data found</td>
                            </tr>`
                        );
                        return;
                    }

                    res.data.forEach((review, index) => {

                        let formattedDuration = '-';

                        if (review.duration && !isNaN(review.duration)) {
                            let totalSeconds = parseInt(review.duration, 10);

                            let minutes = Math.floor(totalSeconds / 60);
                            let seconds = totalSeconds % 60;

                            formattedDuration = `${minutes}:${String(seconds).padStart(2, '0')}`;
                        }

                        let audio = review.recording_name ? `
                        <div class="d-flex flex-column align-items-center gap-1">
                            
                            <!-- Duration Text -->
                            <small style="color: #ffa500;">${formattedDuration}</small>

                            <!-- Audio Player -->
                            <audio controls style="width:180px; height:30px;">
                                <source src="/storage/${review.recording_path}/${review.recording_name}" type="audio/mpeg">
                            </audio>

                        </div>
                    ` : '-';

                        let action = '';

                        if (!review.notes || review.notes.trim() === '') {

                            // ✅ NO NOTE → show Add Note button
                            action = `
                                    <button class="btn btn-sm text-primary fw-semibold add-note-btn"
                                        data-id="${review.id}"
                                        data-from="${review.from_number}"
                                        data-to="${review.to_number}"
                                        data-date="${review.call_date}"
                                        data-time="${review.call_time}"
                                        data-duration="${review.duration}"
                                        data-type="${review.type}"
                                        data-updated_by="${review.updated_by}"
                                        data-notes="">
                                        Note+
                                    </button>
                                `;

                        } else {

                            action = `
                                    <div class="d-flex justify-content-between align-items-center">

                                        <!-- NOTE TEXT -->
                                        <span class="text-muted small text-truncate" style="max-width:120px;">
                                            ${review.notes}
                                        </span>

                                        <!-- THREE DOTS -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm p-0 border-0 bg-transparent"
                                                    data-bs-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end">

                                                <li>
                                                    <a href="#" class="dropdown-item add-note-btn"
                                                        data-id="${review.id}"
                                                        data-from="${review.from_number}"
                                                        data-to="${review.to_number}"
                                                        data-date="${review.call_date}"
                                                        data-time="${review.call_time}"
                                                        data-duration="${review.duration}"
                                                        data-type="${review.type}"
                                                        data-notes="${review.notes}"
                                                        data-updated_by="${review.updated_by}">
                                                        ✏ Edit
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="#" class="dropdown-item view-history-btn"
                                                        data-id="${review.id}">
                                                        🕘 History
                                                    </a>
                                                </li>

                                            </ul>
                                        </div>

                                    </div>
                                `;
                        }

                        let formattedDateTime = '-';

                        if (review.call_date && review.call_time) {

                            let dateObj = new Date(review.call_date + ' ' + review.call_time);

                            let formattedDate = dateObj.toLocaleDateString('en-GB', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });

                            let formattedTime = dateObj.toLocaleTimeString('en-US', {
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                                hour12: true
                            });

                            formattedDateTime = `
                                <div class="text-center">
                                    <div>${formattedDate}</div>
                                    <div class="text-muted small">${formattedTime}</div>
                                </div>
                            `;
                        }

                        let typeBadge = '-';

                        if (review.type === 'incoming') {
                            typeBadge = `<span class="badge bg-success">
                                            <i class="fa fa-arrow-down"></i> Incoming
                                        </span>`;
                        } else if (review.type === 'outgoing') {
                            typeBadge = `<span class="badge bg-info text-dark">
                                            <i class="fa fa-arrow-up"></i> Outgoing
                                        </span>`;
                        } else if (review.type === 'missed') {
                            typeBadge = `<span class="badge bg-warning text-dark">
                                            <i class="fa fa-phone"></i> Missed
                                        </span>`;
                        } else if (review.type === 'rejected') {
                            typeBadge = `<span class="badge bg-danger">
                                            <i class="fa fa-times"></i> Rejected
                                        </span>`;
                        }

                        table.row.add([
                            index + 1,
                            review.from_name ?
                            `${review.from_name} (${review.from_number})` :
                            review.from_number,
                            review.to_name ?
                            `${review.to_name} (${review.to_number})` :
                            review.to_number,
                            formattedDateTime,
                            audio,
                            typeBadge,
                            action
                        ]);
                    });

                    table.draw();
                },

                error: function(err) {
                    console.log(err);
                    alert('Something went wrong while loading data');
                }
            });
        }
    </script>

    <script>
        // ✅ DURATION UI
        document.addEventListener('DOMContentLoaded', function() {

            const durationRadios = document.querySelectorAll('.duration-radio');
            const durationBox = document.getElementById('durationValueBox');
            const durationBtn = document.getElementById('duration');

            function updateDurationUI() {
                const selected = document.querySelector('.duration-radio:checked');

                if (selected) {
                    durationBox.classList.remove('d-none');
                    durationBtn.textContent = selected.value;
                } else {
                    durationBox.classList.add('d-none');
                    durationBtn.textContent = 'Duration';
                }
            }

            durationRadios.forEach(radio => {
                radio.addEventListener('change', updateDurationUI);
            });

            updateDurationUI();
        });
    </script>

    <script>
        let currentReviewId = null;

        // ✅ OPEN NOTE MODAL
        $(document).on('click', '.add-note-btn', function() {

            currentReviewId = $(this).data('id');

            $('#modalFrom').text($(this).data('from'));
            $('#modalTo').text($(this).data('to'));
            $('#modalDate').text($(this).data('date'));
            $('#modalTime').text($(this).data('time'));
            $('#modalDuration').text($(this).data('duration'));
            $('#modalType').text($(this).data('type'));

            $('#noteText').val($(this).data('notes') || '');
            $('#charCount').text($('#noteText').val().length + '/1000');

            $('#noteModal').modal('show');
        });

        // ✅ CHARACTER COUNT
        $('#noteText').on('input', function() {
            let len = $(this).val().length;
            $('#charCount').text(len + '/1000');
        });

        // ✅ SAVE NOTE
        $('#saveNoteBtn').on('click', function() {

            $.ajax({
                url: "{{ route('reviews.saveNote') }}",
                type: 'POST',
                data: {
                    review_id: currentReviewId,
                    note: $('#noteText').val(),
                    _token: "{{ csrf_token() }}"
                },

                success: function(res) {
                    console.log(res);

                    if (res.success) {
                        $('#noteModal').modal('hide');
                        fetchReviews(); // ✅ reload table
                    }
                },

                error: function(err) {
                    console.log(err.responseText); // 👈 ADD THIS
                    alert('Error saving note');
                }
            });
        });
    </script>

    <script>
        // ✅ VIEW HISTORY
        $(document).on('click', '.view-history-btn', function() {

            let reviewId = $(this).data('id');
            $.ajax({
                url: "{{ url('/reviews/history') }}/" + reviewId,
                type: 'GET',

                success: function(res) {

                    if (!res || res.length === 0) {
                        $('#historyTimeline').html('<p>No history</p>');
                        return;
                    }

                    let timeline = '';
                    let first = res[0];

                    $('#historyLastModified').text(first.created_at + ' by ' + first.user);
                    $('#historyNoteText').text(first.note);

                    res.forEach(function(item, index) {

                        timeline += `
                        <div class="history-item p-2 mb-2 border rounded ${index === 0 ? 'active-history' : ''}"
                            data-note="${item.note}"
                            data-date="${item.created_at}"
                            data-user="${item.user}"
                            style="cursor:pointer;">

                            <strong>${item.created_at}</strong><br>
                            <small>by ${item.user}</small>
                        </div>
                    `;
                    });

                    $('#historyTimeline').html(timeline);

                    new bootstrap.Modal(document.getElementById('historyModal')).show();
                }
            });
        });

        $(document).on('click', '.history-item', function() {

            $('.history-item').removeClass('active-history');
            $(this).addClass('active-history');

            $('#historyNoteText').text($(this).data('note'));
            $('#historyLastModified').text($(this).data('date') + ' by ' + $(this).data('user'));
        });
    </script>
@endpush
