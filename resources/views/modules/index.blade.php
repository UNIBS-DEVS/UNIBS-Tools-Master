@extends('layouts.app')

@section('title', 'Module Management')

@section('content')
    <div class="container-fluid">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card-header d-flex justify-content-between align-items-center mb-3">

            <h4 class="mb-0">
                <i class="fa fa-cubes"></i>
                Module Management
                @if (isset($application))
                    - {{ $application->appName }}
                @elseif(isset($module) && $module->application)
                    - {{ $module->application->appName }}
                @endif
            </h4>

            <div>
                @php
                    $appId = isset($application) ? $application->id : $module->application->id ?? null;
                @endphp

                <a href="{{ route('applications.index', ['app_id' => $appId]) }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                </a>

                <a href="{{ route('modules.create', ['app_id' => $appId]) }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Add Module
                </a>
            </div>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            {{-- <th>Application Code</th>
                                <th>Application Name</th> --}}
                            {{-- <th>Module Code</th> --}}
                            <th>Module Name</th>
                            {{-- <th>Status</th> --}}
                            {{-- <th>Status Message</th> --}}
                            <th width="170">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($modules as $module)
                            <tr>

                                <td>{{ $loop->iteration }}</td>

                                {{-- <td>
                                        {{ $module->application->appCode ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $module->application->appName ?? '-' }}
                                    </td> --}}

                                {{-- <td>
                                        {{ $module->moduleCode }}
                                    </td> --}}

                                <td>
                                    {{ $module->name }}
                                </td>

                                {{-- <td>
                                        @if ($module->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td> --}}

                                {{-- <td>
                                        {{ $module->status_message }}
                                    </td> --}}

                                <td>

                                    <a href="{{ route('modules.edit', $module->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <form action="{{ route('modules.destroy', $module->id) }}" method="POST"
                                        class="d-inline">

                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                            <i class="fa fa-trash"></i>
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="text-center">
                                    No Modules Found.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">
                {{ $modules->withQueryString()->links() }}
            </div>

        </div>

    </div>

    </div>
@endsection
