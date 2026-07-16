@extends('layouts.app')

@section('title', 'Create Day Shift Schedule | Unibs Tools')

@section('content')

    <div class="container mt-4">
        <div class="row justify-content-center">

            <div class="col-xl-12">

                <div class="card shadow-sm border-0">

                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-calendar-plus me-2 text-primary"></i>
                            Create Day Shift Schedule for {{ $shift->shift_schedule }}
                        </h5>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('day-shift-schedule.store') }}" method="POST">

                            @csrf

                            @include('day_shift_schedules.form')

                            <div class="d-flex justify-content-end mt-4 gap-2">

                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-1"></i>
                                    Create
                                </button>

                                <a href="{{ route('day-shift-schedule.index', ['shift_schedule_id' => $shift->id]) }}"
                                    class="btn btn-outline-dark">
                                    <i class="fa fa-arrow-left"></i>
                                </a>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>
    </div>

@endsection
