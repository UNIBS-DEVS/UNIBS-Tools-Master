@extends('layouts.app')

@section('title', 'Create Candidate')

@section('content')

    <div class="container">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white">
                <h4>Candidate - Job Mapping</h4>
            </div>

            <div class="card-body">

                <form action="{{ route('candidates.store') }}" method="POST">

                    @csrf

                    @include('candidates.form')

                    {{-- Submit --}}
                    <div class="col-12 text-end pt-3">

                        <button type="submit" class="btn btn-primary px-3 rounded-pill shadow-sm">
                            <i class="bi bi-check-circle"></i>
                            Save
                        </button>

                        <a href="{{ route('candidates.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </div>
                </form>

            </div>

        </div>

    </div>

@endsection
