@extends('layouts.app')

@section('title', 'Projects List | Unibs Tools')

@section('content')

    <div class="container-fluid py-2">

        {{-- Flash --}}
        @include('partials.message')

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-2">

            <div>
                <h4 class="fw-bold mb-0">
                    <i class="fa-solid fa-diagram-project text-primary me-2"></i>
                    Projects
                </h4>

                <small class="text-muted">
                    Total Projects: {{ $projects->total() }}
                </small>
            </div>

            {{-- Add Button --}}
            <button class="btn btn-primary btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#projectModal"
                onclick="createProject()">

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

                        @forelse ($projects as $project)
                            <tr class="border-top">

                                {{-- Name --}}
                                <td class="px-3 py-2">

                                    <div class="d-flex align-items-center">

                                        <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center me-2"
                                            style="width:30px;height:30px;">

                                            <i class="fa-solid fa-folder"></i>

                                        </div>

                                        <div>
                                            <div class="fw-semibold text-dark">
                                                {{ $project->name }}
                                            </div>
                                        </div>

                                    </div>

                                </td>

                                {{-- Status --}}
                                <td class="text-center">

                                    @if ($project->status === 'active')
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
                                            data-bs-target="#projectModal"
                                            onclick="editProject(
                                            '{{ $project->id }}',
                                            '{{ $project->name }}',
                                            '{{ $project->status }}'
                                        )">

                                            <i class="fa-solid fa-pen text-warning"></i>

                                        </button>


                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3" class="text-center">

                                    <i class="fa-solid fa-folder-open text-secondary fs-2 mb-2"></i>

                                    <div class="fw-semibold">
                                        No Projects Found
                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
            @if ($projects->hasPages())
                <div class="card-footer bg-white border-0 py-2 px-3">
                    {{ $projects->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- Compact Modal --}}
    <div class="modal fade" id="projectModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered modal-sm">

            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

                <form method="POST" id="projectForm">

                    @csrf

                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    {{-- Top --}}
                    <div class="modal-header-custom bg-primary bg-gradient text-white px-4 py-2">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <h6 class="mb-0 fw-bold" id="modalTitle">
                                    Add Project
                                </h6>
                            </div>

                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal">
                            </button>

                        </div>

                    </div>

                    {{-- Body --}}
                    <div class="modal-body p-4">

                        {{-- Name --}}
                        <div class="mb-3">

                            <label class="form-label small fw-semibold text-secondary mb-1">
                                Project Name
                            </label>

                            <div class="input-group input-group-sm">

                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fa fa-folder text-primary"></i>
                                </span>

                                <input type="text" name="name" id="projectName" value="{{ old('name') }}"
                                    class="form-control border-start-0 shadow-none @error('name') is-invalid @enderror"
                                    placeholder="Enter project name">
                            </div>
                            @error('name')
                                <span class="text-danger small mt-1">
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

                                <select name="status" id="projectStatus"
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
                new bootstrap.Modal($('#projectModal')[0]).show();
            });
        </script>
    @endif

    <script>
        function createProject() {

            document.getElementById('modalTitle').innerText = 'Add Project';

            document.getElementById('submitText').innerText = 'Create';

            document.getElementById('projectForm').action =
                "{{ route('projects.store') }}";

            document.getElementById('formMethod').value = 'POST';

            document.getElementById('projectName').value = '';

            document.getElementById('projectStatus').value = 'active';

            // Header
            document.querySelector('#projectModal .modal-header-custom').className =
                'modal-header-custom bg-primary bg-gradient text-white px-4 py-2';

            // Button
            document.getElementById('submitButton').className =
                'btn btn-primary btn-sm w-50 rounded-3 shadow-sm border-0';
        }

        function editProject(id, name, status) {

            document.getElementById('modalTitle').innerText = 'Edit Project';

            document.getElementById('submitText').innerText = 'Update';

            document.getElementById('projectForm').action =
                `/projects/${id}`;

            document.getElementById('formMethod').value = 'PUT';

            document.getElementById('projectName').value = name;

            document.getElementById('projectStatus').value = status;

            // Header
            document.querySelector('#projectModal .modal-header-custom').className =
                'modal-header-custom bg-warning bg-gradient text-dark px-4 py-2';

            // Button
            document.getElementById('submitButton').className =
                'btn btn-warning btn-sm w-50 rounded-3 shadow-sm border-0 text-dark';
        }
    </script>
@endpush
