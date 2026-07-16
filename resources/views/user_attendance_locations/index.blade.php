@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">
        {{-- <div class="card-header d-flex justify-content-between">

            <h5>
                User Attendance Locations -
                {{ $location?->location_name ?? 'All Locations' }}
            </h5>

            <a href="{{ route('user-attendance-locations.create', [
                'attendance_location_id' => $locationId,
            ]) }}"
                class="btn btn-primary">

                Add Assignment
            </a>

        </div> --}}


        <div class="card-header d-flex justify-content-between align-items-center">

            <div class="d-flex align-items-center gap-2">

                <a href="{{ route('attendance-locations.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left"></i> Back
                </a>

                <h5 class="mb-0">
                    User Attendance Locations -
                    {{ $location?->location_name ?? 'All Locations' }}
                </h5>

            </div>

            <a href="{{ route('user-attendance-locations.create', [
                'attendance_location_id' => $location->id,
            ]) }}"
                class="btn btn-primary">
                <i class="fa fa-plus"></i> Add Assignment
            </a>

        </div>
        <div class="card-body">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($assignments as $row)
                        <tr>
                            <td>{{ $row->user->name }}</td>

                            <td>
                                <span class="badge bg-{{ $row->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($row->status) }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('user-attendance-locations.edit', $row->id) }}"
                                    class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i>
                                </a>

                                <form action="{{ route('user-attendance-locations.destroy', $row->id) }}" method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">

                                        <i class="fa fa-trash"></i>
                                    </button>

                                </form>
                            </td>
                        </tr>
                    @empty

                        <tr>
                            <td colspan="4" class="text-center">
                                No Records Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            {{ $assignments->appends(request()->query())->links() }}

        </div>
    </div>
@endsection
