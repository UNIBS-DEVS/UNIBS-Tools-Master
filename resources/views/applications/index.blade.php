@extends('layouts.app')

@section('content')
    <div class="container">

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h4>Applications</h4>

                <a href="{{ route('applications.create') }}" class="btn btn-primary">
                    Add Application
                </a>

            </div>

            <div class="card-body">

                <form method="GET" class="row mb-3">

                    <div class="col-md-4">

                        <input type="text" name="search" class="form-control" placeholder="Search..."
                            value="{{ request('search') }}">

                    </div>

                    <div class="col-md-2">

                        <button class="btn btn-primary">
                            Search
                        </button>

                    </div>

                </form>

                <table class="table table-bordered table-striped">

                    <thead>

                        <tr>

                            <th>#</th>
                            <th>Code</th>
                            <th> Application Name</th>
                            <th>Status</th>
                            <th width="220">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($applications as $application)
                            <tr>

                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $application->appCode }}</td>

                                <td>{{ $application->appName }}</td>

                                <td>

                                    @if ($application->status)
                                        <span class="badge bg-success">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            Inactive
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    <a href="{{ route('modules.index', ['app_id' => $application->id]) }}"
                                        class="btn btn-primary btn-sm" title="Modules">
                                        <i class="fa fa-cubes"></i>
                                    </a>

                                    <a href="{{ route('applications.show', $application) }}" class="btn btn-info btn-sm">
                                        View
                                    </a>

                                    <a href="{{ route('applications.edit', $application) }}" class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('applications.destroy', $application) }}" method="POST"
                                        style="display:inline;">

                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="text-center">
                                    No Record Found
                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

                {{ $applications->links() }}

            </div>

        </div>

    </div>
@endsection
