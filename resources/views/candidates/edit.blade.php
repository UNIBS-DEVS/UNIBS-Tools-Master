@extends('layouts.app')

@section('title', 'Edit Candidate')

@section('content')

    <div class="container">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white">
                <h4>Edit Candidate - Job Mapping</h4>
            </div>

            <div class="card-body">

                <form action="{{ route('candidates.update', $candidate->id) }}" method="POST">

                    @csrf
                    @method('PUT')


                    <input type="hidden" name="page" value="{{ request('page') }}">
                    
                    @include('candidates.form')


                    {{-- Submit --}}
                    <div class="col-12 text-end pt-3">

                        <button type="submit" class="btn btn-warning px-3 rounded-pill shadow-sm">
                            <i class="bi bi-check-circle"></i>
                            Update
                        </button>


                        <a href="{{ route('candidates.index', [
                            'page' => request('page'),
                        ]) }}"
                            class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection
