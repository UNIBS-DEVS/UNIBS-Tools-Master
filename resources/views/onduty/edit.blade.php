@extends('layouts.app')

@section('title', 'Edit On Duty Request')

@section('content')

    <div class="container mt-4">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-semibold text-primary">
                    Edit On Duty Request
                </h5>

            </div>

            <div class="card-body">

                <form action="{{ route('onduty.update', $onduty->id) }}" method="POST">

                    @csrf
                    @method('PUT')

                    @include('onduty.form')

                </form>

            </div>

        </div>

    </div>

@endsection
