@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Attendance Locations</h4>

                <a href="{{ route('attendance-locations.create') }}" class="btn btn-primary">
                    Add Location
                </a>
            </div>

            <div class="card-body">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Radius</th>
                            <th>Shift Schedule</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($locations as $location)
                            <tr>
                                <td>{{ $location->location_name }}</td>
                                <td>{{ ucfirst($location->type) }}</td>
                                <td>{{ $location->radius }} Meter</td>
                                <td>{{ $location->shiftSchedule?->shift_schedule }}</td>
                                <td>
                                    {!! $location->is_active
                                        ? '<span class="badge bg-success">Active</span>'
                                        : '<span class="badge bg-danger">Inactive</span>' !!}
                                </td>

                                <td>
                                    <a href="{{ route('user-attendance-locations.index', [
                                        'attendance_location_id' => $location->id,
                                    ]) }}"
                                        class="btn btn-info btn-sm" title="Assign Users">

                                        <i class="fa fa-users"></i>
                                    </a>


                                    <a href="{{ route('attendance-locations.edit', $location->id) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <form action="{{ route('attendance-locations.destroy', $location->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

                {{ $locations->links() }}

            </div>
        </div>

    </div>
@endsection
