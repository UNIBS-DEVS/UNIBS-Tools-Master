@extends('layouts.app')

@section('content')
    <div class="container">

        @if (session('success'))
            <div class="alert alert-success shadow-sm border-0">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger shadow-sm border-0">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card: Add Punch Request Form -->
        <div class="card shadow border-0 bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-primary">Add Punch Request</h4>
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary btn-sm">
                    Back to Attendance
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('attendance.punch') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Date -->
                        <div class="col-md-4 mb-3">
                            <label for="attendance_date" class="form-label fw-bold text-dark">Date</label>
                            <input type="date" name="attendance_date" id="attendance_date" class="form-control shadow-sm"
                                max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <!-- Time -->
                        <div class="col-md-4 mb-3">
                            <label for="punch_time" class="form-label fw-bold text-dark">Time</label>
                            <input type="time" name="punch_time" id="punch_time" class="form-control shadow-sm"
                                value="{{ date('H:i') }}" required>
                        </div>

                        <!-- Punch Type -->
                        <div class="col-md-4 mb-3">
                            <label for="punch_type" class="form-label fw-bold text-dark">Punch Type</label>
                            <select name="punch_type" id="punch_type" class="form-select shadow-sm" required>
                                <option value="in">Check In</option>
                                <option value="out">Check Out</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Location -->
                        <div class="col-md-6 mb-3">
                            <label for="attendance_location_id" class="form-label fw-bold text-dark">Select Location</label>
                            <select name="attendance_location_id" id="attendance_location_id" class="form-select shadow-sm"
                                required>
                                <option value="">-- Choose Location --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">
                                        {{ $location->location_name }} ({{ ucfirst($location->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>


                    </div>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm fw-bold">
                            Submit Punch Request
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection