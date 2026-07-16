@extends('layouts.app')

@section('title', 'Edit Comp Off Request')

@section('content')

    <div class="container mt-4">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-semibold text-primary">
                    Edit Comp Off Request
                </h5>

            </div>

            <div class="card-body">

                <form action="{{ route('compoff.update', $compOff->id) }}" method="POST">

                    @csrf
                    @method('PUT')

                    @include('compoff.form')

                </form>

            </div>

        </div>

    </div>

@endsection
