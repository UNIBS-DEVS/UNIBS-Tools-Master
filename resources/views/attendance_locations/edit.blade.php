@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Edit Location</div>

            <div class="card-body">
                <form method="POST" action="{{ route('attendance-locations.update', $attendanceLocation->id) }}">
                    @csrf
                    @method('PUT')

                    @include('attendance_locations._form')

                    <br>
                    <button class="btn btn-warning">
                        Update
                    </button>

                    <a href="{{ route('attendance-locations.index') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection
