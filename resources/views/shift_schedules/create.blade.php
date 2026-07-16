@extends('layouts.app')

@section('title', 'Create Shift Schedule | Unibs Tools')

@section('content')

    @include('partials.message')

    <div class="container mt-4">

        <div class="row justify-content-center">
            <div class="col-xl-5">

                <div class="card shadow-sm border-0">

                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-calendar-plus me-2 text-primary"></i>
                            Create Shift Schedule
                        </h5>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('shift-schedule.store') }}" method="POST">
                            @csrf

                            @include('shift_schedules.form')

                            <div class="d-flex justify-content-end mt-4 gap-2">

                                <a href="{{ route('shift-schedule.index') }}" class="btn btn-light">
                                    <i class="fa fa-arrow-left"></i>
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-1"></i>
                                    Create
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>

    </div>

@endsection
