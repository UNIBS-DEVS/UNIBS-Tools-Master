@extends('layouts.app')

@section('title', 'Dashboard | Unibs Tools')

@section('content')

    <div class="container my-5">
        <div class="row g-4">

            @if (auth()->user()->hasRole(['admin', 'manager', 'employee', 'accounts', 'hr']))
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h4 class="card-title">Timesheets</h4>
                            <p class="card-text">
                                Submit and manage weekly timesheets (Mon–Sun), multiple tasks per day, decimal hours.
                            </p>
                            {{-- <a href="{{ route('timesheets.index') }}" class="btn btn-primary">
                                Go to Timesheets
                            </a> --}}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
