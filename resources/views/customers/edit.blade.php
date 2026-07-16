@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')

    <div class="container">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h4>Edit Customer</h4>
            </div>

            <div class="card-body">

                <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('customers.form')

                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left"></i>
                    </a>

                    <button class="btn btn-warning">
                        <i class="fa fa-pencil"></i> Update
                    </button>
                </form>

            </div>
        </div>
    </div>

@endsection
