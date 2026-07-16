@extends('layouts.app')

@section('title', 'Edit Timesheet | Unibs Tools')

@section('content')
    <div class="container-fluid ">

        @include('partials.message')

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 fw-semibold">
                <i class="fa-solid fa-calendar-week me-2 text-primary"></i>
                Edit Weekly Timesheet
            </h4>

            <a href="{{ route('timesheets.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left"></i>
            </a>
        </div>
        <div class="card shadow-sm border-0">

            <!-- Body -->
            <div class="card-body">

                <!-- Week Picker -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Week
                    </label>

                    <div class="d-flex align-items-end gap-2">
                        <div>
                            <input type="date" id="weekPicker" class="form-control" style="max-width:300px;">
                        </div>

                        <button type="button" id="copyPreviousWeek" class="btn btn-outline-primary">
                            <i class="fa fa-copy me-1"></i>
                            Copy from previous week
                        </button>
                    </div>

                    <div class="text-muted mt-2">
                        Week: <strong id="weekRange">-</strong>
                    </div>
                </div>

                <form method="POST" action="{{ route('timesheets.update', $timesheet->id ?? 0) }}">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="week_start" id="weekStart">
                    <input type="hidden" name="week_end" id="weekEnd">

                    <!-- Days -->
                    <div id="timesheetDays"></div>

                    <!-- Remarks -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Overall Remarks</label>
                        <textarea name="user_remarks" class="form-control" rows="3">{{ old('user_remarks', $timesheet->user_remarks ?? '') }}</textarea>
                    </div>

                    <!-- Footer -->
                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <h5 class="mb-0">
                            Week Total:
                            <strong class="text-primary">
                                <span id="weekTotal">0.00</span> hrs
                            </strong>
                        </h5>

                        <div>
                            <a href="{{ route('timesheets.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="fa fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fa-solid fa-save me-1"></i> Update
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const projects = @json($projects);
        const customers = @json($customers);

        // Pre-populated data from the timesheet
        const timesheetData = @json($timesheetData ?? []);

        /* ======================
           WEEK PICKER
        ====================== */
        $('#weekPicker').on('change', function() {
            buildWeek($(this).val());
        });

        function buildWeek(selectedDate) {
            if (!selectedDate) return;

            const date = new Date(selectedDate);
            const day = date.getDay() || 7;
            date.setDate(date.getDate() - day + 1);

            const start = new Date(date);
            const end = new Date(date);
            end.setDate(start.getDate() + 6);

            document.getElementById('weekStart').value = formatDate(start);
            document.getElementById('weekEnd').value = formatDate(end);
            document.getElementById('weekRange').innerText = formatDate(start) + ' → ' + formatDate(end);

            const container = document.getElementById('timesheetDays');
            container.innerHTML = '';

            for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
                addDayCard(new Date(d));
            }

            // Populate existing data after cards are created
            populateExistingData();
        }

        /* ======================
           DAY CARD
        ====================== */
        function addDayCard(date) {
            const iso = formatDate(date);
            if (document.querySelector(`[data-day="${iso}"]`)) return;

            const container = document.getElementById('timesheetDays');
            container.insertAdjacentHTML('beforeend', `
                <div class="card mb-3 day-card" data-day="${iso}">
                    <div class="card-header d-flex justify-content-between">
                        <strong>${displayDate(date)}</strong>
                        <span>Day total: <strong class="day-total">0.00</strong> hrs</span>
                    </div>
                    <div class="card-body task-container">
                        ${taskRowTemplate(iso)}
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2 add-task">
                            <i class="fa fa-plus"></i> Add Task
                        </button>
                    </div>
                </div>
            `);
        }

        /* ======================
           TASK ROWS
        ====================== */
        function taskRowTemplate(day) {
            return `
            <div class="task-row border rounded p-3 mb-3 position-relative">
                <span class="remove-task text-danger position-absolute top-0 end-0 p-2">
                    <i class="fa fa-trash"></i>
                </span>
                <div class="row g-2">
                   
                    <input type="hidden" name="user_submission_at" value="{{ now() }}"> 
                    
                    <div class="col-md-4">
                        <select
                            name="tasks[${day}][project_id][]"
                            class="form-select project-select">

                            ${projectOptions()}

                        </select>
                    </div> 

                    <div class="col-md-4">
                        <select
                            name="tasks[${day}][activity_id][]"
                            class="form-select activity-select">

                            <option value="">Activity</option>

                        </select>
                    </div> 

                    <div class="col-md-4">
                        <select
                            name="tasks[${day}][sub_activity_id][]"
                            class="form-select subactivity-select">

                            <option value="">Sub Activity</option>

                        </select>
                    </div> 
                     
                    <div class="col-md-4">
                        <select
                            name="tasks[${day}][customer_id][]"
                            class="form-select customer-select">

                            ${customerOptions()}

                        </select>
                    </div> 

                    <div class="col-md-4">
                        <input type="text" name="tasks[${day}][request_id][]" class="form-control" placeholder="Request ID">
                    </div> 

                    <div class="col-md-4">
                        <input type="number" step="0.25" name="tasks[${day}][hours][]" class="form-control hours-input" placeholder="Working hours">
                    </div>

                    <div class="col-md-12 mt-2"><textarea name="tasks[${day}][description][]" class="form-control"></textarea></div>
                </div>
            </div>`;
        }

        function taskRowTemplateWithData(day, row) {
            return `
                <div class="task-row border rounded p-3 mb-3 position-relative">
                    <span class="remove-task text-danger position-absolute top-0 end-0 p-2">
                        <i class="fa fa-trash"></i>
                    </span>

                    <div class="row g-2">

                        <input type="hidden" name="user_submission_at" value="{{ now() }}"> 

                        <div class="col-md-4">
                            <select
                                name="tasks[${day}][project_id][]"
                                class="form-select project-select">
                                ${projectOptions(row.project_id)}
                            </select>
                        </div>

                        <div class="col-md-4">
                            <select
                                name="tasks[${day}][activity_id][]"
                                class="form-select activity-select">
                                ${activityOptions(row.project_id, row.activity_id)}
                            </select>
                        </div>

                        <div class="col-md-4">
                            <select
                                name="tasks[${day}][sub_activity_id][]"
                                class="form-select subactivity-select">
                                ${subActivityOptions(row.project_id, row.activity_id, row.sub_activity_id)}
                            </select>
                        </div>

                        <div class="col-md-4">
                            <select
                                name="tasks[${day}][customer_id][]"
                                class="form-select customer-select">
                                ${customerOptions(row.customer_id)}
                            </select>
                        </div>

                        <div class="col-md-4">
                            <input
                                type="text"
                                name="tasks[${day}][request_id][]"
                                class="form-control"
                                placeholder="Request ID"
                                value="${row.request_id ?? ''}">
                        </div>

                        <div class="col-md-4">
                            <input
                                type="number"
                                step="0.25"
                                name="tasks[${day}][hours][]"
                                class="form-control hours-input"
                                placeholder="Working hours"
                                value="${row.hours ?? ''}">
                        </div>

                        <div class="col-md-12 mt-2">
                            <textarea
                                name="tasks[${day}][description][]"
                                class="form-control">${row.description ?? ''}</textarea>
                        </div>

                    </div>
                </div>`;
        }

        /* ======================
           POPULATE EXISTING DATA
        ====================== */
        function populateExistingData() {

            if (!timesheetData) return;

            Object.keys(timesheetData).forEach(workDate => {

                const rows = timesheetData[workDate];

                const card = document.querySelector(
                    `[data-day="${workDate}"]`
                );

                if (!card) return;

                const container =
                    card.querySelector('.task-container');

                container.querySelector('.task-row')?.remove();

                const addBtn =
                    container.querySelector('.add-task');

                rows.forEach(row => {

                    addBtn.insertAdjacentHTML(
                        'beforebegin',
                        taskRowTemplateWithData(workDate, row)
                    );

                });
            });

            calculateTotals();
        }
        /* ======================
           COPY PREVIOUS WEEK (FINAL WORKING)
        ====================== */
        document.getElementById('copyPreviousWeek').addEventListener('click', async () => {

            if (!document.getElementById('weekStart').value) {
                alert('Please select a week first');
                return;
            }

            if (!confirm('This will replace all existing tasks for this week. Are you sure?')) {
                return;
            }

            const res = await fetch(
                "{{ route('timesheets.previous-week') }}?week_start=" + document.getElementById('weekStart')
                .value
            );

            const data = await res.json();

            if (!Object.keys(data).length) {
                alert('No previous week timesheet found');
                return;
            }

            // Clear existing days
            document.getElementById('timesheetDays').innerHTML = '';

            const start = new Date(document.getElementById('weekStart').value);

            // Build all 7 days
            for (let i = 0; i < 7; i++) {
                const d = new Date(start);
                d.setDate(start.getDate() + i);
                addDayCard(d);
            }

            // Insert data day-wise
            Object.keys(data).forEach(index => {
                const d = new Date(start);
                d.setDate(start.getDate() + parseInt(index));
                const iso = formatDate(d);

                const card = document.querySelector(`[data-day="${iso}"]`);
                if (!card) return;

                const container = card.querySelector('.task-container');

                // Remove default empty task
                container.querySelector('.task-row')?.remove();

                const addBtn = container.querySelector('.add-task');

                // Add all tasks for the day
                data[index].forEach(row => {
                    addBtn.insertAdjacentHTML(
                        'beforebegin',
                        taskRowTemplateWithData(iso, row)
                    );
                });
            });

            calculateTotals();
        });

        /* ======================
           EVENTS & HELPERS
        ====================== */
        document.addEventListener('click', e => {
            if (e.target.closest('.add-task')) {
                const card = e.target.closest('.day-card');
                card.querySelector('.add-task').insertAdjacentHTML(
                    'beforebegin',
                    taskRowTemplate(card.dataset.day)
                );
            }
            if (e.target.closest('.remove-task')) {
                e.target.closest('.task-row').remove();
                calculateTotals();
            }
        });

        document.addEventListener('input', e => {
            if (e.target.classList.contains('hours-input')) calculateTotals();
        });

        function calculateTotals() {
            let total = 0;
            document.querySelectorAll('.day-card').forEach(card => {
                let day = 0;
                card.querySelectorAll('.hours-input').forEach(i => day += +i.value || 0);
                card.querySelector('.day-total').innerText = day.toFixed(2);
                total += day;
            });
            document.getElementById('weekTotal').innerText = total.toFixed(2);
        }

        /* ======================
           OPTION HELPERS
        ====================== */
        function projectOptions(selectedId = null) {
            let html = '<option value="">Project</option>';
            projects.forEach(project => {
                html += `
                    <option value="${project.id}"
                        ${project.id == selectedId ? 'selected' : ''}>
                        ${project.name}
                    </option>
                `;
            });
            return html;
        }

        function customerOptions(selectedId = null) {
            let html = '<option value="">Customer</option>';
            customers.forEach(customer => {
                html += `
                    <option value="${customer.id}"
                        ${customer.id == selectedId ? 'selected' : ''}>
                        ${customer.customer}
                    </option>
                `;
            });
            return html;
        }

        function getActivities(projectId) {
            const project = projects.find(p => p.id == projectId);
            return project ? project.activities : [];
        }

        function getSubActivities(projectId, activityId) {
            const project = projects.find(p => p.id == projectId);
            if (!project) return [];
            const activity = project.activities.find(a => a.id == activityId);
            return activity ? activity.sub_activities : [];
        }

        function activityOptions(projectId, selectedId = null) {
            let html = '<option value="">Activity</option>';
            if (!projectId) return html;

            getActivities(projectId).forEach(activity => {
                html += `
                    <option value="${activity.id}"
                        ${activity.id == selectedId ? 'selected' : ''}>
                        ${activity.name}
                    </option>
                `;
            });
            return html;
        }

        function subActivityOptions(projectId, activityId, selectedId = null) {
            let html = '<option value="">Sub Activity</option>';
            if (!projectId || !activityId) return html;

            getSubActivities(projectId, activityId).forEach(sub => {
                html += `
                    <option value="${sub.id}"
                        ${sub.id == selectedId ? 'selected' : ''}>
                        ${sub.name}
                    </option>
                `;
            });
            return html;
        }

        /* ======================
           PROJECT / ACTIVITY CHANGE HANDLERS
        ====================== */
        document.addEventListener('change', function(e) {
            // Project Changed
            if (e.target.classList.contains('project-select')) {
                const row = e.target.closest('.task-row');
                const projectId = e.target.value;
                const activitySelect = row.querySelector('.activity-select');
                const subActivitySelect = row.querySelector('.subactivity-select');

                activitySelect.innerHTML = '<option value="">Activity</option>';
                subActivitySelect.innerHTML = '<option value="">Sub Activity</option>';

                if (!projectId) return;

                getActivities(projectId).forEach(activity => {
                    activitySelect.innerHTML += `
                        <option value="${activity.id}">
                            ${activity.name}
                        </option>
                    `;
                });
            }

            // Activity Changed
            if (e.target.classList.contains('activity-select')) {
                const row = e.target.closest('.task-row');
                const projectId = row.querySelector('.project-select').value;
                const activityId = e.target.value;
                const subActivitySelect = row.querySelector('.subactivity-select');

                subActivitySelect.innerHTML = '<option value="">Sub Activity</option>';

                if (!projectId || !activityId) return;

                getSubActivities(projectId, activityId).forEach(sub => {
                    subActivitySelect.innerHTML += `
                        <option value="${sub.id}">
                            ${sub.name}
                        </option>
                    `;
                });
            }
        });

        /* ======================
           DATE HELPERS
        ====================== */
        function formatDate(d) {

            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }

        function displayDate(d) {
            return d.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        /* ======================
           FORM SUBMISSION VALIDATION
        ====================== */
        document.querySelector('form').addEventListener('submit', function(e) {
            let valid = true;
            let message = '';

            document.querySelectorAll('.task-row').forEach(row => {
                const subactivity = row.querySelector('[name*="[sub_activity_id]"]').value;
                const hours = row.querySelector('.hours-input').value;

                if (hours && parseFloat(hours) > 0) {
                    if (!subactivity) {
                        valid = false;
                        message = 'Please select Sub Activity.';
                        return;
                    }
                }
            });

            if (!valid) {
                e.preventDefault();
                alert(message);
            }
        });

        /* ======================
           INITIALIZE - SET WEEK FROM TIMESHEET
        ====================== */
        /* ======================
           INITIALIZE - SET WEEK FROM TIMESHEET
        ====================== */
        /* ======================
           INITIALIZE - SET WEEK FROM TIMESHEET
        ====================== */
        (function initialize() {
            // Format the date properly for the input field
            let weekStartDate =
                '{{ $timesheet->week_start ? \Carbon\Carbon::parse($timesheet->week_start)->format('Y-m-d') : '' }}';
            console.log('Week Start Date:', weekStartDate);

            if (weekStartDate) {
                // Set the week picker value
                const picker = document.getElementById('weekPicker');
                if (picker) {
                    picker.value = weekStartDate;
                }
                // Build the week
                buildWeek(weekStartDate);
            } else {
                // Default to current week
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const formattedDate = `${year}-${month}-${day}`;
                document.getElementById('weekPicker').value = formattedDate;
                buildWeek(formattedDate);
            }
        })();
    </script>

    <script>
        $(document).on('keyup', '#customerSearch', function() {
            let value = $(this).val().toLowerCase();
            $(this)
                .closest('.dropdown-menu')
                .find('.customer-item')
                .each(function() {
                    let text = $(this).text().toLowerCase();
                    $(this).toggle(text.indexOf(value) > -1);
                });
        });
    </script>
@endpush
