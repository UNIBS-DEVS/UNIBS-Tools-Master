@extends('layouts.app')

@section('title', 'Sub Activities | Unibs Tools')

@section('content')

    <div class="container-fluid py-2">

        @include('partials.message')

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-2">

            <div>

                <h4 class="fw-bold mb-0">
                    <i class="fa-solid fa-list-check text-primary me-2"></i>
                    Sub Activities
                </h4>

                <small class="text-muted">
                    Total Sub Activities: {{ $subActivities->total() }}
                </small>

            </div>

            {{-- Add --}}
            <button class="btn btn-primary btn-sm rounded-3 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#subActivityModal" onclick="createSubActivity()">

                <i class="fa-solid fa-plus"></i>

            </button>

        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead class="bg-light">

                        <tr>

                            <th class="px-3 py-2 small fw-semibold text-secondary">
                                SUB ACTIVITY
                            </th>

                            <th class="px-3 py-2 small fw-semibold text-secondary">
                                PROJECT
                            </th>

                            <th class="px-3 py-2 small fw-semibold text-secondary">
                                ACTIVITY
                            </th>



                            <th width="120" class="text-center small fw-semibold text-secondary">
                                STATUS
                            </th>

                            <th width="120" class="text-center small fw-semibold text-secondary">
                                ACTIONS
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($subActivities as $subActivity)
                            <tr class="border-top">

                                {{-- Name --}}
                                <td class="px-3 py-2">

                                    <div class="d-flex align-items-center">

                                        <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center me-2"
                                            style="width:30px;height:30px;">

                                            <i class="fa-solid fa-layer-group"></i>

                                        </div>

                                        <div class="fw-semibold text-dark">
                                            {{ $subActivity->name }}
                                        </div>

                                    </div>

                                </td>

                                {{-- Activity --}}
                                <td>

                                    <span class="small text-muted">
                                        {{ $subActivity->project->name }}
                                    </span>

                                </td>

                                {{-- Activity --}}
                                <td>

                                    <span class="small text-muted">
                                        {{ $subActivity->activity->name }}
                                    </span>

                                </td>


                                {{-- Status --}}
                                <td class="text-center">

                                    @if ($subActivity->status === 'active')
                                        <span class="badge rounded-pill bg-success-subtle text-success px-2 py-2">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-danger-subtle text-danger px-2 py-2">
                                            Inactive
                                        </span>
                                    @endif

                                </td>

                                {{-- Actions --}}
                                <td class="text-center">

                                    <div class="d-flex justify-content-center gap-1">

                                        {{-- Edit --}}
                                        <button class="btn btn-light border btn-sm rounded-3" data-bs-toggle="modal"
                                            data-bs-target="#subActivityModal"
                                            onclick="editSubActivity(
                                            '{{ $subActivity->id }}',
                                            '{{ $subActivity->name }}',
                                            '{{ $subActivity->project_id }}', 
                                            '{{ $subActivity->activity_id }}',
                                            '{{ $subActivity->status }}'
                                        )">

                                            <i class="fa-solid fa-pen text-warning"></i>

                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4" class="text-center py-4">

                                    <i class="fa-solid fa-folder-open text-secondary fs-2 mb-2"></i>

                                    <div class="fw-semibold">
                                        No Sub Activities Found
                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
            @if ($subActivities->hasPages())
                <div class="card-footer bg-white border-0 py-2 px-3">

                    {{ $subActivities->links() }}

                </div>
            @endif

        </div>

    </div>

    {{-- Modal --}}
    <div class="modal fade" id="subActivityModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered modal-sm">

            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

                <form method="POST" id="subActivityForm">

                    @csrf

                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    {{-- Header --}}
                    <div class="modal-header-custom bg-primary bg-gradient text-white px-4 py-2">

                        <div class="d-flex justify-content-between align-items-center">

                            <h6 class="mb-0 fw-bold" id="modalTitle">
                                Add Sub Activity
                            </h6>

                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal">
                            </button>

                        </div>

                    </div>

                    {{-- Body --}}
                    <div class="modal-body p-4">

                        {{-- Name --}}
                        <div class="mb-3">

                            <label class="form-label small fw-semibold text-secondary mb-1">
                                Sub Activity Name
                            </label>

                            <div class="input-group input-group-sm">

                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fa fa-layer-group text-primary"></i>
                                </span>

                                <input type="text" name="name" id="subActivityName" value="{{ old('name') }}"
                                    class="form-control border-start-0 shadow-none @error('name') is-invalid @enderror">

                            </div>
                            @error('name')
                                <span class="text-danger small mt-1 d-block">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>

                        {{-- Project --}}
                        <div class="mb-3">

                            <label class="form-label small fw-semibold text-secondary mb-1">
                                Project
                            </label>

                            <div class="input-group input-group-sm">

                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fa fa-folder text-primary"></i>
                                </span>

                                <select name="project_id" id="subActivityProject"
                                    class="form-select border-start-0 shadow-none @error('project_id') is-invalid @enderror"
                                    onchange="filterActivities()">

                                    <option value="">
                                        -- Select Project --
                                    </option>

                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}"
                                            {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>
                            @error('project_id')
                                <span class="text-danger small mt-1 d-block">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>

                        {{-- Activity --}}
                        <div class="mb-3">

                            <label class="form-label small fw-semibold text-secondary mb-1">
                                Activity
                            </label>

                            <div class="input-group input-group-sm">

                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fa fa-paintbrush text-primary"></i>
                                </span>

                                <select name="activity_id" id="subActivityActivity"
                                    class="form-select border-start-0 shadow-none @error('activity_id') is-invalid @enderror">

                                    <option value="">
                                        -- Select Activity --
                                    </option>

                                    @foreach ($activities as $activity)
                                        <option value="{{ $activity->id }}" data-project="{{ $activity->project_id }}"
                                            {{ old('activity_id') == $activity->id ? 'selected' : '' }}>

                                            {{ $activity->name }}

                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('activity_id')
                                <span class="text-danger small mt-1 d-block">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>

                        {{-- Status --}}
                        <div>

                            <label class="form-label small fw-semibold text-secondary mb-1">
                                Status
                            </label>

                            <div class="input-group input-group-sm">

                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fa fa-toggle-on text-success"></i>
                                </span>

                                <select name="status" id="subActivityStatus"
                                    class="form-select border-start-0 shadow-none @error('status') is-invalid @enderror"
                                    required>

                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>

                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>

                                </select>

                            </div>
                            @error('status')
                                <span class="text-danger small mt-1 d-block">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="px-4 pb-4 pt-1">

                        <div class="d-flex gap-2">

                            <button type="button" class="btn btn-light border btn-sm w-50 rounded-3"
                                data-bs-dismiss="modal">

                                Cancel

                            </button>

                            <button type="submit" id="submitButton"
                                class="btn btn-primary btn-sm w-50 rounded-3 shadow-sm border-0">

                                <i class="fa fa-save me-1"></i>

                                <span id="submitText">
                                    Save
                                </span>

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection

@push('styles')
    <style>
        .modal-content {
            animation: popupScale .18s ease;
        }

        @keyframes popupScale {
            from {
                opacity: 0;
                transform: scale(.96);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }
    </style>
@endpush

@push('scripts')
    @if ($errors->any())
        <script>
            $(function() {
                new bootstrap.Modal($('#subActivityModal')[0]).show();

                filterActivities();
            });
        </script>
    @endif

    <script>
        function createSubActivity() {

            document.getElementById('modalTitle').innerText =
                'Add Sub Activity';

            document.getElementById('submitText').innerText =
                'Create';

            document.getElementById('subActivityForm').action =
                "{{ route('sub-activities.store') }}";

            document.getElementById('formMethod').value =
                'POST';

            document.getElementById('subActivityName').value =
                '';

            document.getElementById('subActivityActivity').value =
                '';

            document.getElementById('subActivityStatus').value =
                'active';

            document.querySelector('#subActivityModal .modal-header-custom').className =
                'modal-header-custom bg-primary bg-gradient text-white px-4 py-2';

            document.getElementById('submitButton').className =
                'btn btn-primary btn-sm w-50 rounded-3 shadow-sm border-0';
        }

        function editSubActivity(
            id,
            name,
            projectId,
            activityId,
            status
        ) {

            document.getElementById('modalTitle').innerText =
                'Edit Sub Activity';

            document.getElementById('submitText').innerText =
                'Update';

            document.getElementById('subActivityForm').action =
                `/sub-activities/${id}`;

            document.getElementById('formMethod').value =
                'PUT';

            document.getElementById('subActivityName').value =
                name;

            document.getElementById('subActivityProject').value =
                projectId;

            filterActivities();

            document.getElementById('subActivityActivity').value =
                activityId;

            document.getElementById('subActivityStatus').value =
                status;

            document.querySelector(
                    '#subActivityModal .modal-header-custom'
                ).className =
                'modal-header-custom bg-warning bg-gradient text-dark px-4 py-2';

            document.getElementById('submitButton').className =
                'btn btn-warning btn-sm w-50 rounded-3 shadow-sm border-0 text-dark';
        }

        function filterActivities() {

            let projectId =
                document.getElementById('subActivityProject').value;

            let activityDropdown =
                document.getElementById('subActivityActivity');

            let options =
                activityDropdown.querySelectorAll('option');

            activityDropdown.value = '';

            options.forEach(option => {

                if (option.value === '') {

                    option.style.display = 'block';

                    return;
                }

                if (option.dataset.project === projectId) {

                    option.style.display = 'block';

                } else {

                    option.style.display = 'none';
                }
            });
        }
    </script>
@endpush
