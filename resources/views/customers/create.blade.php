@extends('layouts.app')

@section('title', 'Create Customer')

@section('content')

    <div class="container">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h4>Create Customer</h4>
            </div>

            <div class="card-body">

                <form action="{{ route('customers.store') }}" method="POST">
                    @csrf

                    @include('customers.form')

                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
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
