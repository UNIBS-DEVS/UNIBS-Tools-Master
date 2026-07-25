@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="card">

            <div class="card-header d-flex justify-content-between">

                <h4>Application Details</h4>

                <a href="{{ route('applications.index') }}" class="btn btn-secondary">
                    Back
                </a>

            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <tr>
                        <th width="200">Application Code</th>
                        <td>{{ $application->appCode }}</td>
                    </tr>

                    <tr>
                        <th>Application Name</th>
                        <td>{{ $application->appName }}</td>
                    </tr>

                    <tr>
                        <th>Status</th>

                        <td>
                            @if ($application->status)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>

                    </tr>

                    <tr>
                        <th>Status Message</th>
                        <td>{{ $application->status_message }}</td>
                    </tr>

                    <tr>
                        <th>Created At</th>
                        <td>{{ $application->created_at }}</td>
                    </tr>

                    <tr>
                        <th>Updated At</th>
                        <td>{{ $application->updated_at }}</td>
                    </tr>

                </table>

            </div>

        </div>

    </div>
@endsection
