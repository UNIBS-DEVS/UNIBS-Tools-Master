@extends('layouts.app')

@section('title', 'Create Tender')

@section('content')

<div class="container">

    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-header bg-white border-0 py-3 px-4">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h4 class="mb-0 fw-bold">
                        Create Tender
                    </h4>

                    <small class="text-muted">
                        Add new tender information
                    </small>
                </div>

                <a href="{{ route('tenders.index') }}"
                    class="btn btn-outline-secondary rounded-pill px-3">

                    <i class="fa fa-arrow-left me-1"></i>
                    Back

                </a>

            </div>

        </div>

        <div class="card-body p-4">

            <form action="{{ route('tenders.store') }}" method="POST">

                @csrf

                @include('tenders.form')

                <div class="text-end pt-3">

                    <button type="submit"
                        class="btn btn-primary rounded-pill px-4 shadow-sm">

                        <i class="fa fa-save me-1"></i>
                        Save

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection
    