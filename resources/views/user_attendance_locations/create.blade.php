@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            Add Assignment - {{ $location->location_name }}
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('user-attendance-locations.store') }}">

                @csrf

                @include('user_attendance_locations._form')

                <button class="btn btn-success">
                    Save
                </button>

            </form>

        </div>
    </div>
@endsection
