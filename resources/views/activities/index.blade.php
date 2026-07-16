@extends('layouts.app')

@section('title', 'Activities List | Unibs Tools')

@section('content')

    <div class="container-fluid py-2">

        {{-- Flash --}}
        @include('partials.message')

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-2">

            <div>

                <h4 class="fw-bold mb-0">
                    <i class="fa-solid fa-paintbrush text-primary me-2"></i>
                    Activities
                </h4>

                <small class="text-muted">
                    Total Activities: {{ $activities->total() }}
                </small>

            </div>

            {{-- Add --}}
            <button class="btn btn-primary btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#activityModal"
                onclick="createActivity()">

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
                                ACTIVITY
                            </th>

                            <th class="small fw-semibold text-secondary">
                                PROJECT
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

                        @forelse($activities as $activity)
                            <tr class="border-top">

                                {{-- Activity --}}
                                <td class="px-3 py-2">

                                    <div class="d-flex align-items-center">

                                        <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center me-2"
                                            style="width:30px;height:30px;">

                                            <i class="fa-solid fa-paintbrush"></i>

                                        </div>

                                        <div>

                                            <div class="fw-semibold text-dark">
                                                {{ $activity->name }}
                                            </div>
                                        </div>

                                    </div>

                                </td>

                                {{-- Project --}}
                                <td>

                                    <span class="small text-muted">
                                        {{ $activity->project->name }}
                                    </span>

                                </td>

                                {{-- Status --}}
                                <td class="text-center">

                                    @if ($activity->status === 'active')
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
                                            data-bs-target="#activityModal"
                                            onclick="editActivity(
                                            '{{ $activity->id }}', 
                                            '{{ $activity->name }}',
                                            '{{ $activity->project_id }}',
                                            '{{ $activity->status }}'
                                        )">

                                            <i class="fa-solid fa-pen text-warning"></i>

                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4" class="text-center">

                                    <i class="fa-solid fa-folder-open text-secondary fs-2 mb-2"></i>

                                    <div class="fw-semibold">
                                        No Activities Found
                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
            @if ($activities->hasPages())
                <div class="card-footer bg-white border-0 py-2 px-3">

                    {{ $activities->links() }}

                </div>
            @endif

        </div>

    </div>

    {{-- Activity Modal --}}
    <div class="modal fade" id="activityModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered modal-sm">

            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

                <form method="POST" id="activityForm">

                    @csrf

                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    {{-- Header --}}
                    <div class="modal-header-custom bg-primary bg-gradient text-white px-4 py-2">

                        <div class="d-flex justify-content-between align-items-center">

                            <h6 class="mb-0 fw-bold" id="modalTitle">
                                Add Activity
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
                                Activity Name
                            </label>

                            <div class="input-group input-group-sm">

                                <span class="input-group-text bg-light border-end-0">

                                    <i class="fa fa-paintbrush text-primary"></i>

                                </span>

                                <input type="text" name="name" id="activityName" value="{{ old('name') }}"
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

                                <select name="project_id" id="activityProject"
                                    class="form-select border-start-0 shadow-none @error('project_id') is-invalid @enderror">

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

                        {{-- Status --}}
                        <div>

                            <label class="form-label small fw-semibold text-secondary mb-1">
                                Status
                            </label>

                            <div class="input-group input-group-sm">

                                <span class="input-group-text bg-light border-end-0">

                                    <i class="fa fa-toggle-on text-success"></i>

                                </span>

                                <select name="status" id="activityStatus"
                                    class="form-select border-start-0 shadow-none @error('status') is-invalid @enderror"
                                    required>

                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>

                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>

                                </select>

                                @error('status')
                                    <span class="text-danger small mt-1 d-block">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

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
                new bootstrap.Modal($('#activityModal')[0]).show();
            });
        </script>
    @endif

    <script>
        // CREATE
        function createActivity() {

            document.getElementById('modalTitle').innerText = 'Add Activity';

            document.getElementById('submitText').innerText = 'Create';

            document.getElementById('activityForm').action =
                "{{ route('activities.store') }}";

            document.getElementById('formMethod').value = 'POST';

            document.getElementById('activityName').value = '';

            document.getElementById('activityProject').value = '';

            document.getElementById('activityStatus').value = 'active';

            // Header Color
            document.querySelector('#activityModal .modal-header-custom').className =
                'modal-header-custom bg-primary bg-gradient text-white px-4 py-2';

            // Button Color
            document.getElementById('submitButton').className =
                'btn btn-primary btn-sm w-50 rounded-3 shadow-sm border-0';
        }

        // EDIT
        function editActivity(id, name, projectId, status) {

            document.getElementById('modalTitle').innerText = 'Edit Activity';

            document.getElementById('submitText').innerText = 'Update';

            document.getElementById('activityForm').action =
                `/activities/${id}`;

            document.getElementById('formMethod').value = 'PUT';

            document.getElementById('activityName').value = name;

            document.getElementById('activityProject').value = projectId;

            document.getElementById('activityStatus').value = status;

            // Header Color
            document.querySelector('#activityModal .modal-header-custom').className =
                'modal-header-custom bg-warning bg-gradient text-dark px-4 py-2';

            // Button Color
            document.getElementById('submitButton').className =
                'btn btn-warning btn-sm w-50 rounded-3 shadow-sm border-0 text-dark';
        }
    </script>
@endpush
