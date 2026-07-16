@extends('layouts.app')

@section('title', 'Edit User | Unibs Tools')

@section('content')

    <div class="container mt-4">

        <div class="row justify-content-center">
            <div class="col-xl-12">

                <div class="card shadow-sm border-0">

                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-user-edit me-2 text-primary"></i>
                            Edit User
                        </h5>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            @include('users.form')

                            <div class="d-flex justify-content-end mt-4 gap-2">
                                <a href="{{ route('users.index') }}" class="btn btn-light">
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
