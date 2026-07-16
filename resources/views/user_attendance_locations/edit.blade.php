@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            Edit Assignment - {{ $location->location_name }}
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('user-attendance-locations.update', $userAttendanceLocation->id) }}">

                @csrf
                @method('PUT')

                @include('user_attendance_locations._form')

                <button class="btn btn-primary">
                    Update
                </button>

            </form>

        </div>
    </div>
@endsection
