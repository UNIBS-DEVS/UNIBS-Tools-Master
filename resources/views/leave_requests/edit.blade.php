@extends('layouts.app')

@section('title', 'Edit Leave')

@section('content')

    <div class="container">

        <div class="card">

            <div class="card-header">
                <h4>Edit Leave Application</h4>
            </div>

            <div class="card-body">

                <form action="{{ route('leave-requests.update', $leaveRequest->id) }}" method="POST">

                    @csrf
                    @method('PUT')

                    @include('leave_requests.form')

                    <button type="submit" class="btn btn-primary">
                        Update
                    </button>

                    <a href="{{ route('leave-requests.index') }}" class="btn btn-secondary">
                        Back
                    </a>

                </form>

            </div>

        </div>

    </div>

@endsection
