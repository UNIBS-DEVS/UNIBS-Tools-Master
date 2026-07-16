@extends('layouts.app')

@section('title', 'Edit Day Shift Schedule | Unibs Tools')

@section('content')

    <div class="container mt-4">

        <div class="row justify-content-center">

            <div class="col-xl-12">

                <div class="card shadow-sm border-0">

                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-calendar-plus me-2 text-primary"></i>
                            Edit Day Shift Schedule - {{ $dayshift->shiftSchedule->shift_schedule }}
                        </h5>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('day-shift-schedule.update', $dayshift->id) }}" method="POST">

                            @csrf
                            @method('PUT')

                            @include('day_shift_schedules.form')

                            <div class="d-flex justify-content-end mt-4 gap-2">

                                <a href="{{ route('day-shift-schedule.index') }}" class="btn btn-outline-dark">
                                    <i class="fa fa-arrow-left"></i>
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-1"></i>
                                    Update
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
