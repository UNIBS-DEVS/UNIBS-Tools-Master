@extends('layouts.app')

@section('title', 'Edit Tender')

@section('content')

<div class="container">

    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-header bg-white border-0 py-2 px-4">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h4 class="mb-0 fw-bold">
                        Edit Tender
                    </h4>

                    <small class="text-muted">
                        Update tender information
                    </small>

                </div>

                <a href="{{ route('tenders.index') }}"
                    class="btn btn-outline-secondary rounded-pill px-3">

                    <i class="fa fa-arrow-left"></i>

                </a>

            </div>

        </div>

        <div class="card-body px-4 py-2">

            <form action="{{ route('tenders.update', $tender->id) }}"
                method="POST">

                @csrf
                @method('PUT')

                @include('tenders.form')

                <div class="text-end pt-3">

                    <button type="submit"
                        class="btn btn-warning rounded-pill px-4 shadow-sm">

                        <i class="fa fa-save me-1"></i>
                        Update

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection