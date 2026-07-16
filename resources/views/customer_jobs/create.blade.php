@extends('layouts.app')

@section('title', 'Create Job')

@section('content')

    <div class="container">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white">
                <h4>Create Job</h4>
            </div>

            <div class="card-body">

                <form action="{{ route('customer-jobs.store') }}" method="POST">

                    @csrf

                    @include('customer_jobs.form')

                    <a href="{{ route('customer-jobs.index') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left"></i>
                    </a>

                    <button class="btn btn-primary">
                        <i class="fa fa-save"></i> Save
                    </button>
                </form>

            </div>

        </div>

    </div>

@endsection
