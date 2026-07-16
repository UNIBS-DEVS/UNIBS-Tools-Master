@extends('layouts.app')

@section('title', 'Edit Advance Request')

@section('content')

    <div class="container">

        <div class="card shadow-sm border-0 rounded-4">

            <div class="card-header bg-white border-0 py-3 px-4">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h4 class="mb-0 fw-bold">
                            Edit Advance Request
                        </h4>
                        <small class="text-muted">
                            Update your advance request details
                        </small>
                    </div>

                    <a href="{{ route('advances.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                        <i class="fa fa-arrow-left"></i>
                    </a>

                </div>

            </div>

            <div class="card-body px-4 py-2">

                <form action="{{ route('advances.update', $advance->id) }}" method="POST">

                    @csrf
                    @method('PUT')

                    @include('advances.form')

                </form>

            </div>

        </div>

    </div>

@endsection
