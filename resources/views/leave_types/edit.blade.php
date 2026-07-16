@extends('layouts.app')

@section('title', 'Edit Leave Type | Unibs Tools')

@section('content')

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-xl-8">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-pen-to-square me-2 text-warning"></i>
                        Edit Leave Type
                    </h5>
                </div>

                <div class="card-body">

                    <form action="{{ route('leave-types.update', $leaveType->id) }}"
                        method="POST">

                        @csrf
                        @method('PUT')

                        @include('leave_types.form')

                        <div class="d-flex justify-content-end mt-4 gap-2">

                            <a href="{{ route('leave-types.index') }}"
                                class="btn btn-light">
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