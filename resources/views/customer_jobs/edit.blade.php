@extends('layouts.app')

@section('title', 'Edit Position')

@section('content')

    <div class="container">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white">
                <h4>Edit Job</h4>
            </div>

            <div class="card-body">

                <form action="{{ route('customer-jobs.update', $customerJob->id) }}" method="POST">

                    @csrf
                    @method('PUT')

                    @include('customer_jobs.form')

                    <a href="{{ route('customer-jobs.index') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left"></i>
                    </a>

                    <button class="btn btn-warning">
                        <i class="fa fa-save"></i> Update
                    </button>
                </form>

            </div>

        </div>

    </div>

@endsection
