@extends('layouts.app')

@section('title', 'Apply Leave')

@section('content')

    <div class="container">

        <div class="card">

            <div class="card-header">
                <h4>Apply Leave</h4>
            </div>

            <div class="card-body">

                <form action="{{ route('leave-requests.store') }}" method="POST">

                    @csrf

                    @include('leave_requests.form')

                    <button type="submit" class="btn btn-success">
                        Submit
                    </button>

                    <a href="{{ route('leave-requests.index') }}" class="btn btn-secondary">
                        Back
                    </a>

                </form>

            </div>

        </div>

    </div>

@endsection
